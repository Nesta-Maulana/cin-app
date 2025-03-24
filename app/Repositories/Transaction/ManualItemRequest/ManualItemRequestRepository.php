<?php
namespace App\Repositories\Transaction\ManualItemRequest;

use App\Models\ManualItemRequest;
use App\Repositories\BaseRepository;

class ManualItemRequestRepository extends BaseRepository implements ManualItemRequestRepositoryInterface
{
    protected $model;

    public function __construct(ManualItemRequest $model)
    {
        parent::__construct($model);
    }

}
