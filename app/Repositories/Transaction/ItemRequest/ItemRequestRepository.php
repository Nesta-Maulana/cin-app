<?php
namespace App\Repositories\Transaction\ItemRequest;

use App\Models\ItemRequest;
use App\Repositories\BaseRepository;

class ItemRequestRepository extends BaseRepository implements ItemRequestRepositoryInterface
{
    protected $model;

    public function __construct(ItemRequest $model)
    {
        parent::__construct($model);
    }

}
