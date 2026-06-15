<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Permission;

class Ability
{
    public function initialize(User $user)
    {
        $user = $user ?: new User();

        // Apply permissions in order of priority
        $this->applyGroupPermissions($user);
        $this->applyUserPermissions($user);
    }

    protected function applyGroupPermissions(User $user)
    {
        foreach ($user->groups as $group) {
            foreach ($group->permissions as $permission) {
                $this->grantPermission($permission);
            }
        }
    }

    protected function applyUserPermissions(User $user)
    {
        foreach ($user->userPermissions as $userPermission) {
            $permission = $userPermission->permission;
            $userPermission->is_negative 
                ? $this->denyPermission($permission)
                : $this->grantPermission($permission);
        }
    }

    protected function grantPermission(Permission $permission)
    {
        $subject = $this->resolveSubject($permission->subject);
        $conditions = $this->parseConditions($permission->conditions);

        Gate::define($permission->name, function ($user) use ($subject, $permission, $conditions) {
            return $user->can($permission->action, $subject, $conditions);
        });
    }

    protected function denyPermission(Permission $permission)
    {
        $subject = $this->resolveSubject($permission->subject);
        $conditions = $this->parseConditions($permission->conditions);

        Gate::define($permission->name, function ($user) use ($subject, $permission, $conditions) {
            return $user->cannot($permission->action, $subject, $conditions);
        });
    }

    protected function resolveSubject($subject)
    {
        if (class_exists($subject)) {
            return $subject;
        }

        return $subject; // Could be a string like 'dashboard'
    }

    protected function parseConditions($conditions)
    {
        if (empty($conditions)) {
            return [];
        }

        return json_decode($conditions, true) ?: [];
    }
}