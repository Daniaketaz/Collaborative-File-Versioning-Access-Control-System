<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FileStatusChanged extends Notification
{
    use Queueable;

    protected $fileName;
    protected $action;
    protected $userName;

    public function __construct($fileName, $action,$userName)
    {
        $this->fileName = $fileName;
        $this->action = $action;
        $this->userName = $userName;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'message' => "file {$this->fileName} has been {$this->action} by {$this->userName}  ",
            'time' => now()->toDateTimeString(),

        ];
    }
}
