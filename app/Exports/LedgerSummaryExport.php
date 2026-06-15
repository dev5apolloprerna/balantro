<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LedgerSummaryExport implements FromCollection, WithHeadings, WithTitle, WithStyles, WithEvents
{
    protected $data;
    protected $from;
    protected $to;

    public function __construct($data, $from, $to)
    {
        $this->data = $data;
        $this->from = $from;
        $this->to = $to;
    }

    public function collection()
    {
        $ledgers = $this->data['by_ledger'] ?? [];

        return collect($ledgers)->map(function ($ledger) {
            return [
                'Ledger Name' => $ledger['ledger_name'] ?? '',
                'Total Debit' => $ledger['total_dr'] ?? '0.00',
                'Total Credit' => $ledger['total_cr'] ?? '0.00',
                'Closing Balance' => $ledger['closing'] ?? '0.00',
            ];
        });
    }

    public function headings(): array
    {
        return [
            ['Ledger Summary Report'],
            [],
            ['Period:', $this->from . ' to ' . $this->to],
            [],
            ['Ledger Name', 'Total Debit', 'Total Credit', 'Closing Balance']
        ];
    }

    public function title(): string
    {
        return 'Ledger Summary';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 16]],
            5 => ['font' => ['bold' => true]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->mergeCells('A1:D1');

                $lastRow = count($this->data['by_ledger'] ?? []) + 6;

                // Add grand totals
                $event->sheet->setCellValue('A' . ($lastRow + 1), 'Grand Total:');
                $event->sheet->setCellValue('B' . ($lastRow + 1), $this->data['grand_dr'] ?? '0.00');
                $event->sheet->setCellValue('C' . ($lastRow + 1), $this->data['grand_cr'] ?? '0.00');
                $event->sheet->setCellValue('D' . ($lastRow + 1), $this->data['grand_diff'] ?? '0.00');

                $event->sheet->getStyle('A' . ($lastRow + 1) . ':D' . ($lastRow + 1))
                    ->getFont()
                    ->setBold(true);

                foreach (range('A', 'D') as $column) {
                    $event->sheet->getColumnDimension($column)->setAutoSize(true);
                }
            },
        ];
    }
}
