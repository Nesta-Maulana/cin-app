<?php
namespace App\Repositories\Transaction\DeliveryOrder;

use App\Models\DeliveryOrder;
use App\Repositories\BaseRepository;

class DeliveryOrderRepository extends BaseRepository implements DeliveryOrderRepositoryInterface
{
    protected $model;

    public function __construct(DeliveryOrder $model)
    {
        parent::__construct($model);
    }

}
