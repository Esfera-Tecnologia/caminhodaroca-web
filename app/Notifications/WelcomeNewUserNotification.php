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
        $isApiRegistration = $this->user->registration_source === 'api';

        if ($isApiRegistration) {
            // Texto para cadastros via API (sem botão de definir senha)
            return (new MailMessage)
                ->subject('Bem-vindo(a) ao Caminho da Roça!')
                ->greeting("Olá {$this->user->name},")
                ->line("É um prazer tê-lo(a) conosco! Seu cadastro no Caminho da Roça foi realizado com sucesso e estamos empolgados para ajudá-lo(a) a aproveitar ao máximo tudo o que oferecemos.")
                ->line("Aqui estão os detalhes básicos do seu acesso:")
                ->line("• Usuário: {$this->user->email}")
                ->line("• Data do Cadastro: " . now()->format('d/m/Y'))
                ->line("Agora você pode acessar nosso app e explorar as funcionalidades disponíveis. Caso tenha alguma dúvida ou precise de ajuda, não hesite em entrar em contato com nossa equipe de suporte.")
                ->line("📌 Próximos passos:")
                ->line("Explore as funcionalidades que tornam sua jornada mais prática e eficiente.")
                ->line("Estamos à disposição para ajudá-lo(a) em sua experiência. Mais uma vez, seja bem-vindo(a) e conte conosco!")
                ->salutation("Atenciosamente,\nCaminho da Roça");
        } else {
            // Texto para cadastros via WEB (com botão de definir senha)
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
                ->greeting("Olá {$this->user->name},")
                ->line("É um prazer tê-lo(a) conosco! Seu cadastro no sistema de Caminho da Roça foi realizado com sucesso.")
                ->line("Aqui estão os detalhes básicos do seu acesso:")
                ->line("• Usuário: {$this->user->email}")
                ->line("• Data do Cadastro: " . now()->format('d/m/Y'))
                ->line("📌 Próximos passos:")
                ->action('Clique aqui para cadastrar sua senha', $url)
                ->line("Explore as funcionalidades que tornam sua jornada mais prática e eficiente.")
                ->line("Estamos à disposição para ajudá-lo(a) em sua experiência.")
                ->salutation("Atenciosamente,\nCaminho da Roça");
        }
    }
}
