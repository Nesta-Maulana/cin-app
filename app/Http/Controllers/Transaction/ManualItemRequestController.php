<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Repositories\Transaction\ManualItemRequest\ManualItemRequestRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ManualItemRequestController extends Controller
{
    public $view, $route;
    protected $repository;
    public function __construct(ManualItemRequestRepositoryInterface $repository)
    {
        $this->repository = $repository;
        $this->view = 'transaction.manual-item-request';
        $this->route = 'manual-item-request';

        $this->middleware("can:create-{$this->route}")->only('create', 'store');
        $this->middleware("can:read-{$this->route}")->only('index');
        $this->middleware("can:update-{$this->route}")->only('edit', 'update');
        $this->middleware("can:delete-{$this->route}")->only('destroy');
    }

    public function index()
    {
        return view("{$this->view}.index");
    }

    public function create()
    {
        return view("{$this->view}.create");
    }

    public function downloadTemplate(Request $request)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set the headers and format them
        $headers = [
            'NO BOM',
            'PROYEK BAP',
            'TGL',
            'NAMA BARANG',
            'DESCRIPTION',
            'SPESIFICATION',
            'UNIT',
            'QTY'
        ];

        foreach (range('A', 'H') as $index => $column) {
            $sheet->setCellValue($column . '1', $headers[$index]);

            // Auto size columns
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Add some sample data (optional)
        $sampleData = [
            [
                'IR-2024-001',
                'CIN2404001',
                '25/03/2024',
                'Laptop Dell XPS 13',
                'Dell XPS 13 for programming',
                'Intel i7, 16GB RAM, 512GB SSD',
                'Unit',
                '2'
            ],
            [
                'IR-2024-001',
                'CIN2404001',
                '25/03/2024',
                'Monitor LG 27"',
                'Monitor for workstation',
                '27" 4K UHD, IPS Panel',
                'Unit',
                '2'
            ],
            [
                'IR-2024-002',
                'CIN2404002',
                '26/03/2024',
                'Office Chair',
                'Executive Office Chair',
                'Ergonomic with lumbar support',
                'Unit',
                '5'
            ],
        ];

        // Add the sample data
        $row = 2;
        foreach ($sampleData as $data) {
            foreach (range('A', 'H') as $index => $column) {
                $sheet->setCellValue($column . $row, $data[$index]);
            }
            $row++;
        }

        // Style the header row
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ];

        $sheet->getStyle('A1:H1')->applyFromArray($headerStyle);

        // Add cell format/validation for the date column (TGL)
        $sheet->getStyle('C2:C' . ($row - 1))->getNumberFormat()
            ->setFormatCode('DD/MM/YYYY');

        // Add cell format for the quantity column (QTY)
        $sheet->getStyle('H2:H' . ($row - 1))->getNumberFormat()
            ->setFormatCode('#,##0.00');

        // Set the data rows style
        $dataStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ];

        if ($row > 2) {
            $sheet->getStyle('A2:H' . ($row - 1))->applyFromArray($dataStyle);
        }

        // Create a temporary file
        $fileName = 'manual_item_request_template.xlsx';
        $tempPath = storage_path('app/public/' . $fileName);

        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        return response()->download($tempPath, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            //
        ]);
        try {
            DB::transaction(function () use ($request, $data) {
                $this->repository->create($data);
            });
            alertNotif('save');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            alertNotif('error', $e->getMessage());
        }
        return redirect()->route("{$this->route}.index");
    }

    public function edit($id)
    {
        try {
            $data = $this->repository->find($id);
            return view("{$this->view}.edit", compact('data'));
        } catch (Exception $e) {
            Log::error($e->getMessage());
            alertNotif('error', $e->getMessage());
            return redirect()->route("{$this->route}.index");
        }
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            //
        ]);
        $data['updated_by'] = auth()->user()->id;
        try {
            DB::transaction(function () use ($request, $id, $data) {
                $this->repository->update($id, $data);
            });
            alertNotif('update');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            alertNotif('error', $e->getMessage());
        }
        return redirect()->route("{$this->route}.index");
    }

    public function destroy($id)
    {
        try {
            $repository = $this->repository->find($id);
            $repository->delete();
            alertNotif('delete');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            alertNotif('error', $e->getMessage());
        }
        return redirect()->route("{$this->route}.index");
    }
}
