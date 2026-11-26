<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\VisitStatusHistory;
use Illuminate\Auth\Access\HandlesAuthorization;

class VisitStatusHistoryPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:VisitStatusHistory');
    }

    public function view(AuthUser $authUser, VisitStatusHistory $visitStatusHistory): bool
    {
        return $authUser->can('View:VisitStatusHistory');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:VisitStatusHistory');
    }

    public function update(AuthUser $authUser, VisitStatusHistory $visitStatusHistory): bool
    {
        return $authUser->can('Update:VisitStatusHistory');
    }

    public function delete(AuthUser $authUser, VisitStatusHistory $visitStatusHistory): bool
    {
        return $authUser->can('Delete:VisitStatusHistory');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:VisitStatusHistory');
    }

    public function restore(AuthUser $authUser, VisitStatusHistory $visitStatusHistory): bool
    {
        return $authUser->can('Restore:VisitStatusHistory');
    }

    public function forceDelete(AuthUser $authUser, VisitStatusHistory $visitStatusHistory): bool
    {
        return $authUser->can('ForceDelete:VisitStatusHistory');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:VisitStatusHistory');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:VisitStatusHistory');
    }

    public function replicate(AuthUser $authUser, VisitStatusHistory $visitStatusHistory): bool
    {
        return $authUser->can('Replicate:VisitStatusHistory');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:VisitStatusHistory');
    }

}