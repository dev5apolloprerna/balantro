<?php

namespace App\Services;

use App\Models\Document;
use App\Models\User;

class ManagerDocumentsService
{
    protected $manager;

    public function __construct(User $manager)
    {
        $this->manager = $manager;
    }

    public function call()
    {
        return Document::with('user')
            ->whereIn('user_id', $this->allClientIds())
            ->distinct()
            ->get();
    }

    public function groupedByStatus()
    {
        return $this->call()->groupBy('status')->map->count();
    }

    protected function allClientIds()
    {
        // 1. Manager's direct clients
        $directClientIds = $this->manager->clients()->pluck('id')->toArray();

        // 2. Clients of manager's supervisors
        $supervisorIds = $this->manager->supervisors()->pluck('id')->toArray();
        $supervisorsClientsIds = User::whereHas('supervisors', function ($query) use ($supervisorIds) {
            $query->whereIn('users.id', $supervisorIds);
        })->pluck('id')->toArray();

        // 3. Clients of manager's data entry operators
        $managerDeoIds = $this->manager->dataEntryOperators()->pluck('id')->toArray();
        $deoClientsIds = User::whereHas('dataEntryOperators', function ($query) use ($managerDeoIds) {
            $query->whereIn('users.id', $managerDeoIds);
        })->pluck('id')->toArray();

        // 4. Clients of DEOs under manager's supervisors
        $supervisorDeosIds = User::whereHas('supervisors', function ($query) use ($supervisorIds) {
            $query->whereIn('users.id', $supervisorIds);
        })->pluck('id')->toArray();
        $supervisorDeosClientsIds = User::whereHas('dataEntryOperators', function ($query) use ($supervisorDeosIds) {
            $query->whereIn('users.id', $supervisorDeosIds);
        })->pluck('id')->toArray();

        return array_unique(array_merge(
            $directClientIds,
            $supervisorsClientsIds,
            $deoClientsIds,
            $supervisorDeosClientsIds
        ));
    }
}
