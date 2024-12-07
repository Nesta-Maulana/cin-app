<?php
namespace App\Repositories\Transaction\PurchaseRequest;

use App\Models\PurchaseRequest;
use App\Repositories\BaseRepository;

class PurchaseRequestRepository extends BaseRepository implements PurchaseRequestRepositoryInterface
{
    protected $model;

    public function __construct(PurchaseRequest $model)
    {
        parent::__construct($model);
    }

}
