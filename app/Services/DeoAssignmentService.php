<?php

namespace App\Services;

use App\Jobs\NotifyAssignedUsersJob;
use App\Models\User;

class DeoAssignmentService
{
    protected $assigner;
    protected $dataEntryOperator;
    protected $params;

    public function __construct(User $assigner, User $dataEntryOperator, array $params)
    {
        $this->assigner = $assigner;
        $this->dataEntryOperator = $dataEntryOperator;
        $this->params = $params;
    }

    public function call()
    {
        // Fetch old ids
        $oldManagerIds = $this->dataEntryOperator->managers()->pluck('id')->toArray();
        $oldSupervisorIds = $this->dataEntryOperator->supervisors()->pluck('id')->toArray();

        // Fetch newly added ids
        $newManagerIds = isset($this->params['manager_ids']) 
            ? array_diff($this->params['manager_ids'], $oldManagerIds)
            : [];

        $supervisorIds = $this->params['supervisor_ids'] ?? $this->params['supervisorIds'] ?? [];
        $newSupervisorIds = array_diff($supervisorIds, $oldSupervisorIds);

        NotifyAssignedUsersJob::dispatch(
            $this->assigner->id,
            $this->dataEntryOperator,
            $newManagerIds,
            $newSupervisorIds
        );
    }
}