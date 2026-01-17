<?php

declare(strict_types=1);

namespace Keystone\Plugins\Auth\Domain\Token;

enum TokenType: string
{
    case ACTIVATION = 'activation';
    case PASSWORD_RESET = 'password_reset';
    case TWO_FACTOR     = 'two_factor'; 
}

?>