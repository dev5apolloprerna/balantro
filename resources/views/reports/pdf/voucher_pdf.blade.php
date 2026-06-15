<!DOCTYPE html>
<html>
<head>

    <style>

        body{
            font-family: DejaVu Sans;
            font-size:12px;
            color:#000;
        }

        .border-bottom{
            border-bottom:1px solid #999;
        }

        .row{
            width:100%;
            clear:both;
        }

        .left{
            float:left;
        }

        .right{
            float:right;
        }

        .mt{
            margin-top:15px;
        }

        table{
            width:100%;
            border-collapse:collapse;
        }

        th{
            border-bottom:1px solid #999;
            text-align:left;
            padding:6px;
        }

        td{
            padding:5px 6px;
        }

        .amount{
            text-align:right;
            width:150px;
        }

        .title{
            font-size:24px;
            font-weight:bold;
        }

        .total{
            border-top:1px solid #999;
            font-weight:bold;
        }

    </style>

</head>
<body>

    {{-- HEADER --}}
    <div class="border-bottom">

        <div class="title">
            {{ strtoupper($header->vchType) }}
        </div>

        <div class="mt">

            <div>
                Voucher No :
                <strong>{{ $header->vchNo }}</strong>
            </div>

            <div>
                Date :
                {{ date('d-M-y', strtotime($header->strVchDate)) }}
            </div>

        </div>

    </div>

    {{-- PARTY --}}
    @php
        $partyLedger = $header; // $voucher->firstWhere('CRAmount', '>', 0);
    @endphp

    <div class="mt border-bottom" style="padding-bottom:10px;">

        <strong>Party A/c Name :</strong>

        {{ $partyLedger->trnAccount ?? '' }}

    </div>

    {{-- TABLE --}}
    <div class="mt">

        <table>

            <thead>

                <tr>

                    <th>
                        Particulars
                    </th>

                    <th class="amount">
                        Amount
                    </th>

                </tr>

            </thead>

            <tbody>

                @foreach($voucher as $v)

                    @if($v->trnAccount != ($partyLedger->trnAccount ?? ''))

                        @php

                            $dr = (float)$v->DRAmount;
                            $cr = (float)$v->CRAmount;

                            $amount =
                                abs($dr) > 0
                                ? abs($dr)
                                : abs($cr);
                            $side = ($dr > 0) ? ' Dr' : ' Cr' ;
                        @endphp

                        <tr>

                            <td>
                                {{ strtoupper($v->trnAccount) }} 
                            </td>

                            <td class="amount">
                                {{ number_format($amount,2) }} {{ $side }}
                            </td>

                        </tr>

                    @endif

                @endforeach

                <tr class="total">

                    <td></td>

                    <td class="amount">

                        {{ number_format(abs($totalDr ?: $totalCr),2) }} {{ $side }}

                    </td>

                </tr>

            </tbody>

        </table>

    </div>

</body>
</html>