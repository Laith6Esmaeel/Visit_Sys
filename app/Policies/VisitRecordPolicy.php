<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\VisitRecord;
use Illuminate\Auth\Access\HandlesAuthorization;

class VisitRecordPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:VisitRecord');
    }

    public function view(AuthUser $authUser, VisitRecord $visitRecord): bool
    {
        return $authUser->can('View:VisitRecord');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:VisitRecord');
    }

    public function update(AuthUser $authUser, VisitRecord $visitRecord): bool
    {
        return $authUser->can('Update:VisitRecord');
    }

    public function delete(AuthUser $authUser, VisitRecord $visitRecord): bool
    {
        return $authUser->can('Delete:VisitRecord');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:VisitRecord');
    }

    public function restore(AuthUser $authUser, VisitRecord $visitRecord): bool
    {
        return $authUser->can('Restore:VisitRecord');
    }

    public function forceDelete(AuthUser $authUser, VisitRecord $visitRecord): bool
    {
        return $authUser->can('ForceDelete:VisitRecord');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:VisitRecord');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:VisitRecord');
    }

    public function replicate(AuthUser $authUser, VisitRecord $visitRecord): bool
    {
        return $authUser->can('Replicate:VisitRecord');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:VisitRecord');
    }

}