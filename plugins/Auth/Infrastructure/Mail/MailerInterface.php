<?php

declare(strict_types=1);

namespace Keystone\Plugins\Auth\Infrastructure\Mail;

use Keystone\Domain\User\User;

interface MailerInterface {
    public function sendActivation(User $user, string $token): void;

    public function sendPasswordReset(User $user, string $token): void;
}


?>