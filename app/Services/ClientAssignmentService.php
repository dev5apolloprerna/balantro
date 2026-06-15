<?php

namespace App\Services;

use App\Jobs\NotifyAssignedUsersJob;
use App\Models\User;

class ClientAssignmentService
{
    protected $assigner;
    protected $client;
    protected $params;

    public function __construct(User $assigner, User $client, array $params)
    {
        $this->assigner = $assigner;
        $this->client = $client;
        $this->params = $params;
    }

    public function call()
    {
        // Fetch old ids
        $oldManagerIds = $this->client->managers()->pluck('id')->toArray();
        $oldSupervisorIds = $this->client->supervisors()->pluck('id')->toArray();
        $oldDeoIds = $this->client->dataEntryOperators()->pluck('id')->toArray();

        // Fetch newly added ids
        $newManagerIds = isset($this->params['manager_ids']) 
            ? array_diff($this->params['manager_ids'], $oldManagerIds)
            : [];

        $supervisorIds = $this->params['supervisor_ids'] ?? $this->params['supervisorIds'] ?? [];
        $newSupervisorIds = array_diff($supervisorIds, $oldSupervisorIds);

        $deoParamIds = $this->params['data_entry_operator_ids'] ?? $this->params['deo_ids'] ?? $this->params['deoIds'] ?? [];
        $newDeoIds = array_diff($deoParamIds, $oldDeoIds);

        NotifyAssignedUsersJob::dispatch(
            $this->assigner->id,
            $this->client,
            $newManagerIds,
            $newSupervisorIds,
            $newDeoIds
        );
    }
}