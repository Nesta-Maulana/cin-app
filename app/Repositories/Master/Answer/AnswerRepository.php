<?php

namespace App\Repositories\Master\Answer;

use App\Models\Answer;
use App\Repositories\BaseRepository;

class AnswerRepository extends BaseRepository implements AnswerRepositoryInterface
{
    protected $model;

    public function __construct(Answer $model)
    {
        parent::__construct($model);
    }

}
