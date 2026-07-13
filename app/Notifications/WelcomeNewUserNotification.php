<?php

namespace App\Notifications;


use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Password;

class WelcomeNewUserNotification extends Notification
{
    protected $user;
    protected $propertyName;

    public function __construct($user, $propertyName = null)
    {
        $this->user = $user;
        $this->propertyName = $propertyName;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $token = Password::createToken($notifiable);
        $url = URL::temporarySignedRoute(
            'password.set.token',
            Carbon::now()->addMinutes(60),
            [
                'token' => $token,
                'email' => $notifiable->email,
            ]
        );

        return (new MailMessage)
            ->subject('Bem-vindo(a) ao Caminho da Roça!')
            ->greeting($this->propertyName ? "Olá, {$this->propertyName}!" : "Olá {$this->user->name},")
            ->line("Seja muito bem-vindo(a) ao Caminho da Roça. Seu cadastro foi realizado com sucesso!")
            ->line("Confira abaixo os dados básicos de acesso:")
            ->line("• Usuário: {$this->user->email}")
            ->line("• Data do Cadastro: " . now()->format('d/m/Y'))
            ->action('Clique aqui para cadastrar sua senha', $url)
            ->line("Após o cadastro da senha, você poderá acessar o sistema para visualizar e atualizar as informações da sua propriedade, como endereço, telefone, fotos e outros dados, além de explorar todas as funcionalidades disponíveis na plataforma. Em caso de dúvidas ou necessidade de suporte, nossa equipe estará à disposição pelos canais abaixo:")
            ->line("• E-mail: contato@caminhodaroca.app.br")
            ->line("• WhatsApp: (21) 96867-0746")
            ->line("\nConte conosco nessa jornada!\n")
            ->salutation("Atenciosamente,\n\nEquipe Caminho da Roça");
    }
}
