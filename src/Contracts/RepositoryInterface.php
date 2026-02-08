<?php

declare(strict_types=1);

namespace Jengo\Base\Contracts;

use CodeIgniter\Model;

interface RepositoryInterface
{
    public function model(): Model;
    // add crud methods
    public function find(int $id): ?object;

    public function findAll(): array;

    public function save(array|object $entity): bool;

    public function insert(array|object $entity): bool|int|string;

    public function delete(int $id): bool;

    public function errors(): array;
}
