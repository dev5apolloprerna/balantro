<?php

namespace App\Http\Controllers;

use App\Jobs\DocumentActivityNotification;
use App\Models\Document;
use App\Services\FilterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File; // for File::size
use Symfony\Component\HttpFoundation\StreamedResponse;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use App\Models\DocumentComment;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;


class DocumentsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }

        switch ($user->role) {
            case \App\Models\User::ROLES['super_admin']:
                $validStatuses = [
                    'uploaded',
                    'processing',
                    'approved',
                    'rejected',
                    'accepted',
                    'data_entry_in_progress',
                    'data_entry_completed',
                    'query_raised',
                    'query_resolved'
                ];
                break;

            case \App\Models\User::ROLES['manager']:
            case \App\Models\User::ROLES['supervisor']:
            case \App\Models\User::ROLES['data_entry_operator']:
                $validStatuses = ['uploaded', 'accepted', 'rejected', 'data_entry_in_progress', 'data_entry_completed', 'query_raised', 'query_resolved', 'approved'];
                break;

            case \App\Models\User::ROLES['client']:
            default:
                $validStatuses = ['uploaded', 'accepted', 'processing', 'approved', 'rejected']; // keep "processing" visible to client
                break;
        }

        // ---------- NEW: define what "processing" means for clients ----------
        $clientPendingStatuses = [
            'accepted',
            'data_entry_in_progress',
            'data_entry_completed',
            'query_raised',
            'query_resolved',
        ];
        // --------------------------------------------------------------------

        $start  = $request->filled('start_date') ? Carbon::parse($request->start_date)->startOfDay()->toDateString() : null;
        $end    = $request->filled('end_date')   ? Carbon::parse($request->end_date)->endOfDay()->toDateString()   : null;
        $q      = trim((string) $request->get('q', ''));
        $cid    = $request->integer('client_id') ?: null;

        $visibleClientIds = match ($user->role) {
            \App\Models\User::ROLES['data_entry_operator'] => $user->clientsAsDataEntryOperator()->pluck('id')->all(),
            \App\Models\User::ROLES['super_admin']         => \App\Models\User::query()->where('role', \App\Models\User::ROLES['client'])->pluck('id')->all(),
            \App\Models\User::ROLES['manager']             => method_exists($user, 'managedClientIds') ? $user->managedClientIds() : [],
            \App\Models\User::ROLES['supervisor']          => method_exists($user, 'supervisedClientIds') ? $user->supervisedClientIds() : [],
            \App\Models\User::ROLES['client']              => [$user->id],
            default                                        => [$user->id],
        };
        if (empty($visibleClientIds) && $user->role === \App\Models\User::ROLES['client']) {
            $visibleClientIds = [$user->id];
        }
        if ($cid && !in_array($cid, $visibleClientIds, true)) {
            $cid = null;
        }

        // Requested status from UI (or null)
        $requestedStatus = $request->filled('status') && $request->status !== 'all'
            ? (in_array($request->status, $validStatuses, true) ? $request->status : null)
            : null;

        // ---------- NEW: translate "processing" for client to a list ----------
        // $statusFilter can be string (single) or array (multi)
        $statusFilter = $requestedStatus;
        if ($requestedStatus === 'processing' && $user->role === \App\Models\User::ROLES['client']) {
            $statusFilter = $clientPendingStatuses; // turns into whereIn later
        }
        // ---------------------------------------------------------------------

        // Subquery for latest file per doc
        $latestFileSub = DB::table('files as f')
            ->select([
                'f.attachable_id',
                'f.path',
                'f.original_name',
                'f.size',
                DB::raw('ROW_NUMBER() OVER (PARTITION BY f.attachable_id ORDER BY f.created_at DESC) AS rn'),
            ])
            ->where('f.attachable_type', Document::class);

        $query = Document::query()
            ->leftJoinSub($latestFileSub, 'df', function ($join) {
                $join->on('df.attachable_id', '=', 'documents.id')
                    ->where('df.rn', '=', 1);
            })
            ->when($q !== '', function ($q2) use ($q) {
                $like = '%' . str_replace(['%', '_'], ['\%', '\_'], $q) . '%';
                $q2->where(function ($w) use ($like) {
                    $w->where('documents.title', 'like', $like)
                        ->orWhere('documents.reference_no', 'like', $like)
                        ->orWhere('documents.notes', 'like', $like)
                        ->orWhere('df.original_name', 'like', $like);
                });
            })
            ->when(!empty($visibleClientIds), fn($q2) => $q2->whereIn('documents.user_id', $visibleClientIds))
            ->when($cid, fn($q2) => $q2->where('documents.user_id', $cid))
            // ---------- CHANGED: apply statusFilter (string OR array) ----------
            ->when($statusFilter, function ($q2) use ($statusFilter) {
                if (is_array($statusFilter)) {
                    $q2->whereIn('documents.status', $statusFilter);   // processing => pending statuses
                } else {
                    $q2->where('documents.status', $statusFilter);
                }
            })
            // -------------------------------------------------------------------
            ->when($start,  fn($q2) => $q2->whereDate('documents.created_at', '>=', $start))
            ->when($end,    fn($q2) => $q2->whereDate('documents.created_at', '<=', $end));

        // Role scoping
        if ($user->role === \App\Models\User::ROLES['super_admin']) {
            // see all
            $query->when($request->integer('client_id'), fn($q2) => $q2->where('documents.user_id', $request->integer('client_id')));
        } elseif ($user->role === \App\Models\User::ROLES['manager']) {
            $query->whereIn('documents.user_id', $user->managedClientIds())
                ->when($request->integer('client_id'), fn($q2) => $q2->where('documents.user_id', $request->integer('client_id')));
        } elseif ($user->role === \App\Models\User::ROLES['supervisor']) {
            $query->whereIn('documents.user_id', $user->supervisedClientIds())
                ->when($request->integer('client_id'), fn($q2) => $q2->where('documents.user_id', $request->integer('client_id')));
        } elseif ($user->role === \App\Models\User::ROLES['data_entry_operator']) {
            $query->whereIn('documents.user_id', $visibleClientIds)
                ->when($request->integer('client_id'), fn($q2) => $q2->where('documents.user_id', $request->integer('client_id')));
        } else { // client
            $query->where('documents.user_id', $user->id);
        }

        $documents = $query->orderByDesc('documents.created_at')
            ->select([
                'documents.*',
                'df.path as file_path',
                'df.original_name as file_name',
                'df.size as file_size',
            ])
            //->toSql();
            ->paginate(20);

        $documents->withPath(route('documents.index'));
        $documents->appends($request->only(['status', 'start_date', 'end_date', 'page']));
        $userId = (int) $user->id;
        $uploaded_count = 0;
        $in_progress_count = 0;
        $completed_count = 0;
        $rejected_count = 0;
        $accepted_count = 0;
        if($user->role == \App\Models\User::ROLES['client']) {
            $rows = DB::select('EXEC dbo.usp_GetClientDocumentSummary ?', [$userId]);
            $row  = $rows[0] ?? (object) [];
            $uploaded_count   = (int) ($row->uploaded_count    ?? 0);
            $in_progress_count = (int) ($row->in_progress_count ?? 0);
            $completed_count  = (int) ($row->completed_count   ?? 0);
            $rejected_count   = (int) ($row->rejected_count    ?? 0);
            $accepted_count   = (int) ($row->accepted_count    ?? 0);
        }
        
        // Status options for filter (unchanged)
        $statuses = $user->role != \App\Models\User::ROLES['client']
            ? [
                'all'        => 'All',
                'uploaded'   => 'Uploaded',
                'accepted'   => 'Accepted',
                'rejected'   => 'Rejected',
                'data_entry_in_progress' => 'Data Entry In Progress',
                'data_entry_completed'   => 'Data Entry Completed',
                'query_raised'           => 'Query Raised',
                'query_resolved'         => 'Query Resolved',
                'approved'               => 'Approved',
            ]
            : [
                'all'        => 'All',
                'uploaded'   => 'Uploaded',
                'accepted'   => 'Accepted',
                'processing' => 'Processing', // now maps to pending list
                'approved'   => 'Approved',
                'rejected'   => 'Rejected'
            ];

        $clients = \App\Models\User::query()
            ->whereIn('id', $visibleClientIds)
            ->orderBy('name')
            ->get(['id', 'name']);
        
        return view('client.documents.index', compact('documents', 'statuses', 'user', 'clients','uploaded_count','in_progress_count','completed_count','rejected_count','accepted_count'));
    }

    public function create()
    {
        return view('documents.partials.upload_form', [
            'document' => new Document()
        ]);
    }

    public function store(Request $request)
    {
        // $request->validate([
        //     'files'   => 'required|array',
        //     // 'files.*' => 'file|max:20480', // 20 MB each
        //     'files.*' => 'required|file|mimes:jpg,jpeg,png,heic,heif,pdf,xls,xlsx|max:30720'
        // ]);

        try {
            $request->validate([
                'files'   => 'required|array',
                // 'files.*' => 'file|max:20480', // 20 MB each
                'files.*' => 'required|file|mimes:jpg,jpeg,png,heic,heif,pdf,xls,xlsx|max:30720'
            ]);
        } catch (ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'message' => $e->validator->errors()->first(),
                    'errors'  => $e->errors(),
                ], 422);
            }
            throw $e;
        }


        $userId = Auth::id();
        if (!$userId) {
            return back()->with('alert', 'Unauthenticated.');
        }

        $savedIds  = [];
        $publicDir = public_path('documents');

        if (!File::isDirectory($publicDir)) {
            File::makeDirectory($publicDir, 0755, true, true);
        }

        //DB::beginTransaction();
        //try {
        foreach ($request->file('files', []) as $uploaded) {
            // ----- capture metadata BEFORE move() -----
            $originalName = $uploaded->getClientOriginalName();
            $ext          = $uploaded->getClientOriginalExtension();
            $size         = $uploaded->getSize(); // may be null/0 on some setups

            // unique filename (microseconds + random)
            $name = 'document_' . now()->format('Ymd_Hisv') . '_' . Str::random(8) . ($ext ? ".{$ext}" : '');

            // Move to /public/documents
            $uploaded->move($publicDir, $name);

            // Fallback: if size missing, stat the DESTINATION file
            if (empty($size)) {
                $size = File::size($publicDir . DIRECTORY_SEPARATOR . $name);
            }

            // Save relative path on documents table
            $doc = new \App\Models\Document();
            $doc->user_id = $userId;
            $doc->status  = 'uploaded';
            $doc->file    = 'documents/' . $name; // relative to public/
            $doc->save();

            // Also insert into polymorphic files table
            DB::table('files')->insert([
                'path'            => $doc->file,                 // e.g. "documents/abc.pdf"
                'original_name'   => $originalName,
                'size'            => $size,
                'attachable_type' => \App\Models\Document::class,
                'attachable_id'   => $doc->id,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);

            $savedIds[] = $doc->id;
            $this->logVersion(
                'create',
                $doc->id,
                ['status' => [null, 'uploaded']],
                [
                    'file'          => $doc->file,
                    'original_name' => $originalName,
                    'size'          => (int) $size,
                ]
            );
            // Activity after commit
            \App\Jobs\DocumentActivityNotification::dispatch($doc->id, 'create', $userId)->afterCommit();
        }

        DB::commit();

        return redirect()
            ->route('documents.index', ['page' => $this->getTargetPage()])
            ->with('notice', __('client.documents.flash.create.success', ['count' => count($savedIds)]));
        // } catch (\Throwable $e) {
        //     DB::rollBack();
        //     Log::error('Document upload failed', ['error' => $e->getMessage()]);
        //     return back()->with('alert', __('client.documents.flash.create.error'));
        // }
    }

    public function edit(Document $document)
    {
        return view('documents.partials.edit_form', compact('document'));
    }

    // public function update(Request $request, Document $document)
    // {
    //     $beforeStatus = $document->status;
    //     $beforeFile   = $document->file; // string path on documents table (you also keep polymorphic row)

    //     if ($document->status !== 'rejected') {
    //         return back()->with('alert', 'Only "Rejected" documents can be edited.');
    //     }
    //     // Handle file replace (if applicable in your UI)
    //     if ($request->hasFile('file')) {
    //         // NOTE: adjust this block if you actually use $document->file relation; here we keep it simple
    //         $uploaded = $request->file('file');
    //         $originalName = $uploaded->getClientOriginalName();
    //         $ext = $uploaded->getClientOriginalExtension();
    //         $name = 'document_' . now()->format('Ymd_Hisv') . '_' . Str::random(8) . ($ext ? ".{$ext}" : '');
    //         $uploaded->move(public_path('documents'), $name);

    //         $document->file = 'documents/' . $name;

    //         // also write to the polymorphic `files` table
    //         DB::table('files')->insert([
    //             'path'            => $document->file,
    //             'original_name'   => $originalName,
    //             'size'            => $uploaded->getSize(),
    //             'attachable_type' => \App\Models\Document::class,
    //             'attachable_id'   => $document->id,
    //             'created_at'      => now(),
    //             'updated_at'      => now(),
    //         ]);
    //     }

    //     // Status (if provided)
    //     if ($request->filled('status')) {
    //         $document->status = $request->string('status');
    //     }

    //     if ($document->isDirty()) {
    //         $document->status = 'uploaded';
    //         $document->save();

    //         // Build object_changes diff
    //         $changes = [];
    //         if ($beforeStatus !== $document->status) {
    //             $changes['status'] = [$beforeStatus, $document->status];
    //         }
    //         if ($beforeFile !== $document->file) {
    //             $changes['file'] = [$beforeFile, $document->file];
    //         }

    //         if ($changes) {
    //             $this->logVersion('update', $document->id, $changes);
    //         }

    //         DocumentActivityNotification::dispatch($document->id, auth()->id(), 'update');

    //         return response()->json([
    //             'success' => true,
    //             'message' => __('client.documents.flash.update.success')
    //         ]);
    //     }

    //     return response()->json([
    //         'success' => false,
    //         'message' => __('client.documents.flash.update.error')
    //     ], 422);
    // }

    public function update(Request $request, Document $document)
    {
        if ($document->status !== 'rejected') {
            return back()->with('alert', 'Only "Rejected" documents can be edited.');
        }

        $request->validate([
            'file' => ['required', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png'],
        ], [
            'file.required' => 'Please attach a file to re-upload.',
        ]);

        $uploaded     = $request->file('file');
        $originalName = $uploaded->getClientOriginalName();
        $ext          = $uploaded->getClientOriginalExtension();
        $mime         = $uploaded->getMimeType();
        $size         = $uploaded->getSize();

        $newName = 'document_' . now()->format('Ymd_Hisv') . '_' . Str::random(8) . ($ext ? ".{$ext}" : '');
        $destDir = public_path('documents');
        if (!is_dir($destDir)) @mkdir($destDir, 0775, true);
        $uploaded->move($destDir, $newName);

        $publicRelPath = 'documents/' . $newName;

        DB::beginTransaction();
        try {
            $beforeStatus = $document->status;
            $beforeFile   = $document->file;

            // remove old physical file if you only keep latest
            if ($beforeFile && is_file(public_path($beforeFile))) {
                @\Illuminate\Support\Facades\File::delete(public_path($beforeFile));
            }

            // update model
            $document->file             = $publicRelPath;
            $document->status           = \App\Models\Document::STATUS_UPLOADED;
            $document->rejection_reason = null;

            // make sure no rogue attributes are present:
            if ($document->offsetExists('_toLogChanges')) {
                $document->offsetUnset('_toLogChanges');
            }

            $document->save();

            // refresh morphOne latest file
            optional($document->file()->first())->delete();
            $document->file()->create([
                'path'          => $publicRelPath,
                'original_name' => $originalName,
                'size'          => $size,
                'mime_type'     => $mime,
            ]);

            // log diffs without touching $document attributes
            $changes = [];
            if ($beforeStatus !== $document->status) $changes['status'] = [$beforeStatus, $document->status];
            if ($beforeFile   !== $document->file)   $changes['file']   = [$beforeFile,   $document->file];

            if ($changes && method_exists($this, 'logVersion')) {
                $this->logVersion('update', $document->id, $changes);
            }

            DB::commit();
            return back()->with('success', 'Document re-uploaded.');
        } catch (\Throwable $e) {
            DB::rollBack();
            if (is_file(public_path($publicRelPath))) {
                @\Illuminate\Support\Facades\File::delete(public_path($publicRelPath));
            }
            report($e);
            return back()->with('alert', 'Update failed: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request, Document $document)
    {
        $userId = (int) Auth::id();

        if (!$userId || $document->user_id != $userId) {
            abort(403);
        }

        if ($document->status !== 'uploaded') {
            return redirect()
                ->route('documents.index', ['page' => (int) $request->get('page', 1)])
                ->with('alert', __('client.documents.flash.delete.error'));
        }

        if ($document->status !== 'uploaded') {
            return back()->with('alert', 'Only documents in "Uploaded" status can be deleted.');
        }

        try {
            if (!empty($document->file) && File::exists(public_path($document->file))) {
                File::delete(public_path($document->file));
            }
            $deletedId   = $document->id;
            $deletedFile = $document->file;

            $docId = $document->id;
            $document->delete();

            $this->logVersion('delete', $deletedId, [], [
                'file' => $deletedFile,
            ]);
            DocumentActivityNotification::dispatch($docId, 'delete', $userId)->afterCommit();


            return redirect()
                ->route('documents.index', ['page' => $this->getTargetPage()])
                ->with('notice', __('client.documents.flash.delete.success'));
        } catch (\Throwable $e) {
            Log::error('Document delete failed', ['error' => $e->getMessage()]);
            return redirect()
                ->route('documents.index', ['page' => (int) $request->get('page', 1)])
                ->with('alert', __('client.documents.flash.delete.error'));
        }
    }

    protected function renderCreateResponse($successCount, $errorMessages)
    {
        if ($successCount > 0) {
            return redirect()
                ->route('documents.index', ['page' => $this->getTargetPage()])
                ->with('notice', __('client.documents.flash.create.success', ['count' => $successCount]))
                ->withErrors($errorMessages);
        }

        return back()
            ->with('alert', __('client.documents.flash.create.error'))
            ->withErrors($errorMessages);
    }

    protected function getTargetPage()
    {
        $lastPage = (int) auth()->user()->documents()->paginate(20)->lastPage();
        $lastPage = max($lastPage, 1); // safety

        $reqPage  = (int) request('page', 1);

        // Clamp: lower bound 1, upper bound $lastPage
        return max(1, min($reqPage, $lastPage));
    }

    public function download($id): BinaryFileResponse
    {
        $doc = Document::findOrFail($id);
        // who is requesting?

        $userId = Auth::id() ?: Auth::guard('client')->id();
        abort_unless($userId, 401, 'Unauthenticated.');

        // basic ownership check (adjust if admins/managers should be allowed)
        // if ((int)$doc->user_id != (int)$userId) {
        //     abort(403, 'Forbidden.');
        // }

        // Build absolute path and keep it inside /public/documents
        $relative = ltrim($doc->file, '/\\');               // e.g. "documents/abc.pdf"
        $full     = public_path($relative);                 // e.g. ".../public/documents/abc.pdf"

        $root = realpath(public_path('documents'));
        $real = realpath($full);

        // if (!$real || !$root || strncmp($real, $root, strlen($root)) !== 0) {
        //     abort(404, 'File not found.');
        // }
        // abort_unless(file_exists($real), 404, 'File not found.');

        // Prefer the original name saved in the polymorphic files table
        $meta = DB::table('files')
            ->where('attachable_type', Document::class)
            ->where('attachable_id', $doc->id)
            ->latest('id')           // or latest('created_at')
            ->first();

        $downloadName = $meta->original_name ?? basename($relative);
        $this->logVersion('download', $doc->id, [], [
            'path'          => $relative,
            'original_name' => $downloadName,
        ]);
        $mime         = File::mimeType($real) ?: 'application/octet-stream';

        // (Optional) log activity like your store() does
        // \App\Jobs\DocumentActivityNotification::dispatch($doc->id, 'download', $userId)->afterCommit();

        return response()->download($real, $downloadName, [
            'Content-Type' => $mime,
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function verify(Request $request)
    {
        $request->validate([
            'document_id' => 'required|exists:documents,id',
            'status'      => 'required|in:accepted,rejected,data_entry_in_progress,data_entry_completed,query_resolved',
            'reason'      => 'required_if:status,rejected|nullable|string',
            'description' => 'required_if:status,query_resolved|nullable|string',
        ]);

        DB::transaction(function () use ($request) {
            /** @var \App\Models\Document $document */
            $document = \App\Models\Document::lockForUpdate()->findOrFail($request->document_id);
            $old_status = $document->status;
            //dd($document);
            // capture "before" values for logging
            $beforeStatus         = (string) $document->status;
            $beforeRejectionCause = (string) $document->getOriginal('rejection_reason');

            // apply changes
            $document->status = $request->status;

            if ($request->status === 'rejected') {
                $document->rejection_reason = (string) $request->reason;
            }

            $document->save();

            // build changes array (before => after)
            $changes = [];
            if ($beforeStatus !== (string) $document->status) {
                $changes['status'] = [$beforeStatus, (string) $document->status];
            }
            if ($request->status === 'rejected') {
                $changes['rejection_reason'] = [$beforeRejectionCause, (string) $document->rejection_reason];
            }

            // version log for the update
            $this->logVersion('update', $document->id, $changes);
            if ($old_status == "data_entry_completed") {
                // if query resolved, create a comment and log it
                if ($request->status === 'query_resolved') {
                    $comment = \App\Models\DocumentComment::create([
                        'document_id'       => $document->id,
                        'comment_type'      => 1, // e.g. TYPE_QUERY_RESOLVED; use a constant if you have one
                        'description'       => (string) $request->description,
                        'commented_by_id'   => auth()->id(),
                        'commented_by_type' => \App\Models\User::class,
                    ]);

                    $this->logVersion('comment', $document->id, [], [
                        'comment_id'   => $comment->id,
                        'comment_type' => $comment->comment_type,
                        'description'  => $comment->description,
                    ]);
                }
            }
        });

        return redirect()->back()->with('notice', 'Document status updated successfully!');
    }

    public function sup_verify(Request $request)
    {
        $request->validate([
            'document_id' => ['required', 'exists:documents,id'],
            //'status'      => ['required', 'in:approved,query_raised'],
            'status'      => ['required', Rule::in(['approved', 'query_raised'])],
            'description' => ['required_if:status,query_raised', 'nullable', 'string'],
        ]);

        $doc = Document::findOrFail($request->document_id);

        if ((string) $doc->status !== 'data_entry_completed') {
            return back()->with('alert', 'Invalid status transitions.');
            // throw ValidationException::withMessages([
            //     'status' => "Supervisor action allowed only when document status is 'data_entry_completed'. Current status: '{$doc->status}'.",
            // ]);
        }
        DB::transaction(function () use ($request, $doc) {
            $doc->status = $request->status; // documents.status exists and defaults to 'uploaded'. :contentReference[oaicite:0]{index=0}
            $doc->save();
            //$this->logVersion('update', $doc->id, ['status' => [$before, $doc->status]]);
            $this->logVersion('update', $doc->id, ['status' => [$doc->status]]);
            if ($request->status === 'query_raised' && filled($request->description)) {
                $comment = DocumentComment::create([
                    'document_id'      => $doc->id,
                    'comment_type'     => 2, // e.g. 2 = Query Raised
                    'description'      => $request->description, // document_comments table has description & commenter fields. :contentReference[oaicite:1]{index=1}
                    'commented_by_type' => \App\Models\User::class,
                    'commented_by_id'  => auth()->id(),
                ]);

                $this->logVersion('comment', $doc->id, [], [
                    'comment_type' => $comment->comment_type,
                    'description'  => $comment->description,
                ]);
            }
        });

        return back()->with('notice', 'Document updated successfully.');
    }

    protected function logVersion(
        string $event,                 // 'create' | 'update' | 'comment' | 'delete' | 'download' ...
        int    $itemId,
        array  $objectChanges = [],    // e.g. ['status' => ['uploaded','accepted']]
        array  $object = []            // optional snapshot payload (e.g. comment body, file meta)
    ): void {
        DB::table('versions')->insert([
            'whodunnit'      => auth()->id() ? (string) auth()->id() : null,
            'created_at'     => now(),
            'item_id'        => $itemId,
            'item_type'      => \App\Models\Document::class,
            'event'          => $event,
            'object'         => $object ? json_encode($object) : null,
            'object_changes' => $objectChanges ? json_encode($objectChanges) : null,
        ]);
    }
}
