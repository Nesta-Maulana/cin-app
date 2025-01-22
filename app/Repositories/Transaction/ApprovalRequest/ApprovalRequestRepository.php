<?php
namespace App\Repositories\Transaction\ApprovalRequest;

use App\Models\ApprovalRequest;
use App\Repositories\BaseRepository;

class ApprovalRequestRepository extends BaseRepository implements ApprovalRequestRepositoryInterface
{
    protected $model;

    public function __construct(ApprovalRequest $model)
    {
        parent::__construct($model);
    }

}
