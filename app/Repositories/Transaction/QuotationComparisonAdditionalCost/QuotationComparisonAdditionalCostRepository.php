<?php
namespace App\Repositories\Transaction\QuotationComparisonAdditionalCost;

use App\Models\QuotationComparisonAdditionalCost;
use App\Repositories\BaseRepository;

class QuotationComparisonAdditionalCostRepository extends BaseRepository implements QuotationComparisonAdditionalCostRepositoryInterface
{
    protected $model;

    public function __construct(QuotationComparisonAdditionalCost $model)
    {
        parent::__construct($model);
    }

}
