<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Exception;
use Log;

class BaseRepository implements BaseRepositoryInterface
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        try {
            return $this->model->all();
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw new Exception("Error fetching all records: " . $e->getMessage());
        }
    }

    public function getData(array $scope = [], array $with = [], array $orderBy = [], $paginate = null, array $conditions = [], $typeSelect = 'all')
    {
        try {
            $query = $this->model->newQuery();

            // Apply scopes
            foreach ($scope as $method => $parameters) {
                if (method_exists($this->model, 'scope' . ucfirst($method))) {
                    if (is_array($parameters))
                    {
                        $query = $query->$method(...$parameters);
                    }
                    else
                    {
                        $query = $query->$method($parameters);
                    }
                }
            }

            // Apply eager loading
            $query = $this->applyEagerLoading($query, $with);

            // Apply conditions
            foreach ($conditions as $condition) {
                switch ($condition[1]) {
                    case 'IS':
                        $query->whereNull($condition[0]);
                        break;
                    case 'IS NOT':
                        $query->whereNotNull($condition[0]);
                        break;
                    case 'IN':
                        $query->whereIn($condition[0], $condition[2]);
                        break;
                    case 'NOT IN':
                        $query->whereNotIn($condition[0], $condition[2]);
                        break;
                    default:
                        $query->where($condition[0], $condition[1], $condition[2]);
                        break;
                }
            }


            // Apply order by
            foreach ($orderBy as $column => $direction) {
                $query = $query->orderBy($column, $direction);
            }
            // Apply pagination if needed
            if ($paginate) {
                return $query->paginate($paginate);
            }
            Log::info($query->toSql());
            if ($typeSelect === 'first') {
                return $query->first();
            } elseif ($typeSelect === 'last') {
                return $query->latest()->first(); // Use latest for the last record
            } else {
                return $query->get();
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw new Exception("Error fetching records: " . $e->getMessage());
        }
    }
    private function applyEagerLoading($query, $with)
    {
        foreach ($with as $relation => $callback) {
            if (is_array($callback)) {
                $query = $query->with([
                    $relation => function ($query) use ($callback) {
                        $this->applyEagerLoading($query, $callback);
                    }
                ]);
            } elseif (is_callable($callback)) {
                $query = $query->with([$relation => $callback]);
            } else {
                $query = $query->with($callback);
            }
        }

        return $query;
    }

    public function find($id)
    {
        try {
            return $this->model->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            Log::error($e->getMessage());
            throw new Exception("Record not found: " . $e->getMessage());
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw new Exception("Error fetching record: " . $e->getMessage());
        }
    }

    public function create(array $data)
    {
        try {
            return $this->model->create($data);
        } catch (QueryException $e) {
            Log::error($e->getMessage());
            throw new Exception("Database error creating record: " . $e->getMessage());
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw new Exception("General error creating record: " . $e->getMessage());
        }
    }

    public function update($id, array $data)
    {
        try {
            $model = $this->find($id);
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

    public function delete($id)
    {
        try {
            $model = $this->find($id);
            return $model->delete();
        } catch (QueryException $e) {
            Log::error($e->getMessage());
            throw new Exception("Database error deleting record: " . $e->getMessage());
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw new Exception("General error deleting record: " . $e->getMessage());
        }
    }

}
