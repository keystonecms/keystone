<?php

declare(strict_types=1);

namespace Keystone\Http\Controllers\Admin;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Keystone\Domain\Policy\PolicyRepositoryInterface;

final class PolicyController {


public function __construct(
        private PolicyRepositoryInterface $policies,
        private Twig $view
    ) {
    }

public function index(
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface {



$policies = $this->policies->findAll();

$groupedPolicies = [];

foreach ($policies as $policy) {
    $category = $policy['category'] ?? 'Overig';
    $groupedPolicies[$category][] = $policy;
}

return $this->view->render(
            $response,
            '@core/admin/policies/index.twig',
            [
                'policies' => $groupedPolicies,
            ]
        );
}

public function edit(int $id) {

    return $this->render('@core/admin/roles/edit.twig', [
        'role' => $this->roles->get($id),
        'policies' => $this->policies->all(),
        'rolePolicyIds' => $this->roles->policyIds($id),
    ]);
}

public function update(int $id, array $data) {
    $this->roles->syncPolicies(
        $id,
        $data['policies'] ?? []
    );

    return $this->redirect('admin.roles');
    }
}

?>