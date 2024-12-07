<?php

namespace App\Repositories\Master\Material;
use App\Repositories\BaseRepositoryInterface;

interface MaterialRepositoryInterface extends BaseRepositoryInterface
{
    public function getLastMaterial();

}
