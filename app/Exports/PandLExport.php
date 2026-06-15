<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use App\Services\ReportsService;

class PandLExport implements FromArray, WithHeadings, WithTitle, WithStyles, WithColumnFormatting
{
    private $data;
    private $from;
    private $to;
    private $calculatedData;
    private $partyName;
    private $companyAddress;
    private $companyEmail;

    public function __construct(
        ReportsService $svc,
        int $partyId,
        ?string $from = null,
        ?string $to = null,
        ?string $partyName = null,
        ?string $companyAddress = null,
        ?string $companyEmail = null,
        ?array $customData = null
    )
    {
        if ($customData) {
            // Use provided custom data (for mobile API)
            $this->data = $customData['data'] ?? [];
            $this->from = $from;
            $this->to = $to;
            $this->partyName = $partyName;
            $this->companyAddress = $companyAddress;
            $this->companyEmail = $companyEmail;
        } else {
            // Use original method (for web)
            $toDMY = fn($d) => $d ? \Carbon\Carbon::parse($d)->format('d-m-Y') : '';
            $resp = $svc->pandl($partyId, $toDMY($from), $toDMY($to));
            $this->data = data_get($resp, 'data', []);
            $this->from = $from;
            $this->to = $to;
            $this->partyName = $partyName;
            $this->companyAddress = $companyAddress;
            $this->companyEmail = $companyEmail;
        }

        $this->calculatedData = $this->calculateTotals();
    }

    private function calculateTotals()
    {
        $cr = $this->data['cr'] ?? [];
        $dr = $this->data['dr'] ?? [];
        $iInc = $this->data['IndirectIncomes'] ?? [];
        $iExp = $this->data['IndirectExpenses'] ?? [];

        // Get stock values from the data
        $openingStock = (float) ($this->data['OpeningStock'] ?? 0);
        $closingStock = (float) ($this->data['ClosingStock'] ?? 0);

        $sum = function (array $rows, bool $absNeg = false): float {
            $t = 0.0;
            foreach ($rows as $r) {
                $v = (float) ($r['decMainAmount'] ?? 0);
                $t += $absNeg ? abs($v) : $v;
            }
            return $t;
        };

        // Calculate values according to Excel format
        $salesAccounts = $sum(array_filter($cr, function ($item) {
            return ($item['strGroupName'] ?? '') === 'Sales Accounts';
        }), false);

        $directIncomes = $sum(array_filter($cr, function ($item) {
            return ($item['strGroupName'] ?? '') === 'Direct Incomes';
        }), false);

        $purchaseAccounts = $sum(
            array_filter($dr, function ($item) {
                return ($item['strGroupName'] ?? '') === 'Purchase Accounts';
            })
        );

        $directExpenses = $sum(
            array_filter($dr, function ($item) {
                return ($item['strGroupName'] ?? '') === 'Direct Expenses';
            })
        );

        $indirectIncome = $sum($iInc, false);
        $indirectExpenses = $sum($iExp);

        // Excel format calculations
        // $totalIncome = $salesAccounts + $directIncomes + $closingStock; // A + B + C = D
        // $totalExpenses = $openingStock + $purchaseAccounts + $directExpenses; // E + F + G = H
        // ---------------- EXCEL FORMAT CALCULATIONS ----------------

        $totalIncome =
            $salesAccounts
            + $directIncomes
            + max($closingStock, 0)
            + max(-$openingStock, 0);

        $totalExpenses =
            max($openingStock, 0)
            + $purchaseAccounts
            + $directExpenses
            + abs(min($closingStock, 0));

        // Gross Profit/Loss
        $grossProfitLoss = $totalIncome - $totalExpenses;
        $grossIsProfit = $grossProfitLoss >= 0;
        $grossAbs = abs($grossProfitLoss);

        // Net Profit
        $netProfit =
            $grossProfitLoss
            + $indirectIncome
            - $indirectExpenses;

        $netIsProfit = $netProfit >= 0;
        $netAbs = abs($netProfit);

        // Charts
        $directCr = abs($sum($cr, false));
        $directDr = abs($sum($dr, true));
        $indirectCr = abs($sum($iInc, false));
        $indirectDr = abs($sum($iExp, true));

        $totalIncomeForCharts = $directCr + $indirectCr;
        $totalExpensesForCharts = $directDr + $indirectDr;

        // COGS
        $cogs =
            $openingStock
            + $purchaseAccounts
            + $directExpenses
            - $closingStock;

        // Gross Profit/Loss calculation (Excel format)
        $grossProfitLoss = $totalIncome - $totalExpenses; // D - H = I
        $grossIsProfit = $grossProfitLoss >= 0;
        $grossAbs = abs($grossProfitLoss);

        // Net Profit calculation (Excel format)
        $netProfit = $grossProfitLoss + $indirectIncome - $indirectExpenses; // I + J - K = L
        $netIsProfit = $netProfit >= 0;
        $netAbs = abs($netProfit);

        // For charts (keep existing chart calculations)
        $directCr = $sum($cr, false);
        $directDr = $sum($dr, true);
        $indirectCr = $sum($iInc, false);
        $indirectDr = $sum($iExp, true);

        // For charts (existing calculations)
        $totalIncomeForCharts = $directCr + $indirectCr;
        $totalExpensesForCharts = $directDr + $indirectDr;

        // Calculate COGS
        $cogs = $openingStock + $directDr - $closingStock;

        return [
            'salesAccounts' => $salesAccounts,
            'directIncomes' => $directIncomes,
            'purchaseAccounts' => $purchaseAccounts,
            'directExpenses' => $directExpenses,
            'indirectIncome' => $indirectIncome,
            'indirectExpenses' => $indirectExpenses,
            'totalIncome' => $totalIncome,
            'totalExpenses' => $totalExpenses,
            'grossProfitLoss' => $grossProfitLoss,
            'grossIsProfit' => $grossIsProfit,
            'grossAbs' => $grossAbs,
            'netProfit' => $netProfit,
            'netIsProfit' => $netIsProfit,
            'netAbs' => $netAbs,
            'openingStock' => $openingStock,
            'closingStock' => $closingStock,
            'cogs' => $cogs,
            'totalIncomeForCharts' => $totalIncomeForCharts,
            'totalExpensesForCharts' => $totalExpensesForCharts,
        ];
    }

    public function array(): array
    {
        $exportData = [];
        $calc = $this->calculatedData;

        // Header
        // $exportData[] = ['Profit & Loss Statement'];
        // $exportData[] = ['Period:', $this->from ? date('d-m-Y', strtotime($this->from)) : 'Start', 'to', $this->to ? date('d-m-Y', strtotime($this->to)) : 'End'];
        // $exportData[] = []; // Empty row
        $exportData[] = [strtoupper($this->partyName ?? 'COMPANY NAME')];
        $exportData[] = [$this->companyAddress ?? ''];
        $exportData[] = ['E-Mail : ' . ($this->companyEmail ?? '')];
        $exportData[] = ['Profit & Loss A/c'];
        $exportData[] = [
            ($this->from ? date('d-M-y', strtotime($this->from)) : 'Start')
            . ' to ' .
            ($this->to ? date('d-M-y', strtotime($this->to)) : 'End')
        ];
        $exportData[] = [];

        // STOCK INFORMATION
        // $exportData[] = ['STOCK INFORMATION'];
        // $exportData[] = ['Opening Stock', $calc['openingStock']];
        // $exportData[] = ['Closing Stock', $calc['closingStock']];
        // $exportData[] = ['Cost of Goods Sold', $calc['cogs']];
        $exportData[] = []; // Empty row

        // INCOME SECTION
        $exportData[] = ['INCOME'];
        $exportData[] = ['Sales Accounts', $calc['salesAccounts']];
        $exportData[] = ['Direct Incomes', $calc['directIncomes']];
        $exportData[] = ['Closing Stock', $calc['closingStock']];
        $exportData[] = ['Total', $calc['totalIncome']];
        $exportData[] = []; // Empty row

        // EXPENSES SECTION
        $exportData[] = ['EXPENSES'];
        $exportData[] = ['Opening Stock', $calc['openingStock']];
        $exportData[] = ['Purchase Accounts', $calc['purchaseAccounts']];
        $exportData[] = ['Direct Expenses', $calc['directExpenses']];
        $exportData[] = ['Total', $calc['totalExpenses']];
        $exportData[] = []; // Empty row

        // GROSS PROFIT/LOSS SECTION
        $exportData[] = ['GROSS PROFIT/LOSS'];
        $exportData[] = ['Gross Profit/Loss', $calc['grossAbs']];
        $exportData[] = []; // Empty row

        // INDIRECT INCOME SECTION
        $exportData[] = ['INDIRECT INCOME'];
        if (!empty($this->data['IndirectIncomes'])) {
            foreach ($this->data['IndirectIncomes'] as $income) {
                $exportData[] = [
                    $income['strGroupName'] ?? '—',
                    (float) $income['decMainAmount']
                ];
            }
        } else {
            $exportData[] = ['No Indirect Income', 0];
        }
        $exportData[] = ['Total Indirect Income', $calc['indirectIncome']];
        $exportData[] = []; // Empty row

        // INDIRECT EXPENSES SECTION
        $exportData[] = ['INDIRECT EXPENSES'];
        if (!empty($this->data['IndirectExpenses'])) {
            foreach ($this->data['IndirectExpenses'] as $expense) {
                $exportData[] = [
                    $expense['strGroupName'] ?? '—',
                    abs((float) $expense['decMainAmount'])
                ];
            }
        } else {
            $exportData[] = ['No Indirect Expenses', 0];
        }
        $exportData[] = ['Total Indirect Expenses', $calc['indirectExpenses']];
        $exportData[] = []; // Empty row

        // NET PROFIT/LOSS SECTION
        $exportData[] = ['NET PROFIT/LOSS'];
        $exportData[] = ['Net Profit', $calc['netAbs']];
        $exportData[] = []; // Empty row

        // SUMMARY SECTION
        $exportData[] = ['SUMMARY'];
        $exportData[] = ['Total Income', $calc['totalIncome']];
        $exportData[] = ['Total Expenses', $calc['totalExpenses']];
        $exportData[] = ['Gross ' . ($calc['grossIsProfit'] ? 'Profit' : 'Loss'), $calc['grossAbs']];
        $exportData[] = ['Net ' . ($calc['netIsProfit'] ? 'Profit' : 'Loss'), $calc['netAbs']];

        return $exportData;
    }

    public function headings(): array
    {
        // return [
        //     ['Profit & Loss Statement'],
        //     ['Period:', $this->from ? date('d-m-Y', strtotime($this->from)) : 'Start', 'to', $this->to ? date('d-m-Y', strtotime($this->to)) : 'End']
        // ];
        return [];
    }

    public function title(): string
    {
        return 'P&L Report';
    }

    public function styles(Worksheet $sheet)
    {
        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(40);
        $sheet->getColumnDimension('B')->setWidth(20);

        // Apply styles
        $lastRow = $sheet->getHighestRow();

        // Title
        // $sheet->mergeCells('A1:B1');
        // $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        // $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // // Period
        // $sheet->mergeCells('A2:B2');
        // $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A1:B1');
        $sheet->mergeCells('A2:B2');
        $sheet->mergeCells('A3:B3');
        $sheet->mergeCells('A4:B4');
        $sheet->mergeCells('A5:B5');

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

        // Section headers and styling
        foreach ($sheet->getRowIterator() as $row) {
            $rowIndex = $row->getRowIndex();
            $cellA = $sheet->getCell('A' . $rowIndex)->getValue();

            // Section headers
            if (in_array($cellA, [
                'STOCK INFORMATION',
                'INCOME',
                'EXPENSES',
                'GROSS PROFIT/LOSS',
                'INDIRECT INCOME',
                'INDIRECT EXPENSES',
                'NET PROFIT/LOSS',
                'SUMMARY'
            ])) {
                $sheet->getStyle('A' . $rowIndex . ':B' . $rowIndex)
                    ->getFont()->setBold(true)->setSize(12);
                $sheet->getStyle('A' . $rowIndex . ':B' . $rowIndex)
                    ->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFE6E6E6');

                // Merge cells for section headers
                $sheet->mergeCells('A' . $rowIndex . ':B' . $rowIndex);
            }

            // Total rows
            if (
                strpos($cellA, 'Total (') === 0 ||
                strpos($cellA, 'Total Indirect') === 0 ||
                $cellA === 'Total Income' ||
                $cellA === 'Total Expenses'
            ) {
                $sheet->getStyle('A' . $rowIndex . ':B' . $rowIndex)
                    ->getFont()->setBold(true);
                $sheet->getStyle('A' . $rowIndex . ':B' . $rowIndex)
                    ->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFF0F0F0');
            }

            // Profit/Loss rows with color coding
            if (strpos($cellA, 'Gross Profit/Loss') === 0) {
                $sheet->getStyle('A' . $rowIndex . ':B' . $rowIndex)
                    ->getFont()->setBold(true);
                $color = $this->calculatedData['grossIsProfit'] ? 'FF008000' : 'FFFF0000';
                $sheet->getStyle('B' . $rowIndex)->getFont()->getColor()->setARGB($color);
            }

            // Net Profit/Loss row
            if (strpos($cellA, 'Net Profit') === 0) {
                $sheet->getStyle('A' . $rowIndex . ':B' . $rowIndex)
                    ->getFont()->setBold(true)->setSize(12);
                $color = $this->calculatedData['netIsProfit'] ? 'FF008000' : 'FFFF0000';
                $sheet->getStyle('B' . $rowIndex)->getFont()->getColor()->setARGB($color);
            }

            // Summary section profit/loss rows
            if (
                strpos($cellA, 'Gross Profit') === 0 || strpos($cellA, 'Gross Loss') === 0 ||
                strpos($cellA, 'Net Profit') === 0 || strpos($cellA, 'Net Loss') === 0
            ) {
                $sheet->getStyle('A' . $rowIndex . ':B' . $rowIndex)
                    ->getFont()->setBold(true);
                $isProfit = strpos($cellA, 'Profit') !== false;
                $color = $isProfit ? 'FF008000' : 'FFFF0000';
                $sheet->getStyle('B' . $rowIndex)->getFont()->getColor()->setARGB($color);
            }
        }

        // Add borders to main sections
        $sections = [
            'STOCK INFORMATION' => 5,
            'INCOME' => 9,
            'EXPENSES' => 14,
            'GROSS PROFIT/LOSS' => 16,
            'INDIRECT INCOME' => 18,
            'INDIRECT EXPENSES' => 22,
            'NET PROFIT/LOSS' => 26,
            'SUMMARY' => 28
        ];

        foreach ($sections as $section => $startRow) {
            $endRow = $startRow + 4; // Adjust based on actual content
            $sheet->getStyle('A' . $startRow . ':B' . $endRow)
                ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        }

        // Number formatting for amounts
        $sheet->getStyle('B7:B' . $lastRow)->getNumberFormat()->setFormatCode('#,##0.00');
    }

    public function columnFormats(): array
    {
        return [
            'B' => '#,##0.00',
        ];
    }
}
