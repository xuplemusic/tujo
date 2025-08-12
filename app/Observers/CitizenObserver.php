<?php

namespace App\Observers;

use App\Models\Citizen;
use App\Models\Audit;
use Illuminate\Support\Facades\Auth;

class CitizenObserver
{
    public function created(Citizen $citizen)
    {
        $this->logAction('created', $citizen);
    }

    public function updated(Citizen $citizen)
    {
        $this->logAction('updated', $citizen);
    }

    public function deleted(Citizen $citizen)
    {
        $this->logAction('deleted', $citizen);
    }

    protected function logAction(string $action, Citizen $citizen)
    {
        Audit::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'auditable_id' => $citizen->id,
            'auditable_type' => Citizen::class,
            'old_values' => $action !== 'created' ? $citizen->getOriginal() : null,
            'new_values' => $action !== 'deleted' ? $citizen->getChanges() : null,
        ]);
    }
}
