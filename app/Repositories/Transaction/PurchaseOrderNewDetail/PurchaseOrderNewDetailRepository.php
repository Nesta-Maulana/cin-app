<?php
namespace App\Repositories\Transaction\PurchaseOrderNewDetail;

use App\Models\PurchaseOrderNewDetail;
use App\Repositories\BaseRepository;

class PurchaseOrderNewDetailRepository extends BaseRepository implements PurchaseOrderNewDetailRepositoryInterface
{
    protected $model;

    public function __construct(PurchaseOrderNewDetail $model)
    {
        parent::__construct($model);
    }

}
