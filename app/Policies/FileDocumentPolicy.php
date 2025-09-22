<?php

namespace App\Policies;

use App\Models\User;
use App\Models\FileDocument;
use App\Models\Project;
use Illuminate\Auth\Access\HandlesAuthorization;

class FileDocumentPolicy
{
    use HandlesAuthorization;

    /**
     * Global before check: allow admins to do anything.
     * If you return true here the specific method won't be called.
     */
    public function before(?User $user, $ability)
    {
        if ($user && method_exists($user, 'hasRole') && $user->hasRole('admin')) {
            return true;
        }

        // allow if your User model uses 'role' column instead of hasRole()
        if ($user && isset($user->role) && $user->role === 'admin') {
            return true;
        }

        // return null to continue to specific checks
        return null;
    }

    /**
     * Determine whether the user can view the file.
     * Authenticated users: owner (user_id or created_by), project owner, project member, or admin (handled above).
     */
    public function view(?User $user, FileDocument $file)
    {
        // Public unauthenticated view is not allowed here.
        if (! $user) {
            return false;
        }

        // Owner by direct user_id (customer/user who file belongs to)
        if ($file->user_id && $user->id === (int) $file->user_id) {
            return true;
        }

        // Owner by creator (created_by)
        if ($file->created_by && $user->id === (int) $file->created_by) {
            return true;
        }

        // Project-based checks
        if ($file->project_id) {
            // If Project model has user_id as owner:
            if ($file->project && isset($file->project->user_id) && $user->id === (int) $file->project->user_id) {
                return true;
            }

            // If you store project membership on User model relationship `projects()`
            if (method_exists($user, 'projects')) {
                try {
                    if ($user->projects()->where('id', $file->project_id)->exists()) {
                        return true;
                    }
                } catch (\Throwable $e) {
                    // ignore if relationship not present or broken
                }
            }

            // If Project has a members() relation (pivot)
            if ($file->project && method_exists($file->project, 'members')) {
                try {
                    if ($file->project->members()->where('user_id', $user->id)->exists()) {
                        return true;
                    }
                } catch (\Throwable $e) {
                    // ignore
                }
            }
        }

        return false;
    }

    /**
     * Determine whether the user can download the file.
     * Usually same as view, but separated so you can allow view inline (image preview) and restrict download.
     */
    public function download(?User $user, FileDocument $file)
    {
        // Signed URL bypass is handled in controller (signed link -> controller skips policy).
        // For authenticated users apply same rules as view (customize if needed).
        return $this->view($user, $file);
    }

    /**
     * Determine whether the user can delete the file.
     */
    public function delete(?User $user, FileDocument $file)
    {
        if (! $user) return false;

        // Allow admin via before(), owner, or creator
        if ($file->user_id && $user->id === (int) $file->user_id) return true;
        if ($file->created_by && $user->id === (int) $file->created_by) return true;

        // Project owners / project members could be allowed to delete — adjust as necessary
        if ($file->project_id) {
            if ($file->project && isset($file->project->user_id) && $file->project->user_id === $user->id) {
                return true;
            }
        }

        return false;
    }

    // Add other abilities (update, share, rename) as needed...
}
