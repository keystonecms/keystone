<?php

declare(strict_types=1);

namespace Keystone\Plugins\Auth\Infrastructure\Mail;

use Keystone\Domain\User\User;
use Slim\Views\Twig;
use PHPMailer\PHPMailer\PHPMailer;
use Psr\Log\LoggerInterface;

final class SmtpMailer implements MailerInterface {
    public function __construct(
        private Twig $twig,
        private LoggerInterface $logger,
        private array $smtp,
        private string $fromAddress,
        private string $baseUrl
    ) {}

    public function sendActivation(User $user, string $token): void {
        $this->send(
            $user->email(),
            'Activeer je account',
            '@auth/mail/activate.twig',
            [
                'url' => $this->baseUrl . '/activate?token=' . $token,
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
                'url' => $this->baseUrl . '/reset-password?token=' . $token,
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

        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host       = $this->smtp['host'];
        $mail->Port       = $this->smtp['port'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $this->smtp['user'];
        $mail->Password   = $this->smtp['pass'];
        $mail->SMTPSecure = $this->smtp['encryption'];

        $mail->setFrom($this->fromAddress);
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();

        $this->logger->info('SMTP mail sent', [
            'to' => $to,
            'subject' => $subject,
        ]);
    }
}

?>
