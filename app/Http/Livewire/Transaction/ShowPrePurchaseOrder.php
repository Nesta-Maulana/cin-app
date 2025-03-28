<?php

namespace App\Http\Livewire\Transaction;

use Livewire\Component;
use App\Models\PrePurchaseOrder;
use App\Models\QuotationComparison;
use Livewire\WithPagination;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ShowPrePurchaseOrder extends Component
{
    use LivewireAlert;
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    protected $paginationClasses = 'd-flex align-items-center';

    public $paginate = 10;
    public $search = "";
    public $selected = [];
    public $selectAll = false;
    public $expandedRows = [];

    public $title = "Pre Purchase Order";
    public $model, $modelId;
    public $newStatus; // For batch status updates
    public $selectedQuotationId; // For selecting supplier

    protected $listeners = [
        'deleteConfirmed' => 'delete'
    ];

    public function render()
    {
        try {
            $query = PrePurchaseOrder::query();

            // Apply search filter
            if (!empty($this->search)) {
                $query->where(function ($q) {
                    $q->where('pre_po_number', 'like', "%{$this->search}%")
                        ->orWhereHas('customerOrder', function ($q) {
                            $q->where('order_number', 'like', "%{$this->search}%");
                        })
                        ->orWhereHas('createdBy', function ($q) {
                            $q->where('name', 'like', "%{$this->search}%");
                        });
                });
            }

            // Load relationships for both system and manual item requests
            $query->with([
                'details.itemRequestDetail.itemRequest',
                'details.itemRequestDetail.itemPriceHistory.itemUom.item',
                'details.itemRequestDetail.itemPriceHistory.itemUom.unitOfMeasurement',
                'details.manualItemRequestDetail.manualItemRequest',
                'details.uom.unitOfMeasurement',
                'quotations.supplier',
                'quotations.quotationDetails.prePurchaseOrderDetail',
                'quotations.beforeTaxCosts',
                'quotations.afterTaxCosts',
                'customerOrder',
                'createdBy'
            ]);

            // Order by latest
            $query->orderBy('id', 'desc');

            // Paginate results
            $table = $query->paginate($this->paginate);
            return view('livewire.transaction.show-pre-purchase-order', [
                'table' => $table,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching pre-purchase orders: ' . $e->getMessage());
            $this->alert('error', 'Error fetching data: ' . $e->getMessage());
            return view('livewire.transaction.show-pre-purchase-order', [
                'table' => collect([]),
            ]);
        }
    }

    // Toggle expand/collapse row
    public function toggleExpand($itemId)
    {
        if (isset($this->expandedRows[$itemId])) {
            unset($this->expandedRows[$itemId]);
        } else {
            $this->expandedRows[$itemId] = true;
        }
    }

    // Select supplier for quotation
    public function selectSupplier($quotationId)
    {
        $this->selectedQuotationId = $quotationId;
        $this->dispatchBrowserEvent('open-supplier-modal');
    }

    public function confirmSelectSupplier()
    {
        DB::beginTransaction();

        try {
            $quotation = QuotationComparison::findOrFail($this->selectedQuotationId);

            // Reset any previously selected quotations for this pre-purchase order
            QuotationComparison::where('pre_purchase_order_id', $quotation->pre_purchase_order_id)
                ->where('id', '!=', $this->selectedQuotationId)
                ->update(['is_selected' => false]);

            // Update the selected flag for this quotation
            $quotation->update([
                'is_selected' => true
            ]);

            // Update the pre-purchase order status to approved (not finalized yet)
            $quotation->prePurchaseOrder->update([
                'process_status' => 'approved',
                'grand_total' => $quotation->total_amount, // Using total_amount instead of grand_total
                'approved_by' => auth()->id()
            ]);

            DB::commit();

            $this->closeModal();
            $this->alert('success', 'Supplier selected successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error selecting supplier: ' . $e->getMessage());
            $this->alert('error', 'Error selecting supplier: ' . $e->getMessage());
            $this->closeModal();
        }
    }

    public function updateSelectedStatus()
    {
        if (empty($this->selected)) {
            $this->alert('warning', 'No items selected');
            return;
        }

        if (empty($this->newStatus)) {
            $this->alert('warning', 'Please select a status');
            return;
        }

        try {
            // If setting to approved, make sure there's a selected quotation
            if ($this->newStatus === 'approved') {
                $prePurchaseOrders = PrePurchaseOrder::whereIn('id', $this->selected)
                    ->with('quotations', function ($query) {
                        $query->where('is_selected', true);
                    })
                    ->get();

                foreach ($prePurchaseOrders as $po) {
                    if ($po->quotations->isEmpty()) {
                        $this->alert('warning', "Pre-Purchase Order {$po->pre_po_number} needs to have a selected supplier before setting to Approved status");
                        return;
                    }
                }
            }

            // Update the status
            PrePurchaseOrder::whereIn('id', $this->selected)->update([
                'process_status' => $this->newStatus,
                'updated_by' => auth()->id()
            ]);

            $this->alert('success', 'Status updated successfully');
            $this->selected = [];
            $this->selectAll = false;
            $this->newStatus = null;
            $this->closeModal();
        } catch (\Exception $e) {
            $this->alert('error', 'Error updating status: ' . $e->getMessage());
        }
    }

    public function delete()
    {
        try {
            // Load the pre-purchase order with its relationships
            $prePurchaseOrder = PrePurchaseOrder::with('quotations')->find($this->modelId);

            if (!$prePurchaseOrder) {
                $this->alert('error', 'Pre-Purchase Order not found');
                return;
            }

            // Check if the pre-purchase order is finalized or approved
            if (in_array($prePurchaseOrder->process_status, ['finalized', 'approved'])) {
                $this->alert('error', 'Cannot delete a finalized or approved Pre-Purchase Order');
                return;
            }

            // Delete associated quotations first
            foreach ($prePurchaseOrder->quotations as $quotation) {
                // Delete quotation details and additional costs
                $quotation->quotationDetails()->delete();
                $quotation->beforeTaxCosts()->delete();
                $quotation->afterTaxCosts()->delete();
                $quotation->delete();
            }

            // Now delete the pre-purchase order
            $prePurchaseOrder->delete();

            $this->alert('success', 'Pre-Purchase Order deleted successfully');
        } catch (\Exception $e) {
            $this->alert('error', 'Error deleting Pre-Purchase Order: ' . $e->getMessage());
        }
    }

    public function confirmDelete($id)
    {
        $this->modelId = $id;
        $this->confirm('Are you sure you want to delete this Pre-Purchase Order?', [
            'toast' => false,
            'position' => 'center',
            'showConfirmButton' => true,
            'confirmButtonText' => 'Yes, Delete',
            'cancelButtonText' => 'Cancel',
            'onConfirmed' => 'deleteConfirmed',
        ]);
    }

    // Misc
    public function closeModal()
    {
        $this->dispatchBrowserEvent('close-modal');
        $this->resetErrorBag();
        $this->resetCreateForm();
    }

    public function resetCreateForm()
    {
        $this->modelId = null;
        $this->newStatus = null;
        $this->selectedQuotationId = null;
    }

    public function updated($name)
    {
        if ($name == 'search') {
            $this->resetPage();
        }
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $query = PrePurchaseOrder::query();

            if (!empty($this->search)) {
                $query->where(function ($q) {
                    $q->where('pre_po_number', 'like', "%{$this->search}%")
                        ->orWhereHas('customerOrder', function ($q) {
                            $q->where('order_number', 'like', "%{$this->search}%");
                        })
                        ->orWhereHas('createdBy', function ($q) {
                            $q->where('name', 'like', "%{$this->search}%");
                        });
                });
            }

            $this->selected = $query->pluck('id')->toArray();
        } else {
            $this->selected = [];
        }
    }
}
