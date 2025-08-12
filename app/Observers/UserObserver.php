<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Audit;
use Illuminate\Support\Facades\Auth;

class UserObserver
{
    public function created(User $user)
    {
        $this->logAction('created', $user);
    }

    public function updated(User $user)
    {
        $this->logAction('updated', $user);
    }

    public function deleted(User $user)
    {
        $this->logAction('deleted', $user);
    }

    protected function logAction(string $action, User $user)
    {
        // Don't log password changes
        $oldValues = $user->getOriginal();
        unset($oldValues['password']);
        $newValues = $user->getChanges();
        unset($newValues['password']);

        Audit::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'auditable_id' => $user->id,
            'auditable_type' => User::class,
            'old_values' => $action !== 'created' ? $oldValues : null,
            'new_values' => $action !== 'deleted' ? $newValues : null,
        ]);
    }
}
