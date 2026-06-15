<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class ManagerClientsService
{
    protected $manager;
    protected $currentPage;

    public function __construct(User $manager, int $currentPage = 1)
    {
        $this->manager = $manager;
        $this->currentPage = $currentPage;
    }

    public function call(): LengthAwarePaginator
    {
        $supervisorIds = $this->manager->supervisors()->pluck('id')->toArray();
        $supervisorDeoIds = User::whereHas('supervisors', function($query) use ($supervisorIds) {
            $query->whereIn('supervisors.id', $supervisorIds);
        })->pluck('id')->toArray();

        $dataEntryOperatorIds = array_unique(array_merge(
            $this->manager->dataEntryOperators()->pluck('id')->toArray(),
            $supervisorDeoIds
        ));

        return User::whereHas('managers', function($query) {
                $query->where('managers.id', $this->manager->id);
            })
            ->orWhereHas('supervisors', function($query) use ($supervisorIds) {
                $query->whereIn('supervisors.id', $supervisorIds);
            })
            ->orWhereHas('dataEntryOperators', function($query) use ($dataEntryOperatorIds) {
                $query->whereIn('data_entry_operators.id', $dataEntryOperatorIds);
            })
            ->with(['managers', 'supervisors', 'dataEntryOperators'])
            ->distinct()
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'page', $this->currentPage);
    }
}