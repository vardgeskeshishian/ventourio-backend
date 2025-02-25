<?php

namespace App\Models\System;

interface HasSubscribers
{
    public function getMailTemplate(): string;

    public function getMailSubject(): string;

    public function getMailData(): array;
}
