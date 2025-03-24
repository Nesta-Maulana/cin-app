<?php

namespace App\Imports;

use App\Models\Customer;
use App\Models\CustomerOrder;
use App\Models\ManualItemRequest;
use App\Models\ManualItemRequestDetail;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Carbon\Carbon;

class ManualItemRequestImport implements ToCollection, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        DB::beginTransaction();

        try {
            $currentRequestNumber = null;
            $itemRequestId = null;
            $importedRequests = [];

            foreach ($rows as $row) {
                // Skip empty rows
                if (empty($row['no_bom']) && empty($row['proyek_bap']) && empty($row['nama_barang'])) {
                    continue;
                }

                // If we have a new request number, create a new item request
                if (!empty($row['no_bom']) && $currentRequestNumber !== $row['no_bom']) {
                    $currentRequestNumber = $row['no_bom'];

                    // Check if this request number already exists
                    $existingRequest = ManualItemRequest::where('request_number', $currentRequestNumber)->first();

                    if ($existingRequest) {
                        // Use existing request
                        $itemRequestId = $existingRequest->id;
                    } else {
                        // Find or create the customer
                        $customer = $this->findOrCreateCustomer($row['proyek_bap']);

                        // Create new item request
                        $itemRequest = ManualItemRequest::create([
                            'request_number' => $currentRequestNumber,
                            'customer_order_id' => $customer->id,
                            'request_date' => $this->parseDate($row['tgl']),
                            'status' => 'pending',
                            'notes' => $row['notes'] ?? null
                        ]);

                        $itemRequestId = $itemRequest->id;
                        $importedRequests[] = $itemRequest->request_number;
                    }
                }
                // Only add item details if we have a valid item request
                if ($itemRequestId && !empty($row['nama_barang'])) {
                    ManualItemRequestDetail::create([
                        'manual_item_request_id' => $itemRequestId,
                        'item_name' => $row['nama_barang'] ?? '',
                        'description' => $row['description'] ?? '',
                        'specification' => $row['spesification'] ?? '',
                        'unit' => $row['unit'] ?? '',
                        'quantity' => $this->parseQuantity($row['qty'] ?? 0)
                    ]);
                }
            }

            DB::commit();

            // Log success
            Log::info('Successfully imported ' . count($importedRequests) . ' item requests: ' . implode(', ', $importedRequests));

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Import error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            throw $e;
        }
    }

    /**
     * Get the validation rules that apply to the import.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            '*.no_bom' => 'required|string',
            '*.proyek_bap' => 'required|string',
            '*.tgl' => 'required',
            '*.nama_barang' => 'required|string',
            '*.unit' => 'required|string',
            '*.qty' => 'required',
        ];
    }

    /**
     * Custom messages for validation failures.
     *
     * @return array
     */
    public function customValidationMessages()
    {
        return [
            '*.no_bom.required' => 'The NO BOM field is required.',
            '*.proyek_bap.required' => 'The PROYEK BAP field is required.',
            '*.tgl.required' => 'The TGL (date) field is required.',
            '*.nama_barang.required' => 'The NAMA BARANG field is required.',
            '*.unit.required' => 'The UNIT field is required.',
            '*.qty.required' => 'The QTY field is required.',
        ];
    }

    /**
     * Find or create a customer by customer code
     *
     * @param string $customerCode
     * @return Customer
     */
    private function findOrCreateCustomer($customerCode)
    {
        if (empty($customerCode)) {
            throw new \Exception('Customer code (PROYEK BAP) is required');
        }

        $customer = CustomerOrder::where(
            ['order_number' => $customerCode]
        )->first();

        return $customer;
    }

    /**
     * Parse date from different formats
     *
     * @param mixed $date
     * @return \Carbon\Carbon
     */
    private function parseDate($date)
    {
        if (empty($date)) {
            return now();
        }

        // Try to parse different date formats
        try {
            // Handle Excel numeric dates
            if (is_numeric($date)) {
                // Excel dates are days since 1900-01-01 (with a leap year bug)
                return Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($date));
            }

            // Handle European date format (DD/MM/YYYY)
            if (is_string($date) && preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $date)) {
                list($day, $month, $year) = explode('/', $date);
                return Carbon::createFromDate($year, $month, $day);
            }

            // Try to parse standard date formats
            return Carbon::parse($date);
        } catch (\Exception $e) {
            // If all parsing fails, use current date
            Log::warning('Could not parse date: ' . $date . '. Using current date instead.');
            return now();
        }
    }

    /**
     * Parse quantity to ensure it's a valid number
     *
     * @param mixed $qty
     * @return float
     */
    private function parseQuantity($qty)
    {
        // If it's a string with commas as decimal separator, convert to dot
        if (is_string($qty)) {
            $qty = str_replace(',', '.', $qty);
        }

        // Convert to float
        $quantity = (float) $qty;

        // Ensure it's a positive number
        return max(0, $quantity);
    }
}
