<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Directorate;
use Illuminate\Auth\Access\HandlesAuthorization;

class DirectoratePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Directorate');
    }

    public function view(AuthUser $authUser, Directorate $directorate): bool
    {
        return $authUser->can('View:Directorate');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Directorate');
    }

    public function update(AuthUser $authUser, Directorate $directorate): bool
    {
        return $authUser->can('Update:Directorate');
    }

    public function delete(AuthUser $authUser, Directorate $directorate): bool
    {
        return $authUser->can('Delete:Directorate');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Directorate');
    }

    public function restore(AuthUser $authUser, Directorate $directorate): bool
    {
        return $authUser->can('Restore:Directorate');
    }

    public function forceDelete(AuthUser $authUser, Directorate $directorate): bool
    {
        return $authUser->can('ForceDelete:Directorate');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Directorate');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Directorate');
    }

    public function replicate(AuthUser $authUser, Directorate $directorate): bool
    {
        return $authUser->can('Replicate:Directorate');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Directorate');
    }

}