<?php
namespace App\Repositories\Transaction\QuotationComparisonShippingCost;

use App\Models\QuotationComparisonShippingCost;
use App\Repositories\BaseRepository;

class QuotationComparisonShippingCostRepository extends BaseRepository implements QuotationComparisonShippingCostRepositoryInterface
{
    protected $model;

    public function __construct(QuotationComparisonShippingCost $model)
    {
        parent::__construct($model);
    }

}
