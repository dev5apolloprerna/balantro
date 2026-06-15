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
use PhpOffice\PhpSpreadsheet\Style\Color;

class BalanceSheetExport implements FromCollection, WithHeadings, WithTitle, WithStyles, WithEvents
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
        return collect();
    }

    public function headings(): array
    {
        return [
            ['Balantro - Balance Sheet'],
            ['Period:', $this->from . ' to ' . $this->to],
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

                // Merge title cells
                $sheet->mergeCells('A1:C1');
                $sheet->mergeCells('A2:C2');

                $sheet->setCellValue('A1', 'Balantro - Balance Sheet');
                $sheet->setCellValue('A2', 'Period: ' . date('d-m-Y', strtotime($this->from)) . ' to ' . date('d-m-Y', strtotime($this->to)));

                // Start building the report dynamically
                $currentRow = $this->addSummarySection($sheet, 4);
                $currentRow = $this->addAssetsSection($sheet, $currentRow + 2);
                $currentRow = $this->addLiabilitiesEquitySection($sheet, $currentRow + 2);

                // Auto-size columns
                foreach (range('A', 'C') as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }
            },
        ];
    }

    // Helper function to remove minus signs
    private function displayAmount($amount)
    {
        return abs((float) $amount);
    }

    // Helper function for Indian number formatting without minus
    private function formatINR($num)
    {
        $num = $this->displayAmount($num);
        return number_format($num, 2);
    }

    private function addSummarySection($sheet, $startRow)
    {
        $totals = $this->data['totals'] ?? [];

        // Handle both field name structures
        if (isset($totals['totalDr']) && isset($totals['totalCr'])) {
            $totalAssets = $this->displayAmount($totals['totalDr'] ?? 0);
            $totalLiabilities = $this->displayAmount($this->calculateLiabilitiesFromRows());
            $totalEquity = $this->displayAmount($this->calculateEquityFromRows());
        } else {
            $totalAssets = $this->displayAmount($totals['assets'] ?? 0);
            $totalLiabilities = $this->displayAmount($totals['liabilities'] ?? 0);
            $totalEquity = $this->displayAmount($totals['equity'] ?? 0);
        }

        // Get closing stock amount
        $closingStockAmount = $this->displayAmount($totals['closing_stock'] ?? 0);

        $difference = $totalAssets - ($totalLiabilities + $totalEquity);

        $sheet->setCellValue('A' . $startRow, 'Total Assets');
        $sheet->setCellValue('B' . $startRow, '₹' . $this->formatINR($totalAssets));

        $sheet->setCellValue('A' . ($startRow + 1), 'Total Liabilities');
        $sheet->setCellValue('B' . ($startRow + 1), '₹' . $this->formatINR($totalLiabilities));

        $sheet->setCellValue('A' . ($startRow + 2), 'Total Equity');
        $sheet->setCellValue('B' . ($startRow + 2), '₹' . $this->formatINR($totalEquity));

        // Add Closing Stock to summary if exists
        if ($closingStockAmount > 0) {
            $sheet->setCellValue('A' . ($startRow + 3), 'Closing Stock');
            $sheet->setCellValue('B' . ($startRow + 3), '₹' . $this->formatINR($closingStockAmount));
            
            // Style closing stock row with green color
            $sheet->getStyle('A' . ($startRow + 3) . ':B' . ($startRow + 3))
                  ->getFont()
                  ->getColor()
                  ->setARGB(Color::COLOR_DARKGREEN);
            
            $balanceStatusRow = $startRow + 4;
            $differenceRow = $startRow + 5;
        } else {
            $balanceStatusRow = $startRow + 3;
            $differenceRow = $startRow + 4;
        }

        $sheet->setCellValue('A' . $balanceStatusRow, 'Balance Status');
        $sheet->setCellValue('A' . $differenceRow, 'Difference:');
        $sheet->setCellValue('B' . $differenceRow, '₹' . $this->formatINR($difference));

        // Style summary section
        $summaryEndRow = $closingStockAmount > 0 ? $startRow + 5 : $startRow + 4;
        $sheet->getStyle('A' . $startRow . ':B' . $summaryEndRow)->getFont()->setBold(true);
        $sheet->getStyle('B' . $startRow . ':B' . $summaryEndRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        return $summaryEndRow;
    }

    private function calculateLiabilitiesFromRows()
    {
        $rows = $this->data['rows'] ?? [];
        $liabilities = 0;

        foreach ($rows as $row) {
            $group = $row->GroupName ?? ($row->strGroupName ?? '');
            $amount = $row->Amount ?? ($row->decMainAmount ?? 0);

            if (stripos($group, 'liabil') !== false) {
                $liabilities += $amount;
            }
        }

        return $liabilities;
    }

    private function calculateEquityFromRows()
    {
        $rows = $this->data['rows'] ?? [];
        $equity = 0;

        foreach ($rows as $row) {
            $group = $row->GroupName ?? ($row->strGroupName ?? '');
            $amount = $row->Amount ?? ($row->decMainAmount ?? 0);

            if (stripos($group, 'equity') !== false || stripos($group, 'capital') !== false) {
                $equity += $amount;
            }
        }

        return $equity;
    }

    private function addAssetsSection($sheet, $startRow)
    {
        $currentRow = $startRow;

        $sheet->setCellValue('A' . $currentRow, 'Assets (Dr)');
        $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true);
        $currentRow++;

        $assets = [];
        $rows = $this->data['rows'] ?? [];

        // Get closing stock amount from totals
        $closingStockAmount = $this->displayAmount($this->data['totals']['closing_stock'] ?? 0);

        // Define expected asset groups
        $expectedAssetGroups = ['Current Assets', 'Fixed Assets', 'Investments', 'Closing Stock'];

        // Categorize assets - ensure all groups are included
        foreach ($rows as $item) {
            $group = $item->GroupName ?? ($item->strGroupName ?? '');
            $account = $item->AccountName ?? ($item->strAccountName ?? '');
            $amount = $item->Amount ?? ($item->decMainAmount ?? 0);

            if (in_array($group, $expectedAssetGroups) || stripos($group, 'asset') !== false || stripos($group, 'investment') !== false) {
                if (!isset($assets[$group])) {
                    $assets[$group] = [];
                }
                $assets[$group][] = [
                    'account' => $account,
                    'amount' => $this->displayAmount($amount)
                ];
            }
        }

        // Add Closing Stock as a separate asset group if it exists
        if ($closingStockAmount > 0) {
            if (!isset($assets['Closing Stock'])) {
                $assets['Closing Stock'] = [];
            }
            $assets['Closing Stock'][] = [
                'account' => 'Closing Stock',
                'amount' => $closingStockAmount,
            ];
        }

        // Ensure all expected groups exist (even if empty)
        foreach ($expectedAssetGroups as $group) {
            if (!isset($assets[$group])) {
                $assets[$group] = [];
            }
        }

        $assetsTotal = 0;

        // Process assets in specific order (Fixed Assets, Investments, Current Assets, Closing Stock)
        $assetOrder = ['Fixed Assets', 'Investments', 'Current Assets', 'Closing Stock'];

        foreach ($assetOrder as $groupName) {
            if (isset($assets[$groupName]) && count($assets[$groupName]) > 0) {
                $groupTotal = 0;

                // Add group header with special styling for Closing Stock
                if ($groupName === 'Closing Stock') {
                    $sheet->setCellValue('A' . $currentRow, 'Closing Stock');
                    $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true)->getColor()->setARGB(Color::COLOR_DARKGREEN);
                } else {
                    $sheet->setCellValue('A' . $currentRow, $groupName);
                    $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true);
                }
                $currentRow++;

                // Add accounts under this group
                foreach ($assets[$groupName] as $account) {
                    if ($groupName === 'Current Assets') {
                        // For Current Assets, subtract closing stock to show net amount
                        $netAmount = max(0, $account['amount'] - ($groupName === 'Current Assets' ? $closingStockAmount : 0));
                        if ($netAmount > 0) {
                            $sheet->setCellValue('B' . $currentRow, 'Current Assets (excluding stock)');
                            $sheet->setCellValue('C' . $currentRow, $this->formatINR($netAmount));
                            $sheet->getStyle('C' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                            $groupTotal += $netAmount;
                            $assetsTotal += $netAmount;
                            $currentRow++;
                        }
                    } else {
                        if (!empty($account['account'])) {
                            $sheet->setCellValue('B' . $currentRow, $account['account']);
                        } else {
                            $sheet->setCellValue('B' . $currentRow, $groupName);
                        }
                        
                        // Special styling for Closing Stock amount
                        if ($groupName === 'Closing Stock') {
                            $sheet->setCellValue('C' . $currentRow, $this->formatINR($account['amount']));
                            $sheet->getStyle('C' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                            $sheet->getStyle('C' . $currentRow)->getFont()->setBold(true)->getColor()->setARGB(Color::COLOR_DARKGREEN);
                        } else {
                            $sheet->setCellValue('C' . $currentRow, $this->formatINR($account['amount']));
                            $sheet->getStyle('C' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                        }

                        $groupTotal += $account['amount'];
                        $assetsTotal += $account['amount'];
                        $currentRow++;
                    }
                }

                // Add date note for Closing Stock
                if ($groupName === 'Closing Stock' && $closingStockAmount > 0) {
                    $sheet->setCellValue('B' . $currentRow, 'As of ' . date('d-m-Y', strtotime($this->to)));
                    $sheet->getStyle('B' . $currentRow)->getFont()->setItalic(true)->getColor()->setARGB(Color::COLOR_DARKGREEN);
                    $sheet->mergeCells('B' . $currentRow . ':C' . $currentRow);
                    $currentRow++;
                }

                // Add group subtotal (skip for Current Assets since we're showing net amount)
                if ($groupName !== 'Current Assets' || $groupTotal > 0) {
                    $sheet->setCellValue('B' . $currentRow, 'Subtotal');
                    $sheet->setCellValue('C' . $currentRow, $this->formatINR($groupTotal));
                    $sheet->getStyle('B' . $currentRow . ':C' . $currentRow)->getFont()->setBold(true);
                    $sheet->getStyle('C' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    
                    // Special styling for Closing Stock subtotal
                    if ($groupName === 'Closing Stock') {
                        $sheet->getStyle('C' . $currentRow)->getFont()->getColor()->setARGB(Color::COLOR_DARKGREEN);
                    }
                    $currentRow++;
                }
            }
        }

        // Add other assets (if any)
        foreach ($assets as $groupName => $accounts) {
            if (!in_array($groupName, $assetOrder) && count($accounts) > 0) {
                $groupTotal = 0;

                // Add group header
                $sheet->setCellValue('A' . $currentRow, $groupName);
                $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true);
                $currentRow++;

                // Add accounts under this group
                foreach ($accounts as $account) {
                    if (!empty($account['account'])) {
                        $sheet->setCellValue('B' . $currentRow, $account['account']);
                    } else {
                        $sheet->setCellValue('B' . $currentRow, $groupName);
                    }
                    $sheet->setCellValue('C' . $currentRow, $this->formatINR($account['amount']));
                    $sheet->getStyle('C' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                    $groupTotal += $account['amount'];
                    $assetsTotal += $account['amount'];
                    $currentRow++;
                }

                // Add group subtotal
                $sheet->setCellValue('B' . $currentRow, 'Subtotal');
                $sheet->setCellValue('C' . $currentRow, $this->formatINR($groupTotal));
                $sheet->getStyle('B' . $currentRow . ':C' . $currentRow)->getFont()->setBold(true);
                $sheet->getStyle('C' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $currentRow++;
            }
        }

        // Add assets grand total
        $sheet->setCellValue('A' . $currentRow, 'Total Assets (Dr)');
        $sheet->setCellValue('C' . $currentRow, '₹' . $this->formatINR($assetsTotal));
        $sheet->getStyle('A' . $currentRow . ':C' . $currentRow)->getFont()->setBold(true);
        $sheet->getStyle('C' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        return $currentRow;
    }

    private function addLiabilitiesEquitySection($sheet, $startRow)
    {
        $currentRow = $startRow;

        $sheet->setCellValue('A' . $currentRow, 'Liabilities & Equity (Cr)');
        $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true);
        $currentRow++;

        $liabilities = [];
        $equity = [];
        $rows = $this->data['rows'] ?? [];

        // Define expected groups
        $expectedLiabilityGroups = ['Current Liabilities', 'Loans (Liability)'];
        $expectedEquityGroups = ['Capital Account'];

        // Categorize liabilities and equity
        foreach ($rows as $item) {
            $group = $item->GroupName ?? ($item->strGroupName ?? '');
            $account = $item->AccountName ?? ($item->strAccountName ?? '');
            $amount = $item->Amount ?? ($item->decMainAmount ?? 0);

            if (in_array($group, $expectedLiabilityGroups) || stripos($group, 'liabil') !== false) {
                if (!isset($liabilities[$group])) {
                    $liabilities[$group] = [];
                }
                $liabilities[$group][] = [
                    'account' => $account,
                    'amount' => $this->displayAmount($amount)
                ];
            } elseif (in_array($group, $expectedEquityGroups) || stripos($group, 'equity') !== false || stripos($group, 'capital') !== false) {
                if (!isset($equity[$group])) {
                    $equity[$group] = [];
                }
                $equity[$group][] = [
                    'account' => $account,
                    'amount' => $this->displayAmount($amount)
                ];
            }
        }

        // Ensure all expected groups exist (even if empty)
        foreach ($expectedLiabilityGroups as $group) {
            if (!isset($liabilities[$group])) {
                $liabilities[$group] = [];
            }
        }
        foreach ($expectedEquityGroups as $group) {
            if (!isset($equity[$group])) {
                $equity[$group] = [];
            }
        }

        $liabilitiesTotal = 0;
        $equityTotal = 0;

        // Process liabilities in specific order
        $liabilityOrder = ['Current Liabilities', 'Loans (Liability)'];

        foreach ($liabilityOrder as $groupName) {
            if (isset($liabilities[$groupName]) && count($liabilities[$groupName]) > 0) {
                $groupTotal = 0;

                $sheet->setCellValue('A' . $currentRow, $groupName);
                $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true);
                $currentRow++;

                foreach ($liabilities[$groupName] as $account) {
                    if (!empty($account['account'])) {
                        $sheet->setCellValue('B' . $currentRow, $account['account']);
                    } else {
                        $sheet->setCellValue('B' . $currentRow, $groupName);
                    }
                    $sheet->setCellValue('C' . $currentRow, $this->formatINR($account['amount']));
                    $sheet->getStyle('C' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                    $groupTotal += $account['amount'];
                    $liabilitiesTotal += $account['amount'];
                    $currentRow++;
                }

                // Add group subtotal
                $sheet->setCellValue('B' . $currentRow, 'Subtotal');
                $sheet->setCellValue('C' . $currentRow, $this->formatINR($groupTotal));
                $sheet->getStyle('B' . $currentRow . ':C' . $currentRow)->getFont()->setBold(true);
                $sheet->getStyle('C' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $currentRow++;
            }
        }

        // Add other liabilities (if any)
        foreach ($liabilities as $groupName => $accounts) {
            if (!in_array($groupName, $liabilityOrder) && count($accounts) > 0) {
                $groupTotal = 0;

                $sheet->setCellValue('A' . $currentRow, $groupName);
                $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true);
                $currentRow++;

                foreach ($accounts as $account) {
                    if (!empty($account['account'])) {
                        $sheet->setCellValue('B' . $currentRow, $account['account']);
                    } else {
                        $sheet->setCellValue('B' . $currentRow, $groupName);
                    }
                    $sheet->setCellValue('C' . $currentRow, $this->formatINR($account['amount']));
                    $sheet->getStyle('C' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                    $groupTotal += $account['amount'];
                    $liabilitiesTotal += $account['amount'];
                    $currentRow++;
                }

                // Add group subtotal
                $sheet->setCellValue('B' . $currentRow, 'Subtotal');
                $sheet->setCellValue('C' . $currentRow, $this->formatINR($groupTotal));
                $sheet->getStyle('B' . $currentRow . ':C' . $currentRow)->getFont()->setBold(true);
                $sheet->getStyle('C' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $currentRow++;
            }
        }

        // Process equity in specific order
        $equityOrder = ['Capital Account'];

        foreach ($equityOrder as $groupName) {
            if (isset($equity[$groupName]) && count($equity[$groupName]) > 0) {
                $groupTotal = 0;

                $sheet->setCellValue('A' . $currentRow, $groupName);
                $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true);
                $currentRow++;

                foreach ($equity[$groupName] as $account) {
                    if (!empty($account['account'])) {
                        $sheet->setCellValue('B' . $currentRow, $account['account']);
                    } else {
                        $sheet->setCellValue('B' . $currentRow, $groupName);
                    }
                    $sheet->setCellValue('C' . $currentRow, $this->formatINR($account['amount']));
                    $sheet->getStyle('C' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                    $groupTotal += $account['amount'];
                    $equityTotal += $account['amount'];
                    $currentRow++;
                }

                // Add group subtotal
                $sheet->setCellValue('B' . $currentRow, 'Subtotal');
                $sheet->setCellValue('C' . $currentRow, $this->formatINR($groupTotal));
                $sheet->getStyle('B' . $currentRow . ':C' . $currentRow)->getFont()->setBold(true);
                $sheet->getStyle('C' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $currentRow++;
            }
        }

        // Add other equity (if any)
        foreach ($equity as $groupName => $accounts) {
            if (!in_array($groupName, $equityOrder) && count($accounts) > 0) {
                $groupTotal = 0;

                $sheet->setCellValue('A' . $currentRow, $groupName);
                $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true);
                $currentRow++;

                foreach ($accounts as $account) {
                    if (!empty($account['account'])) {
                        $sheet->setCellValue('B' . $currentRow, $account['account']);
                    } else {
                        $sheet->setCellValue('B' . $currentRow, $groupName);
                    }
                    $sheet->setCellValue('C' . $currentRow, $this->formatINR($account['amount']));
                    $sheet->getStyle('C' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                    $groupTotal += $account['amount'];
                    $equityTotal += $account['amount'];
                    $currentRow++;
                }

                // Add group subtotal
                $sheet->setCellValue('B' . $currentRow, 'Subtotal');
                $sheet->setCellValue('C' . $currentRow, $this->formatINR($groupTotal));
                $sheet->getStyle('B' . $currentRow . ':C' . $currentRow)->getFont()->setBold(true);
                $sheet->getStyle('C' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $currentRow++;
            }
        }

        $totalCr = $liabilitiesTotal + $equityTotal;

        // Add liabilities & equity grand total
        $sheet->setCellValue('A' . $currentRow, 'Total Liabilities & Equity (Cr)');
        $sheet->setCellValue('C' . $currentRow, '₹' . $this->formatINR($totalCr));
        $sheet->getStyle('A' . $currentRow . ':C' . $currentRow)->getFont()->setBold(true);
        $sheet->getStyle('C' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // Add difference note
        $currentRow += 2;
        $totals = $this->data['totals'] ?? [];

        if (isset($totals['totalDr'])) {
            $totalAssets = $this->displayAmount($totals['totalDr']);
        } else {
            $totalAssets = $this->displayAmount($totals['assets'] ?? 0);
        }

        $difference = $totalAssets - $totalCr;

        if (abs($difference) <= 0.01) {
            $sheet->setCellValue('A' . $currentRow, '✓ Balanced: Assets equal Liabilities + Equity');
            $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true)->getColor()->setARGB(Color::COLOR_DARKGREEN);
        } else {
            $sheet->setCellValue('A' . $currentRow, 'Note: Assets and Liabilities + Equity differ by ₹' . $this->formatINR(abs($difference)));
            $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true)->setItalic(true);
        }
        $sheet->mergeCells('A' . $currentRow . ':C' . $currentRow);

        return $currentRow;
    }
}