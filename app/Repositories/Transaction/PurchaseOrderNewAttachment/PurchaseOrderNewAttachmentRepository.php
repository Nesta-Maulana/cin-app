<?php
namespace App\Repositories\Transaction\PurchaseOrderNewAttachment;

use App\Models\PurchaseOrderNewAttachment;
use App\Repositories\BaseRepository;

class PurchaseOrderNewAttachmentRepository extends BaseRepository implements PurchaseOrderNewAttachmentRepositoryInterface
{
    protected $model;

    public function __construct(PurchaseOrderNewAttachment $model)
    {
        parent::__construct($model);
    }

}
