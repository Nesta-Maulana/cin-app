<?php
namespace App\Repositories\Transaction\PurchaseOrderSupplierOffer;

use App\Models\PurchaseOrderSupplierOffer;
use App\Repositories\BaseRepository;

class PurchaseOrderSupplierOfferRepository extends BaseRepository implements PurchaseOrderSupplierOfferRepositoryInterface
{
    protected $model;

    public function __construct(PurchaseOrderSupplierOffer $model)
    {
        parent::__construct($model);
    }

}
