<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;

class WelcomeNewUserNotification extends Notification
{
    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        // Gera um link temporário para a tela de definição de senha
        $url = URL::temporarySignedRoute(
            'password.set', // Rota que vamos criar
            Carbon::now()->addMinutes(60),
            ['email' => $notifiable->email]
        );

        return (new MailMessage)
            ->subject('Bem-vindo(a) ao Caminho da Roça!')
            ->greeting("Olá {$this->user->name},")
            ->line("É um prazer tê-lo(a) conosco! Seu cadastro no sistema de Caminho da Roça foi realizado com sucesso.")
            ->line("Aqui estão os detalhes básicos do seu acesso:")
            ->line("• Usuário: {$this->user->email}")
            ->line("• Data do Cadastro: " . now()->format('d/m/Y'))
            ->greeting("📌 Próximos passos:")
            ->action('Clique aqui para cadastrar sua senha', $url)
            ->line("Explore as funcionalidades que tornam sua jornada mais prática e eficiente.")
            ->line("Estamos à disposição para ajudá-lo(a) em sua experiência.")
            ->salutation("Atenciosamente,\nCaminho da Roça");
    }
}
