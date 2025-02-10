<?php
namespace App\Repositories\Transaction\DeliveryOrderDetail;

use App\Models\DeliveryOrderDetail;
use App\Repositories\BaseRepository;

class DeliveryOrderDetailRepository extends BaseRepository implements DeliveryOrderDetailRepositoryInterface
{
    protected $model;

    public function __construct(DeliveryOrderDetail $model)
    {
        parent::__construct($model);
    }

}
