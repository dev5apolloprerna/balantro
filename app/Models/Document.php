<?php

namespace App\Models;

class Document extends Model
{
    const STATUS_UPLOADED = 'uploaded';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_REJECTED = 'rejected';
    const STATUS_DATA_ENTRY_IN_PROGRESS = 'data_entry_in_progress';
    const STATUS_DATA_ENTRY_COMPLETED = 'data_entry_completed';
    const STATUS_QUERY_RAISED = 'query_raised';
    const STATUS_QUERY_RESOLVED = 'query_resolved';
    const STATUS_APPROVED = 'approved';


    public static $statuses = [
        self::STATUS_UPLOADED,
        self::STATUS_ACCEPTED,
        self::STATUS_REJECTED,
        self::STATUS_DATA_ENTRY_IN_PROGRESS,
        self::STATUS_DATA_ENTRY_COMPLETED,
        self::STATUS_QUERY_RAISED,
        self::STATUS_QUERY_RESOLVED,
        self::STATUS_APPROVED
    ];

    public const ALL = [
        self::STATUS_UPLOADED,
        self::STATUS_ACCEPTED,
        self::STATUS_REJECTED,
        self::STATUS_DATA_ENTRY_IN_PROGRESS,
        self::STATUS_DATA_ENTRY_COMPLETED,
        self::STATUS_QUERY_RAISED,
        self::STATUS_QUERY_RESOLVED,
        self::STATUS_APPROVED,
    ];
    protected $fillable = [
        'user_id',
        'status',
        'rejection_reason',
        'message_id',
        'file'
    ];

    protected $casts = [
        'status' => 'string',
        'toLogChanges' => 'array',
    ];

    protected array $nonPersisted = ['_toLogChanges'];

    public function getDirty()
    {
        $dirty = parent::getDirty();
        foreach ($this->nonPersisted as $k) {
            unset($dirty[$k]);
        }
        return $dirty;
    }

    protected static $statusTransitions = [
        self::STATUS_UPLOADED => [self::STATUS_ACCEPTED, self::STATUS_REJECTED],
        self::STATUS_REJECTED => [self::STATUS_UPLOADED],
        self::STATUS_ACCEPTED => [self::STATUS_DATA_ENTRY_IN_PROGRESS],
        self::STATUS_DATA_ENTRY_IN_PROGRESS => [self::STATUS_DATA_ENTRY_COMPLETED],
        self::STATUS_DATA_ENTRY_COMPLETED => [self::STATUS_APPROVED, self::STATUS_QUERY_RAISED],
        self::STATUS_QUERY_RAISED => [self::STATUS_QUERY_RESOLVED],
        self::STATUS_QUERY_RESOLVED => [self::STATUS_QUERY_RAISED, self::STATUS_APPROVED],
        self::STATUS_APPROVED => []
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function message()
    {
        return $this->belongsTo(Message::class);
    }

    public function documentComments()
    {
        return $this->hasMany(DocumentComment::class);
    }

    public static function availableStatusesFor(User $user)
    {
        // ✅ Use the singular method names that match your User model
        if ($user->client()->exists()) {
            return [self::STATUS_UPLOADED];
        } elseif ($user->dataEntryOperator()->exists()) { // ← Change to singular
            return [
                self::STATUS_ACCEPTED,
                self::STATUS_REJECTED,
                self::STATUS_DATA_ENTRY_IN_PROGRESS,
                self::STATUS_DATA_ENTRY_COMPLETED,
                self::STATUS_QUERY_RESOLVED
            ];
        } elseif ($user->supervisor()->exists()) { // ← Change to singular
            return [self::STATUS_APPROVED, self::STATUS_QUERY_RAISED];
        } else {
            return [];
        }
    }

    public function validStatusTransition($newStatus)
    {
        if ($this->status === $newStatus) {
            return false;
        }

        return in_array($newStatus, self::$statusTransitions[$this->status] ?? []);
    }

    // public function file()
    // {
    //     return $this->morphOne(File::class, 'attachable');
    // }

    public function files()
    {
        return $this->morphMany(\App\Models\File::class, 'attachable');
    }

    public function file() // single latest file
    {
        return $this->morphOne(\App\Models\File::class, 'attachable')->latestOfMany();
    }

    // protected static function booted()
    // {
    //     static::saving(function ($document) {
    //         if ($document->status === self::STATUS_REJECTED && empty($document->rejection_reason)) {
    //             throw new \Exception('Rejection reason is required when status is rejected');
    //         }

    //         // Only require file when status is UPLOADED
    //         if ($document->status === self::STATUS_UPLOADED && !$document->file) {
    //             throw new \Exception('File must be attached when status is uploaded');
    //         }
    //     });
    // }

    protected static function booted()
    {
        static::saving(function ($document) {
            if ($document->status === self::STATUS_REJECTED && empty($document->rejection_reason)) {
                throw new \Exception('Rejection reason is required when status is rejected');
            }

            // Only require a file when status is UPLOADED and there is no related file
            // if ($document->status === self::STATUS_UPLOADED && !$document->file()->exists()) {
            //     throw new \Exception('File must be attached when status is uploaded');
            // }
        });
    }

    public function validateFile()
    {
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'csv', 'txt', 'rtf', 'heic'];

        if ($this->file) {
            $extension = strtolower($this->file->extension);
            if (!in_array($extension, $allowedExtensions)) {
                throw new \Exception('File type not allowed. Allowed types: ' . implode(', ', $allowedExtensions));
            }
        }
    }

    public function client()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}
