<?php
namespace App\Repositories\Transaction\PurchaseOrderSupplierOfferDetail;

use App\Models\PurchaseOrderSupplierOfferDetail;
use App\Repositories\BaseRepository;

class PurchaseOrderSupplierOfferDetailRepository extends BaseRepository implements PurchaseOrderSupplierOfferDetailRepositoryInterface
{
    protected $model;

    public function __construct(PurchaseOrderSupplierOfferDetail $model)
    {
        parent::__construct($model);
    }

}
