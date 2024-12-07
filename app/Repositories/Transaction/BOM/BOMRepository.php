<?php
namespace App\Repositories\Transaction\BOM;

use App\Models\BOM;
use App\Models\BomDetail;
use App\Models\Material;
use App\Repositories\BaseRepository;

class BOMRepository extends BaseRepository implements BOMRepositoryInterface
{
    protected $model, $detail_model, $material;

    public function __construct(BOM $model, BomDetail $detail_model, Material $material)
    {
        $this->model = $model;
        $this->detail_model = $detail_model;
        $this->material = $material;
    }

    public function getLastBOM()
    {
        return $this->model->orderBy('created_at', 'desc')->first();
    }

    public function createDetailBOM($header_data, $detail_data)
    {
        $detail_data['bom_id'] = $header_data->id;
        foreach ($detail_data['material_id'] as $key => $material_id)
        {
            $material       = $this->material->find($material_id);
            $detail    = [
                'bom_id' => $header_data->id,
                'material_id' => $material_id,
                'quantity' => $detail_data['quantity'][$key],
                'material_price_id' => $material->materialPrice->id,
                'description' => $detail_data['remarks'][$key],
            ];
            $this->detail_model->create($detail);
        }
    }
}
