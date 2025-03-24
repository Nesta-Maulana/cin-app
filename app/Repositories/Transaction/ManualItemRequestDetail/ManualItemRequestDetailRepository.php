<?php
namespace App\Repositories\Transaction\ManualItemRequestDetail;

use App\Models\ManualItemRequestDetail;
use App\Repositories\BaseRepository;

class ManualItemRequestDetailRepository extends BaseRepository implements ManualItemRequestDetailRepositoryInterface
{
    protected $model;

    public function __construct(ManualItemRequestDetail $model)
    {
        parent::__construct($model);
    }

}
