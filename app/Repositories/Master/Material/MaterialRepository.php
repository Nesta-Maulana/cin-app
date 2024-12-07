<?php
namespace App\Repositories\Master\Material;

use App\Models\Material;
use App\Repositories\BaseRepository;

class MaterialRepository extends BaseRepository implements MaterialRepositoryInterface
{
    protected $model;

    public function __construct(Material $model)
    {
        parent::__construct($model);
    }

    public function getLastMaterial()
    {
        return $this->model->latest()->first();
    }

}
