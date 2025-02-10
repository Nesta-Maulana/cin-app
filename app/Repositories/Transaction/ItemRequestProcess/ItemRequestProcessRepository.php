<?php
namespace App\Repositories\Transaction\ItemRequestProcess;

use App\Models\ItemRequestProcess;
use App\Repositories\BaseRepository;

class ItemRequestProcessRepository extends BaseRepository implements ItemRequestProcessRepositoryInterface
{
    protected $model;

    public function __construct(ItemRequestProcess $model)
    {
        parent::__construct($model);
    }

}
