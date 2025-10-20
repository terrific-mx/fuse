<?php

namespace App\Notifications;

use App\Models\Server;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ServerProvisioned extends Notification
{
    use Queueable;

    public function __construct(public Server $server) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your server is ready!')
            ->line("Your server \"{$this->server->name}\" has been successfully provisioned and is ready to use.")
            ->line('')
            ->line('**SSH Access**')
            ->line('Username: `fuse`')
            ->line('Sudo password: `'.$this->server->sudo_password.'`')
            ->line('SSH command:')
            ->line('```')
            ->line('ssh fuse@'.$this->server->ip_address)
            ->line('```')
            ->line('')
            ->line('**API Address**')
            ->line('`'.$this->server->ip_address.'`')
            ->line('')
            ->line('**Database Access**')
            ->line('Database name: `fuse`')
            ->line('Database password: `'.$this->server->database_password.'`')
            ->action('View Server', url('/servers/'.$this->server->id))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'server_id' => $this->server->id,
        ];
    }
}
