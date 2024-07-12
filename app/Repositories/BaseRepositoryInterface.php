<?php

namespace App\Repositories;

interface BaseRepositoryInterface
{
    public function all();
    public function find($id);
    public function getData(array $scope = [], array $with = [], array $orderBy = [], $paginate = null, array $conditions = [], $typeSelect = 'all');
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
}
