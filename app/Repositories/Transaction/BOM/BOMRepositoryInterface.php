<?php

namespace App\Repositories\Transaction\BOM;
use App\Repositories\BaseRepositoryInterface;

interface BOMRepositoryInterface extends BaseRepositoryInterface
{
    public function getLastBOM();
    public function createDetailBOM($header_data, $detail_data);
}
