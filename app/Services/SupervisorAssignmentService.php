<?php

namespace App\Services;

use App\Jobs\NotifyAssignedUsersJob;
use App\Models\User;

class SupervisorAssignmentService
{
    protected $assigner;
    protected $supervisor;
    protected $params;

    public function __construct(User $assigner, User $supervisor, array $params)
    {
        $this->assigner = $assigner;
        $this->supervisor = $supervisor;
        $this->params = $params;
    }

    public function call()
    {
        // Fetch old ids
        $oldManagerIds = $this->supervisor->managers()->pluck('id')->toArray();

        // Fetch newly added ids
        $newManagerIds = isset($this->params['manager_ids']) 
            ? array_diff($this->params['manager_ids'], $oldManagerIds)
            : [];

        NotifyAssignedUsersJob::dispatch(
            $this->assigner->id,
            $this->supervisor,
            $newManagerIds
        );
    }
}