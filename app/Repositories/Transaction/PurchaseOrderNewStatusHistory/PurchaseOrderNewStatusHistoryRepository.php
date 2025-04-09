<?php
namespace App\Repositories\Transaction\PurchaseOrderNewStatusHistory;

use App\Models\PurchaseOrderNewStatusHistory;
use App\Repositories\BaseRepository;

class PurchaseOrderNewStatusHistoryRepository extends BaseRepository implements PurchaseOrderNewStatusHistoryRepositoryInterface
{
    protected $model;

    public function __construct(PurchaseOrderNewStatusHistory $model)
    {
        parent::__construct($model);
    }

}
