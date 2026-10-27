<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class VoucherHistoryExport implements FromCollection, WithHeadings, WithTitle, WithStyles, WithEvents
{
    protected $data;
    protected $from;
    protected $to;
    protected $ledgerId;
    protected $openingBalance;
    protected $closingBalance;
    protected $totalDr;
    protected $totalCr;
    protected $partyName;
    protected $companyAddress;
    protected $companyEmail;

    public function __construct($data, $from, $to, $ledgerId, $openingBalance, $closingBalance, $totalDr, $totalCr,$partyName,$companyAddress,$companyEmail)
    {
        $this->data = $data;
        $this->from = $from;
        $this->to = $to;
        $this->ledgerId = $ledgerId;
        $this->openingBalance = $openingBalance;
        $this->closingBalance = $closingBalance;
        $this->totalDr = $totalDr;
        $this->totalCr = $totalCr;

        $this->partyName = $partyName;
        $this->companyAddress = $companyAddress;
        $this->companyEmail = $companyEmail;
    }

    public function collection()
    {
        $processedRows = $this->data['processedRows'] ?? [];

        return collect($processedRows)->map(function ($row) {
            // Check if this is an opening/closing balance row
            $isOpening = $row->is_opening ?? false;
            $isClosing = $row->is_closing ?? false;
            $isSpecialRow = $isOpening || $isClosing;

            $drAmount = abs($row->DrAmount ?? 0);
            $crAmount = abs($row->CrAmount ?? 0);
            // $opening = $row->opening_balance ?? ($row->decRunningBalance ?? 0);
            // $closing = $row->decRunningBalance ?? 0;
            // $side = $row->side ?? ($closing >= 0 ? 'Dr' : 'Cr');
            $openingRaw = (float)($row->opening_balance ?? 0);
            $closingRaw = (float)($row->decRunningBalance ?? 0);

            $openingSide = $openingRaw < 0 ? 'Dr' : 'Cr';
            $closingSide = $closingRaw < 0 ? 'Dr' : 'Cr';
            return [
                'Date' => date('d-m-Y', strtotime($row->strVchDate)) ?? '',
                'Voucher No' => $row->vchNo ?? '',
                'Type' => $row->vchType ?? '',
                'Account' => $row->trnAccount ?? '',
                'Opening' => number_format(abs($openingRaw), 2) . ' ' . $openingSide,

                'Dr' => $isSpecialRow ? '0.00' : number_format($drAmount, 2),
                'Cr' => $isSpecialRow ? '0.00' : number_format($crAmount, 2),
                'Closing' => number_format(abs($closingRaw), 2) . ' ' . $closingSide
            ];
        });
    }

    public function headings(): array
    {
        // return [
        //     ['Balantro - Voucher History Report'],
        //     ['Ledger #: ' . $this->ledgerId],
        //     ['Period: ' . ($this->from ? date('d-m-Y', strtotime($this->from)) : '') . ' to ' . ($this->to ? date('d-m-Y', strtotime($this->to)) : '')],
        //     [],
        //     ['Date', 'Voucher No', 'Type', 'Account', 'Opening', 'Dr', 'Cr', 'Closing', 'Side']
        // ];
        return [
            [strtoupper($this->partyName)],
            [$this->companyAddress],
            ['E-Mail : ' . $this->companyEmail],
            ['Ledger History'],
            [
                ($this->from ? date('d-M-y', strtotime($this->from)) : '')
                . ' to ' .
                ($this->to ? date('d-M-y', strtotime($this->to)) : '')
            ],
            [],
            ['Date', 'Voucher No', 'Type', 'Account', 'Opening', 'Dr', 'Cr', 'Closing']
        ];
    }

    public function title(): string
    {
        return 'Ledger History';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 16]],
            2 => ['font' => ['bold' => true, 'size' => 12]],
            3 => ['font' => ['bold' => true, 'size' => 12]],
            5 => ['font' => ['bold' => true]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;

                // Merge title cells
                // $sheet->mergeCells('A1:I1');
                // $sheet->mergeCells('A2:I2');
                // $sheet->mergeCells('A3:I3');
                $sheet->mergeCells('A1:H1');
                $sheet->mergeCells('A2:H2');
                $sheet->mergeCells('A3:H3');
                $sheet->mergeCells('A4:H4');
                $sheet->mergeCells('A5:H5');

                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(18);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->getStyle('A2')->getFont()->setSize(11);
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->getStyle('A3')->getFont()->setSize(11);
                $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->getStyle('A4')->getFont()->setBold(true)->setSize(15);
                $sheet->getStyle('A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->getStyle('A5')->getFont()->setSize(11);
                $sheet->getStyle('A5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Add summary section
                $lastRow = count($this->data['processedRows'] ?? []) + 7;

                $sheet->setCellValue('A' . ($lastRow + 1), 'Summary:');
                $processedRows = $this->data['processedRows'] ?? [];

                $firstRow = collect($processedRows)->first();
                $lastRowData = collect($processedRows)->last();

                $openingRaw = (float)($firstRow->opening_balance ?? 0);
                $closingRaw = (float)($lastRowData->decRunningBalance ?? 0);

                $openingSide = $openingRaw < 0 ? 'Dr' : 'Cr';
                $closingSide = $closingRaw < 0 ? 'Dr' : 'Cr';

                $openingBalance = abs($openingRaw);
                $closingBalance = abs($closingRaw);

                $difference =
                    $openingRaw
                    - abs((float)$this->totalDr)
                    + abs((float)$this->totalCr)
                    - $closingRaw;

                $sheet->setCellValue('A' . ($lastRow + 2), 'Opening Balance:');
                $sheet->setCellValue(
                    'B' . ($lastRow + 2),
                    number_format($openingBalance, 2) . ' ' . $openingSide
                );

                $sheet->setCellValue('A' . ($lastRow + 3), 'Total Debit:');
                $sheet->setCellValue(
                    'B' . ($lastRow + 3),
                    number_format(abs((float)$this->totalDr), 2)
                );

                $sheet->setCellValue('A' . ($lastRow + 4), 'Total Credit:');
                $sheet->setCellValue(
                    'B' . ($lastRow + 4),
                    number_format(abs((float)$this->totalCr), 2)
                );

                $sheet->setCellValue('A' . ($lastRow + 5), 'Closing Balance:');
                $sheet->setCellValue(
                    'B' . ($lastRow + 5),
                    number_format($closingBalance, 2) . ' ' . $closingSide
                );

                $sheet->setCellValue('A' . ($lastRow + 6), 'Difference:');
                $sheet->setCellValue(
                    'B' . ($lastRow + 6),
                    number_format(abs($difference), 2)
                    . ' ' .
                    ($difference < 0 ? 'Cr' : 'Dr')
                );
                // $sheet->setCellValue('A' . ($lastRow + 2), 'Opening Balance:');
                // $sheet->setCellValue('B' . ($lastRow + 2), number_format(abs((float)$this->openingBalance), 2));

                // $sheet->setCellValue('A' . ($lastRow + 3), 'Total Debit:');
                // $sheet->setCellValue('B' . ($lastRow + 3), number_format(abs((float)$this->totalDr), 2));

                // $sheet->setCellValue('A' . ($lastRow + 4), 'Total Credit:');
                // $sheet->setCellValue('B' . ($lastRow + 4), number_format(abs((float)$this->totalCr), 2));

                // $sheet->setCellValue('A' . ($lastRow + 5), 'Closing Balance:');
                // $sheet->setCellValue('B' . ($lastRow + 5), number_format(abs((float)$this->closingBalance), 2));

                // $sheet->setCellValue('A' . ($lastRow + 6), 'Difference:');
                // $sheet->setCellValue('B' . ($lastRow + 6), number_format(abs((float)$this->totalDr) - abs((float)$this->totalCr), 2));

                // Style summary section
                $summaryStyle = [
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F2F2F2']],
                ];
                $sheet->getStyle('A' . ($lastRow + 1) . ':B' . ($lastRow + 6))->applyFromArray($summaryStyle);

                // Auto-size columns
                foreach (range('A', 'H') as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }

                // Add borders to data
                $dataRowCount = count($this->data['processedRows'] ?? []);
                if ($dataRowCount > 0) {
                    $sheet->getStyle('A5:H' . ($dataRowCount + 5))->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => '000000'],
                            ],
                        ],
                    ]);
                }

                // Style header row
                $headerStyle = [
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F81BD']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ];
                $sheet->getStyle('A7:H7')->applyFromArray($headerStyle);

                // Right align numeric columns
                if ($dataRowCount > 0) {
                    $sheet->getStyle('E5:H' . ($dataRowCount + 7))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                }
            },
        ];
    }
}
