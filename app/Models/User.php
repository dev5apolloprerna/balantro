<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Log;   // ← add this
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Concerns\LogsModelChanges;
use Illuminate\Database\Eloquent\SoftDeletes;

use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, LogsModelChanges;

    protected ?array $permissionCache = null;
    // Define roles
    public const ROLE_CLIENT             = 'client';
    public const ROLE_SUPER_ADMIN        = 'super_admin';
    public const ROLE_MANAGER            = 'manager';
    public const ROLE_SUPERVISOR         = 'supervisor';
    public const ROLE_DATA_ENTRY_OPERATOR = 'data_entry_operator';

    // Optional: Array of roles

    const ROLES = [
        'client' => 0,
        'super_admin' => 1,
        'manager' => 2,
        'supervisor' => 3,
        'data_entry_operator' => 4
    ];

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'type',
        'confirmation_token',      // ✅ add this
        'reset_password_token',     // ✅ add this
        'token',
        'device',
        'origin',
        'short_name'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active'         => 'boolean',
        'role' => 'integer'
    ];

    protected array $activityLogIgnore = [
        'password',
        'remember_token',
        'reset_password_token',
        'confirmation_token'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            $user->setTypeBasedOnRole();
        });

        static::created(function ($user) {
            $user->assignDefaultGroup();
        });
    }

    // Relationships
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function user_profile()
    {
        return $this->hasOne(\App\Models\UserProfile::class, 'user_id', 'id');
    }

    public function documentComments()
    {
        return $this->hasMany(DocumentComment::class, 'commented_by_id');
    }

    public function userPermissions()
    {
        // Specify foreign key 'user_id' explicitly
        return $this->hasMany(UserPermission::class, 'user_id', 'id');
    }

    public function permissions()
    {
        //return $this->belongsToMany(Permission::class, 'user_permissions');
        return $this->belongsToMany(
            Permission::class,   // related model
            'user_permissions',  // pivot table
            'user_id',           // FK on pivot -> users.id
            'permission_id'      // FK on pivot -> permissions.id
        )->withPivot('is_negative'); // <-- important so we can filter
    }

    public function groupUsers()
    {
        return $this->hasMany(GroupUser::class);
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_users', 'user_id', 'group_id');
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function userDevices()
    {
        return $this->hasMany(UserDevice::class);
    }

    public function messages()
    {
        return Message::where('sender_id', $this->id)
            ->orWhere('receiver_id', $this->id);
    }

    // Methods
    public function assignPermission($id, $isNegative, $forceNegative = false)
    {
        $userPermission = $this->userPermissions()->firstOrCreate(['permission_id' => $id]);

        if ($forceNegative) {
            $userPermission->update(['is_negative' => true]);
        } elseif ($userPermission->exists && $isNegative) {
            $userPermission->delete();
        } else {
            $userPermission->update(['is_negative' => false]);
        }
    }

    public function removePermissions($permissionIds)
    {
        if (empty($permissionIds)) {
            return;
        }

        $this->userPermissions()->whereIn('id', $permissionIds)->delete();
    }

    protected function setTypeBasedOnRole()
    {
        $this->type = match ($this->role) {
            self::ROLES['client'] => 'Client',
            self::ROLES['super_admin'] => 'SuperAdmin',
            self::ROLES['manager'] => 'Manager',
            self::ROLES['supervisor'] => 'Supervisor',
            self::ROLES['data_entry_operator'] => 'DataEntryOperator',
            default => null,
        };
    }

    protected function assignDefaultGroup()
    {
        $groupName = match ($this->role) {
            self::ROLES['super_admin'] => 'Administrators',
            self::ROLES['manager'] => 'Managers',
            self::ROLES['supervisor'] => 'Supervisors',
            self::ROLES['data_entry_operator'] => 'Data Entry Operators',
            self::ROLES['client'] => 'Clients',
            default => null,
        };

        if ($groupName) {
            $group = Group::where('name', $groupName)->first();
            if ($group) {
                $this->groups()->attach($group->id);
            }
        }
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function dataEntryOperator()
    {
        return $this->hasOne(DataEntryOperator::class, 'id', 'id');
    }

    public function client()
    {
        return $this->hasOne(Client::class, 'id', 'id');
    }

    public function supervisor()
    {
        //return $this->hasOne(Supervisor::class, 'id', 'id');
        return $this->hasOne(Supervisor::class, 'id', 'id');
    }

    // Helper method to check user type
    public function isDataEntryOperator()
    {
        return $this->dataEntryOperator()->exists();
    }

    public function isClient()
    {
        return $this->client()->exists();
    }

    public function isSupervisor()
    {
        return $this->supervisor()->exists();
    }

    public function dataEntryOperators()
    {
        // Log the backtrace to see where this is being called from
        Log::error('dataEntryOperators() called from:', [
            'trace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5)
        ]);

        // Return the singular relationship to prevent the error
        return $this->dataEntryOperator();
    }

    public function supervisors()
    {
        return $this->belongsToMany(
            User::class,            // related model table = users
            'managers_supervisors', // pivot
            'manager_id',           // this user as manager
            'supervisor_id'         // related user as supervisor
        )->withPivot(['manager_id', 'supervisor_id']);
    }

    public function clientsAsDataEntryOperator()
    {
        return $this->belongsToMany(
            \App\Models\User::class,
            'clients_data_entry_operators',   // ✅ correct table name
            'data_entry_operator_id',         // this (DEO) user id
            'client_id'                       // related client user id
        )->where('users.role', self::ROLES['client']);
    }

    public function clientsAsManager()
    {
        return $this->belongsToMany(
            \App\Models\Client::class,
            'clients_managers',
            'manager_id',
            'client_id'
        );
    }

    public function clientsAsSupervisor()
    {
        return $this->belongsToMany(
            \App\Models\Client::class,
            'clients_supervisors',
            'supervisor_id',
            'client_id'
        );
    }

    public function managers()
    {
        return $this->belongsToMany(
            \App\Models\User::class,
            'clients_managers',               // ✅ correct table name
            'client_id',                      // this (client) user id
            'manager_id'                      // related manager user id
        );
    }

    public function clients()
    {
        return $this->belongsToMany(Client::class, 'clients_managers', 'manager_id', 'client_id');
    }

    public function hasRole($role): bool
    {
        // Accept "super_admin" or 5
        if (is_string($role)) {
            return (int) $this->role === static::ROLES[$role] ?? -1;
        }
        return (int) $this->role === (int) $role;
    }

    // public function isSuperAdmin(): bool
    // {
    //     return (int) $this->role === static::ROLES['super_admin'];
    // }

    public function isSuperAdmin(): bool
    {
        // ✅ Make sure this numeric matches your DB!
        $isNumeric = (int) $this->role === static::ROLES['super_admin']; // 1 in your map

        // Also allow type string variants just in case
        $t = strtolower((string) $this->type);
        $isType = in_array($t, ['super_admin', 'superadmin'], true);

        return $isNumeric || $isType;
    }

    public function isAdmin(): bool
    {
        return (int) $this->role === static::ROLES['admin'];
    }

    public function isManager(): bool
    {
        return (int) $this->role === static::ROLES['manager'];
    }

    /* Optional: nice accessor */
    public function getRoleNameAttribute(): ?string
    {
        $map = array_flip(static::ROLES);
        return $map[(int) $this->role] ?? null;
    }

    public function managedClients()
    {
        return $this->belongsToMany(
            User::class,
            'clients_managers',
            'manager_id',
            'client_id'
        )->where('users.role', self::ROLES['client']);
    }

    public function supervisedClients(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'clients_supervisors',   // <- put the REAL pivot table name here
            'supervisor_id',        // FK on pivot pointing to THIS model (supervisor)
            'client_id'             // FK on pivot pointing to the related model (client)
        )
            ->where('users.role', self::ROLES['client'])
            ->select('users.*');        // prevent ambiguous columns later
    }

    /* --- Helpers for your filter --- */
    public function managedClientIds(): array
    {
        // returns array of client user IDs this manager manages
        return $this->managedClients()->pluck('users.id')->all();
    }

    public function supervisedClientIds(): array
    {
        // returns array of client user IDs this supervisor supervises
        return $this->supervisedClients()->pluck('users.id')->all();
    }

    /* (optional) role helpers */
    public function isDEO(): bool
    {
        return $this->role === self::ROLES['data_entry_operator'];
    }

    public function hasPermission(string $action, string $subject): bool
    {
        if ($this->isSuperAdmin()) return true;

        $key = "{$subject}.{$action}";

        if ($this->permissionCache === null) {
            // group-derived allows (from group_permissions -> permissions)
            $groupKeys = $this->groups()
                ->with('permissions:id,action,subject')
                ->get()
                ->flatMap(fn($g) => $g->permissions)
                ->map(fn($p) => "{$p->subject}.{$p->action}")
                ->unique()
                ->values();

            // explicit user ALLOWs (pivot is_negative = 0)
            $allowKeys = $this->permissions()
                ->wherePivot('is_negative', false)
                ->get(['permissions.action', 'permissions.subject'])
                ->map(fn($p) => "{$p->subject}.{$p->action}")
                ->unique()
                ->values();

            // explicit user DENYs (pivot is_negative = 1)
            $denyKeys = $this->permissions()
                ->wherePivot('is_negative', true)
                ->get(['permissions.action', 'permissions.subject'])
                ->map(fn($p) => "{$p->subject}.{$p->action}")
                ->unique()
                ->values();

            $this->permissionCache = [
                'allow' => collect($allowKeys),
                'deny'  => collect($denyKeys),
                'group' => collect($groupKeys),
            ];
        }

        if ($this->permissionCache['deny']->contains($key))  return false; // explicit deny wins
        if ($this->permissionCache['allow']->contains($key)) return true;  // explicit allow
        return $this->permissionCache['group']->contains($key);            // group allow
    }

    // Add this relationship
    public function devices()
    {
        return $this->hasMany(UserDevice::class);
    }

    // Get active devices
    public function activeDevices()
    {
        return $this->devices()->active();
    }

    // Get mobile devices
    public function mobileDevices()
    {
        return $this->devices()->byType('mobile');
    }

    // Get PC devices
    public function pcDevices()
    {
        return $this->devices()->byType('pc');
    }

    public function preferences()
    {
        return $this->hasOne(UserCardPreference::class);
    }
}
