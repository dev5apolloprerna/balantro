<?php

namespace App\Services;

use App\Models\Document;
use App\Models\User;

class SupervisorDocumentsService
{
    protected $supervisor;

    public function __construct(User $supervisor)
    {
        $this->supervisor = $supervisor;
    }

    public function call()
    {
        return Document::with('user')
            ->whereIn('user_id', $this->allClientIds())
            ->distinct()
            ->get();
    }

    protected function allClientIds()
    {
        // 1. supervisor's direct clients
        $directClientIds = $this->supervisor->clients()->pluck('id')->toArray();

        // 2. Clients of supervisor's data entry operators
        $supervisorDeoIds = $this->supervisor->dataEntryOperators()->pluck('id')->toArray();
        $deoClientsIds = User::whereHas('dataEntryOperators', function($query) use ($supervisorDeoIds) {
            $query->whereIn('data_entry_operators.id', $supervisorDeoIds);
        })->pluck('id')->toArray();

        return array_unique(array_merge($directClientIds, $deoClientsIds));
    }
}