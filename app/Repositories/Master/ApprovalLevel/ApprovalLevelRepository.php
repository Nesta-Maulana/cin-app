<?php
namespace App\Repositories\Master\ApprovalLevel;

use App\Models\ApprovalLevel;
use App\Repositories\BaseRepository;

class ApprovalLevelRepository extends BaseRepository implements ApprovalLevelRepositoryInterface
{
    protected $model;

    public function __construct(ApprovalLevel $model)
    {
        parent::__construct($model);
    }

}
