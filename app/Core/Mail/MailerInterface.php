<?php

namespace Keystone\Core\Mail;

interface MailerInterface {

    public function send(
        string $to,
        string $subject,
        string $template,
        array $context = []
    ): void;
}

?>