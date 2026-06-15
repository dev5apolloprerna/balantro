<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // <-- add this

class MessagesController extends Controller
{
    public function index(Request $request)
    {
        $selectionMode  = $request->selection_mode === "true";
        $clients        = $this->searchClients($request);
        $selectedClient = $request->client_id ? $clients->find($request->client_id) : null;

        $messages = $selectedClient
            ? Message::messages($selectedClient->id)
            ->with(['sender', 'documents'])
            ->orderBy('created_at')
            ->get()
            : collect();

        if ($request->wantsJson() || $request->is('turbo-stream/*')) {
            if ($this->isMobileDevice($request)) {
                return response()->turboStream(
                    view('supervisors.messages.partials.mobile_chat', [
                        'clients'        => $clients,
                        'selectedClient' => $selectedClient,
                        'messages'       => $messages,
                    ])
                );
            }

            // return response()->turboStream([
            //     view('supervisors.messages.partials.client_list', [
            //         'clients'        => $clients,
            //         'selectedClient' => $selectedClient,
            //     ]),
            //     view('supervisors.messages.partials.chat_content', [
            //         'selectedClient' => $selectedClient,
            //         'messages'       => $messages,
            //     ]),
            // ]);
            if ($this->isMobileDevice($request)) {
                return response()->turboStream(
                    view('supervisors.messages.partials.mobile_chat', [
                        'clients' => $clients,
                        'selectedClient' => $selectedClient,
                        'messages' => $messages
                    ])
                );
            }
        }

        return view('supervisors.messages.index', [
            'selectionMode'  => $selectionMode,
            'clients'        => $clients,
            'selectedClient' => $selectedClient,
            'messages'       => $messages,
        ]);
    }

    public function create(Request $request)
    {
        return $request->selected_clients
            ? $this->sendBulkMessages($request)
            : $this->sendSingleMessage($request);
    }

    protected function sendBulkMessages(Request $request)
    {
        $clientIds = array_filter((array) $request->selected_clients);
        if (empty($clientIds)) {
            return response()->json(['error' => 'No clients selected'], 422);
        }

        foreach ($clientIds as $clientId) {
            $receiver = User::find($clientId);
            if (!$receiver) {
                continue;
            }

            $message = Auth::user()->sentMessages()->create([
                'receiver_id' => $receiver->id,
                'description' => $request->description,
            ]);

            if ($message) {
                $this->attachDocuments($message, $request);
                $message->broadcastToClient([
                    'client_id'             => $clientId,
                    'user_id'               => Auth::id(),
                    'client_html'           => view('messages.partials.message_block_for_client', [
                        'message' => $message,
                        'sender'  => false,
                    ])->render(),
                    'management_team_html'  => view('client.messages.partials.message_block', [
                        'message' => $message,
                        'sender'  => true,
                    ])->render(),
                ]);
            }
        }

        return response()->turboStream([
            view('supervisors.messages.partials.client_list', [
                'clients'       => $this->searchClients($request),
                'selectedClient' => null,
                'selectionMode' => false,
            ]),
            view('supervisors.messages.partials.chat_content', [
                'selectedClient' => null,
                'messages'       => [],
            ]),
        ]);
    }

    protected function sendSingleMessage(Request $request)
    {
        $receiver = User::findOrFail($request->receiver_id);

        $message = Auth::user()->sentMessages()->create([
            'receiver_id' => $receiver->id,
            'description' => $request->description,
        ]);

        if (!$message) {
            return response()->json(['error' => 'Failed to create message'], 422);
        }

        $this->attachDocuments($message, $request);

        $message->broadcastToClient([
            'client_id'            => $receiver->id,
            'user_id'              => Auth::id(),
            'client_html'          => view('messages.partials.message_block_for_client', [
                'message' => $message,
                'sender'  => false,
            ])->render(),
            'management_team_html' => view('client.messages.partials.message_block', [
                'message' => $message,
                'sender'  => true,
            ])->render(),
        ]);

        return response()->turboStream(
            view('supervisors.messages.partials.chat_content', [
                'selectedClient' => null,
                'messages'       => [],
            ])
        );
    }

    protected function attachDocuments(Message $message, Request $request)
    {
        if (!$request->hasFile('attachments')) {
            return;
        }

        foreach ($request->file('attachments') as $attachment) {
            Document::create([
                'user_id'    => Auth::id(),
                'file'       => $attachment, // assumes your Document model handles storage/casting
                'message_id' => $message->id,
            ]);
        }
    }

    /**
     * INTERNAL helper used by both index() and the JSON endpoint.
     * Returns a Builder filtered to clients under a supervisor.
     */
    protected function supervisorClientsQuery(Request $request, ?int $supervisorId = null)
    {
        // Priority: explicit arg -> route param -> query/body -> auth user
        $supervisorId = $supervisorId
            ?? (int) $request->route('supervisor_id')
            ?? (int) $request->route('supervisor')
            ?? (int) $request->input('supervisor_id')
            ?? (int) Auth::id();

        // If you want to hard-fail when missing, throw or return empty set:
        if (!$supervisorId) {
            // Return an empty query to avoid exceptions upstream
            return User::query()->whereRaw('1=0');
        }

        // Adjust these columns/conditions to your schema
        return User::query()
            ->where('role', 'client')
            ->where('supervisor_id', $supervisorId);
    }

    /**
     * JSON endpoint: /supervisors/{supervisor_id}/clients (or with ?supervisor_id=)
     */
    public function supervisorClients(Request $request, ?int $supervisorId = null)
    {
        $query   = $this->supervisorClientsQuery($request, $supervisorId);

        // Optional search filter if provided on the endpoint
        if ($term = $request->input('search')) {
            $term = mb_strtolower($term);
            $query->whereRaw('LOWER(name) LIKE ?', ['%' . $term . '%']);
        }

        //$clients = $query->orderBy('name')->get(['id', 'name', 'email', 'phone']);
        $clients = $query
            ->leftJoin('profiles', 'profiles.user_id', '=', 'users.id')
            ->orderBy('users.name')
            ->get([
                'users.id',
                'users.name',
                'users.email',
                'profiles.mobile_no as phone'
            ]);

        return response()->json(['success' => true, 'data' => $clients]);
    }

    /**
     * Called by index(); returns a Collection (not JSON).
     */
    protected function searchClients(Request $request)
    {
        $query = $this->supervisorClientsQuery($request);

        if ($request->filled('search')) {
            $term = mb_strtolower($request->search);
            $query->whereRaw('LOWER(name) LIKE ?', ['%' . $term . '%']);
        }

        //return $query->orderBy('name')->get(['id', 'name', 'email', 'phone']);
        $clients = $query
            ->leftJoin('profiles', 'profiles.user_id', '=', 'users.id')
            ->orderBy('users.name')
            ->get([
                'users.id',
                'users.name',
                'users.email',
                'profiles.mobile_no as phone'
            ]);
    }

    protected function isMobileDevice(Request $request)
    {
        return (bool) preg_match(
            '/Mobile|Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i',
            (string) $request->userAgent()
        );
    }
}
