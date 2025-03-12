<?php
namespace App\Repositories\Transaction\QuotationComparisonDetail;

use App\Models\QuotationComparisonDetail;
use App\Repositories\BaseRepository;

class QuotationComparisonDetailRepository extends BaseRepository implements QuotationComparisonDetailRepositoryInterface
{
    protected $model;

    public function __construct(QuotationComparisonDetail $model)
    {
        parent::__construct($model);
    }

}
