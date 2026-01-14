<?php

namespace Tests\Auth;

use Tests\Tester;


use Keystone\Plugins\Pages\Domain\PagePolicy;
use Keystone\Domain\User\User;

final class PagePolicyTest extends Tester {
    public function test_admin_can_edit_pages(): void
    {
        $user = new User(id: 1, email: "test@keystone.local", passwordHash: 'abc', active: 1, roles: ['admin']);

        $policy = new PagePolicy();

        $this->assertTrue(
            $policy->allows($user, 'edit')
        );
    }

    public function test_editor_cannot_publish_pages(): void
    {
        $user = new User(id: 2,email: "test@keystone.local", passwordHash: 'abc', active: 0, roles: ['editor']);

        $policy = new PagePolicy();

        $this->assertFalse(
            $policy->allows($user, 'publish')
        );
    }
}

?>