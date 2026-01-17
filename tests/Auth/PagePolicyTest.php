<?php

namespace Keystone\Tests\Auth;

use Keystone\Tests\Tester;


use Keystone\Plugins\Pages\Domain\PagePolicy;
use Keystone\Domain\User\User;
use Keystone\Core\Auth\UserStatus;


final class PagePolicyTest extends Tester {
    public function test_admin_can_edit_pages(): void
    {
        $user = new User(id: 1, email: "test@keystone.local", passwordHash: 'abc', status: UserStatus::ACTIVE, roles: ['admin'], twoFactorSecret: '11acadaq332424');

        $policy = new PagePolicy();

        $this->assertTrue(
            $policy->allows($user, 'edit')
        );
    }

    public function test_editor_cannot_publish_pages(): void
    {
        $user = new User(id: 2,email: "test@keystone.local", passwordHash: 'abc', status: UserStatus::ACTIVE, roles: ['editor'], twoFactorSecret: '11acadaq332424');

        $policy = new PagePolicy();

        $this->assertFalse(
            $policy->allows($user, 'publish')
        );
    }
}

?>