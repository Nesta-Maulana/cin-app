<?php
namespace App\Repositories\Transaction\PurchaseOrderNew;

use App\Models\PurchaseOrderNew;
use App\Repositories\BaseRepository;

class PurchaseOrderNewRepository extends BaseRepository implements PurchaseOrderNewRepositoryInterface
{
    protected $model;

    public function __construct(PurchaseOrderNew $model)
    {
        parent::__construct($model);
    }

}
