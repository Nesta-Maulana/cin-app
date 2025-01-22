<?php
namespace App\Repositories\Master\JobCategory;

use App\Models\JobCategory;
use App\Repositories\BaseRepository;

class JobCategoryRepository extends BaseRepository implements JobCategoryRepositoryInterface
{
    protected $model;

    public function __construct(JobCategory $model)
    {
        parent::__construct($model);
    }

}
