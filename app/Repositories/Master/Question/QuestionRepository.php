<?php

namespace App\Repositories\Master\Question;

use App\Models\Question;
use App\Repositories\BaseRepository;

class QuestionRepository extends BaseRepository implements QuestionRepositoryInterface
{
    protected $model;

    public function __construct(Question $model)
    {
        parent::__construct($model);
    }

}
