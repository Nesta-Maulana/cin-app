<?php
namespace App\Repositories\Services\File;

use App\Models\File;
use App\Repositories\BaseRepository;

class FileRepository extends BaseRepository implements FileRepositoryInterface
{
    protected $model;

    public function __construct(File $model)
    {
        parent::__construct($model);
    }

}
