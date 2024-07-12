<?php

namespace App\Repositories\Master\Role;

use App\Models\Role;
use App\Repositories\BaseRepository;

class RoleRepository extends BaseRepository implements RoleRepositoryInterface
{
    protected $model;

    public function __construct(Role $model)
    {
        parent::__construct($model);
    }

}
