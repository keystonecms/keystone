<?php

namespace Keystone\Core\Auth;

use Keystone\Core\Activity\ActivityService;

final class AuthorityActivityService {

    public function __construct(
        private ActivityService $activity,
    ) {}

    public function denied(
        string $policy,
        int $userId,
    ): void {
        $this->activity->log(
            message: sprintf('Policy denied: %s', $policy),
            actorType: 'user',
            actorId: $userId,
            context: 'authority'
        );
    }

    public function roleChanged(
        int $targetUserId,
        string $from,
        string $to,
        int $actorUserId,
    ): void {
        $this->activity->log(
            message: sprintf(
                'Rol gewijzigd (%s → %s) voor gebruiker #%d',
                $from,
                $to,
                $targetUserId
            ),
            actorType: 'user',
            actorId: $actorUserId,
            context: 'authority'
        );
    }

    public function loginFailed(string $identifier, string $ipad): void {
        $this->activity->log(
            message: sprintf('Login mislukt (%s) van (%s)', $identifier, $ipaddress),
            actorType: 'system',
            context: 'authority'
        );
    }
    public function loginSuccesFull(string $identifier, string $ipaddress): void {
        $this->activity->log(
            message: sprintf('Login succesvol (%s) van (%s)', $identifier, $ipaddress),
            actorType: 'system',
            context: 'authority'
        );
    }    

    public function uploadSuccesFull(string $filename, string $identifier, string $ipaddress): void {
    $this->activity->log(
        message: sprintf('Upload filename (%s) succesvol (%s) van (%s)',$filename, $identifier, $ipaddress),
        actorType: 'system',
        context: 'upload'
        );
    }
   
    public function page(int $userId, string $filename, string $identifier, string $ipaddress): void {
    $this->activity->log(
        message: sprintf('Page added/modified (%s) by (%s) ip-address (%s)', $filename, $identifier, $ipaddress),
        actorType: 'pages',
        actorId: $userId,
        context: 'add'
        );
    }
 
}

?>