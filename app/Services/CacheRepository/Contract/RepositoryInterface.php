<?php

namespace App\Services\CacheRepository\Contract;

interface RepositoryInterface
{

    const DURATION = 1800; // in minutes

    public function all();

    public function find(int $id);

    public function findBy(array $filter);

    public function findAll(array $filter);

    public function update(array $data, int $id);

    public function updateBy(array $data, array $filter);

    public function delete(int $id = null);

    public function deleteBy(array $filter);

    public static function init($model);
}