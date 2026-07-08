<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class VoucherExport implements FromArray, WithStyles
{
    protected $voucher;
    protected $header;
    protected $total;
    protected $accountLedger;
    protected $particulars;
    protected $displaySide;

    public function __construct($voucher, $header, $total, $accountLedger = null, $particulars = null, ?string $displaySide = null)
    {
        $this->voucher = collect($voucher);
        $this->header = $header;
        $this->total = $total;
        $this->accountLedger = $accountLedger ?: $header;
        $this->particulars = $particulars ? collect($particulars) : $this->voucher->filter(function ($row) {
            return trim((string) ($row->trnAccount ?? '')) !== trim((string) ($this->accountLedger->trnAccount ?? ''));
        })->values();
        $this->displaySide = $displaySide;
    }

    public function array(): array
    {
        $rows = [];

        $rows[] = [strtoupper($this->header->vchType)];
        $rows[] = ['Voucher No', $this->header->vchNo];
        $rows[] = ['Date', $this->header->strVchDate];
        // $partyLedger = $this->header;
        $rows[] = ['Account', ($partyLedger->trnAccount ?? '')];
        // $rows[] = [];
        $rows[] = ['Particulars', 'Amount'];

            // collect($this->voucher)
            // ->firstWhere('CRAmount', '>', 0);

        $lastSide = '';
        foreach ($this->particulars as $v) {
            $dr = (float) ($v->DRAmount ?? 0);
            $cr = (float) ($v->CRAmount ?? 0);
            $amount = abs($dr) > 0 ? abs($dr) : abs($cr);
            $side = ($dr > 0) ? ' Dr' : ' Cr';
            $lastSide = $side;

            $rows[] = [
                trim(strtoupper($v->trnAccount) . $side),
                number_format($amount, 2) . $side,
            ];
        }

        $rows[] = ['', number_format((float) $this->total, 2) . ' ' . trim($this->displaySide ?: $lastSide)];

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        return [

            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 18
                ]
            ],

            5 => [
                'font' => [
                    'bold' => true
                ]
            ],

        ];
    }
}