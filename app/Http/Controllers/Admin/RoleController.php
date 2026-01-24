<?php

declare(strict_types=1);

namespace Keystone\Http\Controllers\Admin;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

use Keystone\Core\Auth\PolicyResolver;
use Keystone\Domain\Role\RoleRepositoryInterface;
use Keystone\Domain\Policy\PolicyRepositoryInterface;
use Keystone\Domain\User\CurrentUser;



final class RoleController {


    public function __construct(
        private Twig $view,
        private RoleRepositoryInterface $roles,
        private PolicyRepositoryInterface $policies,
        private CurrentUser $currentUser,
        private PolicyResolver $policyResolver
    ) {}

    /**
     * GET /admin/roles
     */
    public function index(
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface {
        return $this->view->render($response, '@core/admin/roles/index.twig', [
            'roles' => $this->roles->findAllWithStats(),
        ]);
    }


public function create(
    ServerRequestInterface $request,
    ResponseInterface $response
): ResponseInterface {
    return $this->view->render($response, '@core/admin/roles/create.twig');
}

public function store(
    ServerRequestInterface $request,
    ResponseInterface $response
): ResponseInterface {
    $data = $request->getParsedBody() ?? [];

    if (empty($data['name'])) {
        return $this->json($response, [
            'status'  => 'error',
            'message' => 'Rolnaam is verplicht.',
        ], 422);
    }

    $this->roles->create([
        'name' => trim($data['name']),
    ]);

    return $this->json($response, [
        'status'  => 'ok',
        'message' => 'Rol succesvol aangemaakt.',
        'redirect'=> '/admin/roles'
    ]);
}



    /**
     * GET /admin/roles/{id}
     */
    public function edit(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $roleId = (int) $args['id'];

$policies = $this->policies->findAll();

$groupedPolicies = [];

foreach ($policies as $policy) {
    $category = $policy['category'] ?? 'Overig';
    $groupedPolicies[$category][] = $policy;
}


        return $this->view->render($response, '@core/admin/roles/edit.twig', [
            'role'          => $this->roles->find($roleId),
            'policies'      => $groupedPolicies,
            'rolePolicyIds' => $this->roles->policyIds($roleId),
        ]);
    }

    /**
     * POST /admin/roles/{id}
     * (AJAX)
     */
    public function update(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $roleId = (int) $args['id'];
        $data   = $request->getParsedBody() ?? [];

        // Extra safety: policy handhaving (defence in depth)
        if (!$this->policyResolver->userHasPolicy(
            $this->currentUser->id(),
            'roles.manage'
        )) {
            return $this->json($response, [
                'status'  => 'error',
                'message' => 'Je hebt geen rechten om rollen te beheren.',
            ], 403);
        }

        $this->roles->syncPolicies(
            $roleId,
            $data['policies'] ?? []
        );

        return $this->json($response, [
            'status'  => 'ok',
            'message' => 'Rol is succesvol bijgewerkt.',
        ]);
    }

    /**
     * Kleine helper (of uit je BaseController)
     */
    private function json(
        ResponseInterface $response,
        array $data,
        int $status = 200
    ): ResponseInterface {
        $response->getBody()->write(
            json_encode($data, JSON_THROW_ON_ERROR)
        );

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }
}

?>