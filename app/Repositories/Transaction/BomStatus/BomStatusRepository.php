<?php
namespace App\Repositories\Transaction\BomStatus;

use App\Models\BomStatus;
use App\Repositories\BaseRepository;

class BomStatusRepository extends BaseRepository implements BomStatusRepositoryInterface
{
    protected $model;

    public function __construct(BomStatus $model)
    {
        parent::__construct($model);
    }

}
