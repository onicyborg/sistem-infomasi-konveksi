<?php

namespace App\Exports;

use App\Models\PurchaseOrders;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PurchaseOrdersExport implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        // Mengambil data dengan join ke tabel 'customers'
        $orders = PurchaseOrders::select(
            'customers.name as customer_name',
            'customers.phone_number',
            'customers.address',
            'purchase_orders.po_number',
            'purchase_orders.description',
            'purchase_orders.order_date',
            'purchase_orders.deadline_date',
            'purchase_orders.raw_material_quantity',
            'purchase_orders.size_s',
            'purchase_orders.size_m',
            'purchase_orders.size_l',
            'purchase_orders.size_xl',
            'purchase_orders.total_price'
        )
            ->join('customers', 'purchase_orders.customer_id', '=', 'customers.id');

        // Filter berdasarkan tanggal jika ada
        if ($this->startDate && $this->endDate) {
            $orders->whereBetween('purchase_orders.created_at', [$this->startDate, $this->endDate]);
        }

        return $orders->get()->map(function ($order) {
            // Menambahkan satuan 'yard' pada raw_material_quantity
            $order->raw_material_quantity = $order->raw_material_quantity . ' yard';

            return $order;
        });
    }

    // Define the headings for the export file
    public function headings(): array
    {
        return [
            'Customer Name',      // Column 1
            'Phone Number',       // Column 2
            'Address',            // Column 3
            'PO Number',          // Column 4
            'Description',        // Column 5
            'Order Date',         // Column 6
            'Deadline Date',      // Column 7
            'Raw Material Quantity (yard)', // Column 8
            'Size S',             // Column 9
            'Size M',             // Column 10
            'Size L',             // Column 11
            'Size XL',            // Column 12
            'Total Price',        // Column 13
        ];
    }

    // Styling the header row
    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                    'color' => ['argb' => 'FFFFFF'],
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => '4F81BD'],
                ],
            ],
        ];
    }

    // Set the title for the worksheet
    public function title(): string
    {
        return 'Purchase Orders';  // You can set a title here
    }

    public function afterSheet(Worksheet $sheet)
    {
        // Auto size the columns based on content
        foreach (range('A', 'M') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
    }
}
