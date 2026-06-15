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
use PhpOffice\PhpSpreadsheet\Style\Color;

class BalanceSheetExport implements FromCollection, WithHeadings, WithTitle, WithStyles, WithEvents
{
    protected $data;
    protected $from;
    protected $to;
    protected $partyName;
    protected $companyAddress;
    protected $companyEmail;

    public function __construct($data, $from, $to, $partyName, $companyAddress, $companyEmail)
    {
        $this->data = $data;
        $this->from = $from;
        $this->to   = $to;

        $this->partyName = $partyName;
        $this->companyAddress = $companyAddress;
        $this->companyEmail   = $companyEmail;
    }

    public function collection()
    {
        return collect();
    }

    public function headings(): array
    {
        return [
            [strtoupper($this->partyName ?? 'COMPANY NAME')],
            [$this->companyAddress ?? ''],
            ['E-Mail : ' . ($this->companyEmail ?? '')],
            ['Balance Sheet'],
            [
                date('d-M-y', strtotime($this->from))
                . ' to ' .
                date('d-M-y', strtotime($this->to))
            ],
            [],
        ];
    }

    public function title(): string
    {
        return 'Balance Sheet';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 16]],
            2 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet;
                $sheet->mergeCells('A1:C1');
                $sheet->mergeCells('A2:C2');

                $row = 4;

                $row = $this->addAssets($sheet, $row);
                $row += 2;
                $row = $this->addLiabilities($sheet, $row);
                $row += 2;
                $this->addBalanceCheck($sheet, $row);

                foreach (['A','B','C'] as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            }
        ];
    }

    /* ======================================================
       HELPERS
       ====================================================== */

    private function format($v)
    {
        return number_format((float)$v, 2);
    }

    /* ======================================================
       ASSETS (DR SIDE)
       ====================================================== */

    private function addAssets($sheet, $row)
    {
        $sheet->setCellValue("A$row", 'Assets (Dr)');
        $sheet->getStyle("A$row")->getFont()->setBold(true);
        $row++;

        $rows   = collect($this->data['rows'] ?? []);
        $totals = $this->data['totals'] ?? [];

        $drRows = $rows->where('Side', 'DR');

        $totalDr = 0;
        $fixedOrder = ['Fixed Assets','Investments','Current Assets'];
        $printed = [];

        /* STEP 1: FIXED */
        foreach ($fixedOrder as $grp) {

            foreach ($drRows->where('strGroupName', $grp) as $r) {

                $amount = (float)($r->decMainAmount ?? 0);
                $amt = $amount > 0 ? -1 * $amount : abs($amount);

                $sheet->setCellValue("A$row", $r->strGroupName);
                $sheet->setCellValue("C$row", $this->format($amt));
                $sheet->getStyle("C$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                $totalDr += $amt;
                $printed[] = $r->strGroupName;
                $row++;
            }
        }

        /* STEP 2: REMAINING */
        foreach ($drRows as $r) {

            if (!in_array($r->strGroupName, $printed)) {

                $amount = (float)($r->decMainAmount ?? 0);
                $amt = $amount > 0 ? -1 * $amount : abs($amount);

                $sheet->setCellValue("A$row", $r->strGroupName);
                $sheet->setCellValue("C$row", $this->format($amt));
                $sheet->getStyle("C$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                $totalDr += $amt;
                $row++;
            }
        }
        // foreach ($drRows as $r) {

        //     $amount = (float)($r->decMainAmount ?? 0);

        //     // SAME SIGN LOGIC AS BLADE
        //     if ($amount > 0) {
        //         $amt = -1 * $amount;
        //     } else {
        //         $amt = abs($amount);
        //     }

        //     $sheet->setCellValue("A$row", $r->strGroupName ?? '-');
        //     $sheet->setCellValue("C$row", $this->format($amt));
        //     $sheet->getStyle("C$row")
        //           ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        //     $totalDr += $amt;
        //     $row++;
        // }

        /* Closing Stock */
        $closingStock = abs((float)($totals['closing_stock'] ?? 0));

        if ($closingStock > 0) {
            $sheet->setCellValue("A$row", 'Closing Stock');
            $sheet->setCellValue("C$row", $this->format($closingStock));
            $sheet->getStyle("A$row:C$row")->getFont()->setBold(true)
                  ->getColor()->setARGB(Color::COLOR_DARKGREEN);

            $sheet->getStyle("C$row")
                  ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            $totalDr += $closingStock;
            $row++;
        }
        /* ============================================
        DIFFERENCE ON ASSET SIDE
        ============================================ */

        $rowsAll = collect($this->data['rows'] ?? []);
        $crRowsAll = $rowsAll->where('Side', 'CR');

        $totalCrCheck = 0;

        foreach ($crRowsAll as $r) {
            $totalCrCheck += (float)($r->decMainAmount ?? 0);
        }

        $diff = round($totalDr - $totalCrCheck, 2);

        if ($totalDr < $totalCrCheck) {

            $differenceAmount = abs($diff);

            $sheet->setCellValue("A$row", 'Difference in Balance Sheet');
            $sheet->setCellValue("C$row", $this->format($differenceAmount));

            $sheet->getStyle("A$row:C$row")
                ->getFont()
                ->setBold(true)
                ->getColor()
                ->setARGB(Color::COLOR_RED);

            $sheet->getStyle("C$row")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            $totalDr += $differenceAmount;

            $row++;
        }

        $sheet->setCellValue("A$row", 'Total Assets (Dr)');
        $sheet->setCellValue("C$row", $this->format($totalDr));
        $sheet->getStyle("A$row:C$row")->getFont()->setBold(true);
        $sheet->getStyle("C$row")
              ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        return $row;
    }

    /* ======================================================
       LIABILITIES (CR SIDE)
       ====================================================== */

    private function addLiabilities($sheet, $row)
    {
        $sheet->setCellValue("A$row", 'Liabilities & Equity (Cr)');
        $sheet->getStyle("A$row")->getFont()->setBold(true);
        $row++;

        $rows = collect($this->data['rows'] ?? []);
        $crRows = $rows->where('Side', 'CR');

        $totalCr = 0;

        $fixedCR = ['Capital Account','Loans (Liability)','Current Liabilities','Suspense A/c','Profit & Loss A/c'];
        $printedCR = [];

        /* FIXED */
        foreach ($fixedCR as $grp) {

            foreach ($crRows->where('strGroupName', $grp) as $r) {

                $amt = (float)($r->decMainAmount ?? 0);

                $sheet->setCellValue("A$row", $r->strGroupName);
                $sheet->setCellValue("C$row", $this->format($amt));

                $sheet->getStyle("C$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                $totalCr += $amt;
                $printedCR[] = $r->strGroupName;
                $row++;
            }
        }

        /* REMAINING */
        foreach ($crRows as $r) {

            if (!in_array($r->strGroupName, $printedCR)) {

                $amt = (float)($r->decMainAmount ?? 0);

                $sheet->setCellValue("A$row", $r->strGroupName);
                $sheet->setCellValue("C$row", $this->format($amt));

                $sheet->getStyle("C$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                $totalCr += $amt;
                $row++;
            }
        }

        // foreach ($crRows as $r) {

        //     $amt = (float)($r->decMainAmount ?? 0);

        //     $sheet->setCellValue("A$row", $r->strGroupName ?? '-');
        //     $sheet->setCellValue("C$row", $this->format($amt));
        //     $sheet->getStyle("C$row")
        //           ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        //     $totalCr += $amt;
        //     $row++;
        // }
        /* ============================================
        DIFFERENCE ON LIABILITY SIDE
        ============================================ */

        $rowsAll = collect($this->data['rows'] ?? []);
        $drRowsAll = $rowsAll->where('Side', 'DR');

        $totalDrCheck = 0;

        foreach ($drRowsAll as $r) {

            $amount = (float)($r->decMainAmount ?? 0);

            $totalDrCheck += $amount > 0
                ? -1 * $amount
                : abs($amount);
        }

        $closingStock = abs((float)(($this->data['totals']['closing_stock'] ?? 0)));

        $totalDrCheck += $closingStock;

        $diff = round($totalDrCheck - $totalCr, 2);

        if ($totalCr < $totalDrCheck) {

            $differenceAmount = abs($diff);

            // $sheet->setCellValue("A$row", 'Difference in Balance Sheet');
            $sheet->setCellValue("C$row", $this->format($differenceAmount));

            $sheet->getStyle("A$row:C$row")
                ->getFont()
                ->setBold(true)
                ->getColor()
                ->setARGB(Color::COLOR_RED);

            $sheet->getStyle("C$row")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            $totalCr += $differenceAmount;

            $row++;
        }

        $sheet->setCellValue("A$row", 'Total (Cr)');
        $sheet->setCellValue("C$row", $this->format($totalCr));
        $sheet->getStyle("A$row:C$row")->getFont()->setBold(true);
        $sheet->getStyle("C$row")
              ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        return $row;
    }

    /* ======================================================
       BALANCE CHECK
       ====================================================== */

    private function addBalanceCheck($sheet, $row)
    {
        $rows   = collect($this->data['rows'] ?? []);
        $totals = $this->data['totals'] ?? [];

        $drRows = $rows->where('Side','DR');
        $crRows = $rows->where('Side','CR');

        $totalDr = 0;
        $totalCr = 0;

        foreach ($drRows as $r) {
            $amount = (float)($r->decMainAmount ?? 0);
            $totalDr += $amount > 0 ? -1 * $amount : abs($amount);
        }

        foreach ($crRows as $r) {
            $totalCr += (float)($r->decMainAmount ?? 0);
        }

        $closingStock = abs((float)($totals['closing_stock'] ?? 0));
        $totalDr += $closingStock;

        // $diff = round($totalDr - $totalCr, 2);

        // $sheet->setCellValue("A$row", 'Balance Status');

        // if ($diff == 0) {
        //     $sheet->setCellValue("C$row", 'Balanced');
        //     $sheet->getStyle("C$row")
        //           ->getFont()->getColor()->setARGB(Color::COLOR_DARKGREEN);
        // } else {
        //     $sheet->setCellValue("C$row", 'Difference: ' . $this->format(abs($diff)));
        //     $sheet->getStyle("C$row")
        //           ->getFont()->getColor()->setARGB(Color::COLOR_RED);
        // }

        // $sheet->getStyle("A$row:C$row")->getFont()->setBold(true);
    }
}
