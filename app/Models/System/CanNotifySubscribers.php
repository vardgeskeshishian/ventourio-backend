<?php

namespace App\Models\System;

use App\Events\InstanceWithSubscribersCreated;

trait CanNotifySubscribers
{
    public function notifySubscribers(): void
    {
        InstanceWithSubscribersCreated::dispatch($this);
    }

    public function getMailTemplate(): string
    {
        return $this->mailTemplate ?? 'emails.subscriptions.base_template';
    }

    public function getMailSubject(): string
    {
        return $this->mailSubject ?? 'New update on Ventourio';
    }

    public function getMailData(): array
    {
        return [];
    }
}
