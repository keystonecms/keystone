<?php

namespace Keystone\Domain\Page;

interface PageRepository {
    public function findById(int $id): ?Page;
    public function findBySlug(string $slug): ?Page;
    public function save(Page $page): void;
    }


    ?>
