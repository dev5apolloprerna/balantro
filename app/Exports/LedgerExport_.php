<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class LedgerExport implements FromCollection, WithHeadings, WithTitle, WithStyles, WithEvents
{
    protected $data;
    protected $from;
    protected $to;
    protected $groupName;
    protected $customerName;
    protected $partyName;
    protected $companyAddress;
    protected $companyEmail;

    public function __construct($data, $from, $to, $groupName = '', $customerName = '',$partyName = '',$companyAddress = '',$companyEmail = '')
    {
        $this->data = $data;
        $this->from = $from;
        $this->to = $to;
        $this->groupName = $groupName;
        $this->customerName = $customerName;
        $this->partyName = $partyName;
        $this->companyAddress = $companyAddress;
        $this->companyEmail = $companyEmail;
    }

    public function collection()
    {
        $rows = collect($this->data['rows'] ?? []);

        $toFloat = function ($v) {
            if ($v === null || $v === '') {
                return 0.0;
            }

            return (float) str_replace(',', '', (string) $v);
        };

        $inr = function ($num) {
            return number_format(abs($num), 2);
        };

        $filteredRows = $rows->filter(function ($r) use ($toFloat) {

            $op = $toFloat($r->decOpBl ?? 0);
            $dr = $toFloat($r->decDr ?? 0);
            $cr = $toFloat($r->decCr ?? 0);
            $cl = $toFloat($r->decClBl ?? 0);

            return !($op == 0 && $dr == 0 && $cr == 0 && $cl == 0);
        });

        $grouped = $filteredRows->groupBy('strParents');
        // $grouped = $filteredRows
        //     ->sortBy('strParents')
        //     ->groupBy('strParents');

        $final = collect();

        foreach ($grouped as $parent => $ledgers) {

            // GROUP HEADER
            $final->push([
                'Ledger' => strtoupper($parent ?: 'UNGROUPED'),
                'Parent' => '',
                'Opening' => '',
                'DR' => '',
                'CR' => '',
                'Closing' => '',
            ]);

            $gOp = 0;
            $gDr = 0;
            $gCr = 0;
            $gCl = 0;

            foreach ($ledgers as $item) {

                $opening = $toFloat($item->decOpBl ?? 0);
                $closing = $toFloat($item->decClBl ?? 0);

                $drAmount = abs($toFloat($item->decDr ?? 0));
                $crAmount = abs($toFloat($item->decCr ?? 0));

                $gOp += $opening;
                $gDr += $drAmount;
                $gCr += $crAmount;
                $gCl += $closing;

                $final->push([
                    'Ledger' => $item->strCustomerName ?? '',
                    'Parent' => $item->strParents ?? '',

                    'Opening' =>
                        abs($opening) > 0
                            ? $inr($opening) . ' ' . ($opening < 0 ? 'Dr' : 'Cr')
                            : '0.00',

                    'DR' => $inr($drAmount),

                    'CR' => $inr($crAmount),

                    'Closing' =>
                        abs($closing) > 0
                            ? $inr($closing) . ' ' . ($closing < 0 ? 'Dr' : 'Cr')
                            : '0.00',
                ]);
            }

            // GROUP TOTAL
            $final->push([
                'Ledger' => 'Total',
                'Parent' => '',

                'Opening' =>
                    abs($gOp) > 0
                        ? $inr($gOp) . ' ' . ($gOp < 0 ? 'Dr' : 'Cr')
                        : '0.00',

                'DR' => $inr($gDr),

                'CR' => $inr($gCr),

                'Closing' =>
                    abs($gCl) > 0
                        ? $inr($gCl) . ' ' . ($gCl < 0 ? 'Dr' : 'Cr')
                        : '0.00',
            ]);

            // EMPTY SPACE ROW
            $final->push([
                'Ledger' => '',
                'Parent' => '',
                'Opening' => '',
                'DR' => '',
                'CR' => '',
                'Closing' => '',
            ]);
        }

        return $final;
    }

    public function headings(): array
    {
        return [
            'Ledger',
            'Parent',
            // 'Side',
            'Opening',
            'DR',
            'CR',
            'Closing'
        ];
    }

    public function title(): string
    {
        return 'Ledger Report';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]], // Header row
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;
                $lastRow = $sheet->getHighestRow();

                for ($i = 7; $i <= $lastRow; $i++) {

                    $ledger = trim((string) $sheet->getCell("A{$i}")->getValue());
                    $parent = trim((string) $sheet->getCell("B{$i}")->getValue());

                    // Group Header Row
                    if ($ledger && empty($parent)
                        && empty($sheet->getCell("C{$i}")->getValue())) {

                        $sheet->mergeCells("A{$i}:F{$i}");

                        $sheet->getStyle("A{$i}:F{$i}")
                            ->getFont()
                            ->setBold(true);

                        $sheet->getStyle("A{$i}:F{$i}")
                            ->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setARGB('FF1F2937');
                        $sheet->getStyle("A{$i}:F{$i}")
                            ->getFont()
                            ->getColor()
                            ->setARGB('FFFFFFFF');
                    }

                    // Total Row
                    if ($ledger === 'Total') {

                        $sheet->getStyle("A{$i}:F{$i}")
                            ->getFont()
                            ->setBold(true);

                        $sheet->getStyle("A{$i}:F{$i}")
                            ->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setARGB('FFF2F2F2');
                    }
                }
                // Add title rows above the data
                // $sheet->insertNewRowBefore(1, 2);
                // $sheet->mergeCells('A1:F1');
                // $sheet->mergeCells('A2:F2');
                // $sheet->setCellValue('A1', 'Ballantro - Ledger Report');
                // $sheet->setCellValue('A2', 'Period: ' . $this->from . ' to ' . $this->to);

                $sheet->insertNewRowBefore(1, 5);

                $sheet->mergeCells('A1:F1');
                $sheet->mergeCells('A2:F2');
                $sheet->mergeCells('A3:F3');
                $sheet->mergeCells('A4:F4');
                $sheet->mergeCells('A5:F5');

                $sheet->setCellValue('A1', strtoupper($this->partyName ?: 'COMPANY NAME'));
                $sheet->setCellValue('A2', $this->companyAddress ?: '');
                $sheet->setCellValue('A3', 'E-Mail : ' . ($this->companyEmail ?: ''));
                $sheet->setCellValue('A4', 'Ledger Report');

                $sheet->setCellValue(
                    'A5',
                    date('d-M-y', strtotime($this->from))
                    . ' to ' .
                    date('d-M-y', strtotime($this->to))
                );
                

                // Style title rows
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(18);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->getStyle('A2')->getFont()->setSize(11);
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A2')->getAlignment()->setWrapText(true);

                $sheet->getStyle('A3')->getFont()->setSize(11);
                $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->getStyle('A4')->getFont()->setBold(true)->setSize(15);
                $sheet->getStyle('A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->getStyle('A5')->getFont()->setSize(11);
                $sheet->getStyle('A5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->getRowDimension(2)->setRowHeight(35);

                // Format Opening, CR, DR, Closing columns with Indian number format
                $amountColumns = ['D', 'E', 'F', 'G'];
                foreach ($amountColumns as $column) {
                    $sheet->getStyle($column . '7:' . $column . $lastRow)
                        ->getNumberFormat()
                        ->setFormatCode('#,##0.00');
                }

                // Auto-size columns
                foreach (range('A', 'F') as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }

                // Style the header row (row 3 after inserting titles)
                $headerStyle = $sheet->getStyle('A6:F6');
                $headerStyle->getFont()->setBold(true);
                $headerStyle->getFill()->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FF4F81BD'); // Blue background
                $headerStyle->getFont()->getColor()->setARGB('FFFFFFFF'); // White text

                // Center align Side column
                $sheet->getStyle('C7:C' . $lastRow)
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Right align amount columns
                $sheet->getStyle('C7:F' . $lastRow)
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                // Add borders to all cells
                $sheet->getStyle('A6:F' . $lastRow)
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);


                // ================= SUMMARY =================

                // $summaryStart = $lastRow + 3;

                // // Calculate totals
                // $grandOpening = 0;
                // $grandDr = 0;
                // $grandCr = 0;
                // $grandClosing = 0;

                // $rows = collect($this->data['rows'] ?? []);

                // foreach ($rows as $r) {

                //     $opening = (float) str_replace(',', '', ($r->decOpBl ?? 0));
                //     $dr = abs((float) str_replace(',', '', ($r->decDr ?? 0)));
                //     $cr = abs((float) str_replace(',', '', ($r->decCr ?? 0)));
                //     $closing = (float) str_replace(',', '', ($r->decClBl ?? 0));

                //     $grandOpening += $opening;
                //     $grandDr += $dr;
                //     $grandCr += $cr;
                //     $grandClosing += $closing;
                // }

                // $inr = function ($num) {
                //     return number_format(abs($num), 2);
                // };

                // // Summary Header
                // $sheet->mergeCells("A{$summaryStart}:F{$summaryStart}");

                // $sheet->setCellValue("A{$summaryStart}", 'SUMMARY');

                // $sheet->getStyle("A{$summaryStart}:F{$summaryStart}")
                //     ->getFont()
                //     ->setBold(true);

                // $sheet->getStyle("A{$summaryStart}:F{$summaryStart}")
                //     ->getFill()
                //     ->setFillType(Fill::FILL_SOLID)
                //     ->getStartColor()
                //     ->setARGB('FF1F2937');

                // $sheet->getStyle("A{$summaryStart}:F{$summaryStart}")
                //     ->getFont()
                //     ->getColor()
                //     ->setARGB('FFFFFFFF');

                // // Opening
                // $sheet->setCellValue(
                //     "A" . ($summaryStart + 1),
                //     'Opening Balance'
                // );

                // $sheet->setCellValue(
                //     "B" . ($summaryStart + 1),
                //     abs($grandOpening) > 0
                //         ? $inr($grandOpening) . ' ' . ($grandOpening < 0 ? 'Dr' : 'Cr')
                //         : '0.00'
                // );

                // // DR
                // $sheet->setCellValue(
                //     "C" . ($summaryStart + 1),
                //     'Total Debit'
                // );

                // $sheet->setCellValue(
                //     "D" . ($summaryStart + 1),
                //     $inr($grandDr)
                // );

                // // CR
                // $sheet->setCellValue(
                //     "A" . ($summaryStart + 2),
                //     'Total Credit'
                // );

                // $sheet->setCellValue(
                //     "B" . ($summaryStart + 2),
                //     $inr($grandCr)
                // );

                // // Closing
                // $sheet->setCellValue(
                //     "C" . ($summaryStart + 2),
                //     'Closing Balance'
                // );

                // $sheet->setCellValue(
                //     "D" . ($summaryStart + 2),
                //     abs($grandClosing) > 0
                //         ? $inr($grandClosing) . ' ' . ($grandClosing < 0 ? 'Dr' : 'Cr')
                //         : '0.00'
                // );

                // // Style summary area
                // $sheet->getStyle("A{$summaryStart}:D" . ($summaryStart + 2))
                //     ->getBorders()
                //     ->getAllBorders()
                //     ->setBorderStyle(Border::BORDER_THIN);

                // $sheet->getStyle("A" . ($summaryStart + 1) . ":D" . ($summaryStart + 2))
                //     ->getFont()
                //     ->setBold(true);
            },
        ];
    }

}
