<?php
namespace App\Repositories\Transaction\QuotationComparison;

use App\Models\QuotationComparison;
use App\Repositories\BaseRepository;

class QuotationComparisonRepository extends BaseRepository implements QuotationComparisonRepositoryInterface
{
    protected $model;

    public function __construct(QuotationComparison $model)
    {
        parent::__construct($model);
    }

}
