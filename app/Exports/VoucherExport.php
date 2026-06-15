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

    public function __construct($voucher, $header, $total)
    {
        $this->voucher = $voucher;
        $this->header = $header;
        $this->total = $total;
    }

    public function array(): array
    {
        $rows = [];

        $rows[] = [strtoupper($this->header->vchType)];
        $rows[] = ['Voucher No', $this->header->vchNo];
        $rows[] = ['Date', $this->header->strVchDate];
        $partyLedger = $this->header;
        $rows[] = ['Party A/c Name', ($partyLedger->trnAccount ?? '')];
        // $rows[] = [];
        $rows[] = ['Particulars', 'Amount'];

            // collect($this->voucher)
            // ->firstWhere('CRAmount', '>', 0);

        foreach ($this->voucher as $v) {

            if ($v->trnAccount != ($partyLedger->trnAccount ?? '')) {

                $dr = (float)$v->DRAmount;
                $cr = (float)$v->CRAmount;

                $amount =
                    abs($dr) > 0
                    ? abs($dr)
                    : abs($cr);
                $side = ($dr > 0) ? ' Dr' : ' Cr' ;
                $rows[] = [
                    strtoupper($v->trnAccount),
                    $amount . $side
                ];
            }
        }

        $rows[] = ['', $this->total . $side];

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