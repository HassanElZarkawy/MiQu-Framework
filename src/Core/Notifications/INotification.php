<?php

namespace Miqu\Core\Notifications;

use Miqu\Core\Mailer;

interface INotification
{
    public function via() : array;

    public function toMail() : Mailer;

    public function toDatabase() : array;
}