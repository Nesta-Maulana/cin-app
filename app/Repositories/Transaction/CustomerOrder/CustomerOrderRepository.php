<?php
namespace App\Repositories\Transaction\CustomerOrder;

use App\Models\CustomerOrder;
use App\Repositories\BaseRepository;

class CustomerOrderRepository extends BaseRepository implements CustomerOrderRepositoryInterface
{
    protected $model;

    public function __construct(CustomerOrder $model)
    {
        parent::__construct($model);
    }

}
