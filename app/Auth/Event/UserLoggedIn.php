<?php

namespace Keystone\Auth\Event;


final class UserLoggedIn {

    public function __construct(
        public readonly int $userId,
        public readonly string $ip
    ) {}
}


?>