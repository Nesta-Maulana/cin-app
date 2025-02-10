<?php
namespace App\Repositories\Transaction\StockEntry;

use App\Models\StockEntry;
use App\Repositories\BaseRepository;

class StockEntryRepository extends BaseRepository implements StockEntryRepositoryInterface
{
    protected $model;

    public function __construct(StockEntry $model)
    {
        parent::__construct($model);
    }

}
