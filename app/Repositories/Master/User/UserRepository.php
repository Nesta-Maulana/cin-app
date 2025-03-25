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
    public function update($nik, array $data, $requireApproval = false)
    {
        try {
            $model = $this->model->where('nik', $nik)->first();
            $model->update($data);
            return $model;
        } catch (QueryException $e) {
            Log::error($e->getMessage());
            throw new Exception("Database error updating record: " . $e->getMessage());
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw new Exception("General error updating record: " . $e->getMessage());
        }
    }
}
