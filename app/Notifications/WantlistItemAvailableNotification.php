<?php

namespace App\Notifications;

use App\Models\VinylMaster;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WantlistItemAvailableNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $vinyl;

    /**
     * Create a new notification instance.
     */
    public function __construct(VinylMaster $vinyl)
    {
        $this->vinyl = $vinyl;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = route('site.vinyl.show', [
            'artistSlug' => $this->vinyl->artists->first()->slug, 
            'titleSlug' => $this->vinyl->slug
        ]);

        return (new MailMessage)
            ->subject('Um vinil da sua Wantlist está disponível!')
            ->greeting('Olá, ' . $notifiable->name . '!')
            ->line('Temos boas notícias! Um disco que você adicionou à sua Wantlist agora está disponível para compra.')
            ->line('Disco: ' . $this->vinyl->title . ' - ' . $this->vinyl->artists->pluck('name')->implode(', '))
            ->action('Ver Disco', $url)
            ->line('Não perca a oportunidade, pode ser que acabe rápido!')
            ->line('Obrigado por escolher a Verde&Vermelho para suas compras de discos.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'vinyl_id' => $this->vinyl->id,
            'title' => $this->vinyl->title,
            'artist' => $this->vinyl->artists->pluck('name')->implode(', '),
            'cover_image' => $this->vinyl->cover_image,
            'url' => route('site.vinyl.show', [
                'artistSlug' => $this->vinyl->artists->first()->slug,
                'titleSlug' => $this->vinyl->slug
            ]),
            'message' => 'O disco ' . $this->vinyl->title . ' da sua Wantlist agora está disponível!'
        ];
    }
}
