<?php

namespace Keystone\Security\LoginAudit;


interface LoginAuditRepositoryInterface {

    public function store(array $data): void;
}


?>