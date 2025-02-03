<?php
namespace App\Repositories\Master\Department;

use App\Models\Department;
use App\Repositories\BaseRepository;

class DepartmentRepository extends BaseRepository implements DepartmentRepositoryInterface
{
    protected $model;

    public function __construct(Department $model)
    {
        parent::__construct($model);
    }

}
