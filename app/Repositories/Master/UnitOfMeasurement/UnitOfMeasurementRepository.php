<?php
namespace App\Repositories\Master\UnitOfMeasurement;

use App\Models\UnitOfMeasurement;
use App\Repositories\BaseRepository;

class UnitOfMeasurementRepository extends BaseRepository implements UnitOfMeasurementRepositoryInterface
{
    protected $model;

    public function __construct(UnitOfMeasurement $model)
    {
        parent::__construct($model);
    }

}
