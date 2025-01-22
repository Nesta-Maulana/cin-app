<?php
namespace App\Repositories\Transaction\ItemRequestDetail;

use App\Models\ItemRequestDetail;
use App\Repositories\BaseRepository;

class ItemRequestDetailRepository extends BaseRepository implements ItemRequestDetailRepositoryInterface
{
    protected $model;

    public function __construct(ItemRequestDetail $model)
    {
        parent::__construct($model);
    }

}
