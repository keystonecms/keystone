<?php

namespace Keystone\Core\Security;

use Keystone\Plugins\Auth\Infrastructure\Mail\MailerInterface;
use Keystone\Domain\User\UserRepositoryInterface;
use Keystone\Core\User\UserSecuritySettingsService;

final class SecurityNotificationService {
    public function __construct(
        private MailerInterface $mailer,
        private UserRepositoryInterface $userRepository,
        private UserSecuritySettingsService $securitySettings
    ) {}

    public function notify(
        int $userId,
        string $type,
        string $ip
    ): void {

    $user = $this->userRepository->findById($userId);

    $settings = $this->securitySettings->get($userId);

    if ($type === 'login_new_ip' && !$settings->notifyNewIp()) {
        return;
    }

  if ($type === 'login_failed_threshold'
        && !$settings->notifyFailedLogins()
    ) {
        return;
    }

        match ($type) {
            'login_new_ip' => $this->mailer->sendLoginNewIp($user, $ip),
            'login_failed_threshold' => $this->mailer->sendMessage(
                $user->email(),
                'Meerdere mislukte loginpogingen',
                '@auth/mail/failed_logins.twig',
                ['ip' => $ip]
            ),
            default => null,
        };
    }
}


?>