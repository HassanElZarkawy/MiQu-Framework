<?php

namespace Miqu\Core\Notifications;

use Miqu\Core\Mailer;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Eloquent\Model;
use Miqu\Core\Models\Notification;
use PHPMailer\PHPMailer\Exception;

class Notifier
{
    /**
     * @param $notifiable
     * @param INotification $notification
     * @throws Exception
     */
    public function send( $notifiable, INotification $notification ) : void
    {
        collect($notification->via())->each(function($item) use ( $notifiable, $notification ) {
            $results = call_user_func( [ $notification, (string)string($item)->upperCaseFirst()->prepend('to') ] );
            if ($item === 'mail')
                $this->sendMail($results);
            else if ($item === 'database')
                $this->saveNotification($notifiable, $results);
        });
    }

    /**
     * @param Mailer $mail
     * @throws Exception
     */
    private function sendMail(Mailer $mail) : void
    {
        $mail->send();
    }

    /**
     * @param Model $notifiable
     * @param array $data
     * @return void
     */
    private function saveNotification(Model $notifiable, array $data): void
    {
        $notification = new Notification();
        $fields = Manager::schema()->getColumnListing($notifiable->getTable());
        $properties = collect($data)->filter(function($item, $key) use ( $notification, $fields ) {
            return in_array( $key, $fields );
        })->all();
        $settings = collect($data)->filter(function($item, $key) use ( $notification, $fields ) {
            return ! in_array( $key, $fields );
        })->all();

        $properties[ 'settings' ] = $settings;
        $properties[ 'notifiable_type' ] = get_class( $notifiable );
        $properties[ 'notifiable_id' ] = $notifiable->{$notifiable->primaryKey};
        $properties[ 'is_read' ] = 0;

        $notification->save($properties);
    }
}