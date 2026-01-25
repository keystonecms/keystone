<?php

namespace Keystone\Core\Mail;

final class NullMailer implements MailerInterface {
    
    public function send(
        string $to,
        string $subject,
        string $template,
        array $context = []
    ): void {

     // intentionally noop
 
     }
}

?>