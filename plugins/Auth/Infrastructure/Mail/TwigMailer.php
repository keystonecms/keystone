<?php

declare(strict_types=1);

namespace Keystone\Plugins\Auth\Infrastructure\Mail;

use Keystone\Domain\User\User;
use Slim\Views\Twig;
use Psr\Log\LoggerInterface;

final class TwigMailer implements MailerInterface {
    public function __construct(
        private Twig $twig,
        private LoggerInterface $logger,
        private string $fromAddress,
        private string $baseUrl
    ) {}

    public function sendActivation(User $user, string $token): void
    {
        $this->send(
            $user->email(),
            'Activeer je account',
            '@auth/mail/activate.twig',
            [
                'user' => $user,
                'url'  => $this->baseUrl . '/activate?token=' . $token,
            ]
        );
    }

    public function sendPasswordReset(User $user, string $token): void
    {
        $this->send(
            $user->email(),
            'Stel je wachtwoord in',
            '@auth/mail/reset_password.twig',
            [
                'user' => $user,
                'url'  => $this->baseUrl . '/reset-password?token=' . $token,
            ]
        );
    }

    private function send(
        string $to,
        string $subject,
        string $template,
        array $context
    ): void {
        $body = $this->twig->fetch($template, $context);

        // Simpel voorbeeld (later vervangbaar door Symfony Mailer, SMTP, etc.)
        mail(
            $to,
            $subject,
            $body,
            sprintf("From: %s\r\nContent-Type: text/html", $this->fromAddress)
        );

        $this->logger->info('Mail sent', [
            'to' => $to,
            'subject' => $subject,
        ]);
    }
}


?>