<?php
namespace App\Repositories\Master\Approval;

use App\Models\Approval;
use App\Repositories\BaseRepository;

class ApprovalRepository extends BaseRepository implements ApprovalRepositoryInterface
{
    protected $model;

    public function __construct(Approval $model)
    {
        parent::__construct($model);
    }

}
