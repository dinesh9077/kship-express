<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class BillingInvoiceExport implements FromCollection, WithHeadings, WithEvents, ShouldAutoSize
{
    protected $invoice;
    protected $rows;

    const TAX_DIVISOR = 1.18;
    const CGST_RATE = 0.09;
    const SGST_RATE = 0.09;
    const IGST_RATE = 0.18;

    public function __construct($invoice)
    {
        $this->invoice = $invoice;
        $this->rows = collect(); // store rows for total calculation
    }

    public function collection()
    {
        $this->rows = $this->invoice->orders->map(function ($order) {
            $shipping = $order->shipping_charge ?? 0;
            $base = round($shipping / self::TAX_DIVISOR, 2);
            $cgst = $sgst = $igst = 0;
            $state = strtolower($this->invoice->invoice_state ?? 'gujarat');

            if ($state === 'gujarat') {
                $cgst = round($base * self::CGST_RATE, 2);
                $sgst = round($base * self::SGST_RATE, 2);
            } else {
                $igst = round($base * self::IGST_RATE, 2);
            }

            return [
                'Order No'     => $order->manual_order_id,
                'AWB Number'   => $order->awb_number,
                'Username'     => optional($order->user)->name ?? '',
                'State'        => ucfirst($state),
                'Base Amount'  => $base,
                'CGST'         => $cgst,
                'SGST'         => $sgst,
                'IGST'         => $igst,
                'Total Amount' => $shipping,
            ];
        });

        return $this->rows;
    }

    public function headings(): array
    {
        return [
            'Order No',
            'AWB Number',
            'Username',
            'State',
            'Base Amount',
            'CGST',
            'SGST',
            'IGST',
            'Total Amount',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $rowCount = $this->rows->count() + 1; // +1 for heading row

                // Calculate totals
                $baseSum  = $this->rows->sum('Base Amount');
                $cgstSum  = $this->rows->sum('CGST');
                $sgstSum  = $this->rows->sum('SGST');
                $igstSum  = $this->rows->sum('IGST');
                $totalSum = $this->rows->sum('Total Amount');

                $sheet = $event->sheet;

                // Set total row
                $sheet->setCellValue('A' . ($rowCount + 1), 'TOTAL');
                $sheet->mergeCells("A" . ($rowCount + 1) . ":D" . ($rowCount + 1));
                $sheet->setCellValue('E' . ($rowCount + 1), $baseSum);
                $sheet->setCellValue('F' . ($rowCount + 1), $cgstSum);
                $sheet->setCellValue('G' . ($rowCount + 1), $sgstSum);
                $sheet->setCellValue('H' . ($rowCount + 1), $igstSum);
                $sheet->setCellValue('I' . ($rowCount + 1), $totalSum);

                // Bold total row
                $sheet->getStyle("A" . ($rowCount + 1) . ":I" . ($rowCount + 1))->getFont()->setBold(true);
            },
        ];
    }
}
