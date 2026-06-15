<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

/**
 * Simple FilterService that applies basic filters from request to a query.
 * Extend as needed.
 */
class FilterService
{
    // protected Builder $query;
    // protected Request $request;
    protected Builder $query;
    protected array $filters;

    // public function __construct(Builder $query, Request $request)
    // {
    //     $this->query = $query;
    //     $this->request = $request;
    // }
    public function __construct(Builder $query, Request|array $request)
    {
        $this->query   = $query;
        $this->filters = $request instanceof Request ? $request->all() : (array) $request;
    }

    // public function applyFilters(): Builder
    // {
    //     // Example filters: search by name, filter by manager_id, group_id, etc.
    //     if ($q = $this->request->input('q')) {
    //         $this->query->where('name', 'like', "%{$q}%");
    //     }

    //     if ($name = $this->request->input('name')) {
    //         $this->query->where('name', 'like', "%{$name}%");
    //     }

    //     if ($managerId = $this->request->input('manager_id')) {
    //         $this->query->when($managerId, function ($q) use ($managerId) {
    //             $q->whereHas('managers', function ($sub) use ($managerId) {
    //                 // either of these works:
    //                 $sub->where('users.id', $managerId);   // ✅ correct table
    //                 // $sub->whereKey($managerId);         // ✅ also fine
    //             });
    //         });
    //     }

    //     if ($groupId = $this->request->input('group_id')) {
    //         $this->query->whereHas('groups', function ($b) use ($groupId) {
    //             $b->where('groups.id', $groupId);
    //         });
    //     }

    //     return $this->query;
    // }
    public function applyFilters(): Builder
    {
        // examples — adjust to your fields
        if (!empty($this->filters['status']) && $this->filters['status'] !== 'all') {
            $this->query->where('status', $this->filters['status']);
        }

        if (!empty($this->filters['manager_id'])) {
            $managerId = (int) $this->filters['manager_id'];
            $this->query->whereHas('managers', fn($q) => $q->whereKey($managerId));
        }

        if (!empty($this->filters['start_date'])) {
            $this->query->whereDate('created_at', '>=', $this->filters['start_date']);
        }
        if (!empty($this->filters['end_date'])) {
            $this->query->whereDate('created_at', '<=', $this->filters['end_date']);
        }

        return $this->query;
    }
}
