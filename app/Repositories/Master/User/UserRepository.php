<?php

namespace App\Repositories\Master\User;

use App\Models\User;
use App\Repositories\BaseRepository;
use Exception;
use Illuminate\Database\QueryException;
use Log;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    protected $model;

    public function __construct(User $model)
    {
        parent::__construct($model);
    }

}
