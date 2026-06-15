<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class DashboardController extends BaseApiController
{
    public function index(Request $request)
    {
        try {
            
            $userId = (int) auth()->id();
            $rows = \DB::select('EXEC dbo.usp_GetClientDocumentSummary ?', [$userId]);
            $row = $rows[0] ?? (object) [];
            
            return $this->success(
                __("response_message.dashboard.document_summary"),
                [
                    'uploaded_count'    => (int) ($row->uploaded_count    ?? 0),
                    'in_progress_count' => (int) ($row->in_progress_count ?? 0),
                    'completed_count'   => (int) ($row->completed_count   ?? 0),
                    'rejected_count'    => (int) ($row->rejected_count    ?? 0),
                ]
            );
        } catch (\Exception $e) {
            return $this->error(__("response_message.dashboard.dashboard_error"), 500, $e->getMessage());
        }
    }

    public function dropdown_type_list(Request $request)
    {
        try {

            $summary = [
                ['key' => '1', 'value' => "Sale & Purchase"],
                ['key' => '2', 'value' => "Credit & Debit"],
                ['key' => '3', 'value' => "Recepit & Payment"],
                ['key' => '4', 'value' => "Cash & Bank balance"]
            ];

            return $this->success(__("response_message.dashboard.dropdown_type_list"), $summary);
        } catch (\Exception $e) {
            return $this->error(__("response_message.dashboard.dropdown_type_error"), 500, $e->getMessage());
        }
    }

    public function dropdown_graph(Request $request)
    {
        try {
            // 1) Auth
            $user = auth('api')->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated.',
                ], 401);
            }

            // 2) Validate
            $validator = Validator::make($request->all(), [
                'partyguid'  => 'required|uuid',
                'start_date' => 'nullable|date_format:d-m-Y',
                'end_date'   => 'nullable|date_format:d-m-Y|after_or_equal:start_date',
                'type'       => 'required|in:1,2,3,4',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            // 3) Resolve party
            $partyguid = $request->partyguid;
            $exists = DB::select('EXEC CheckPartyGUIDCount ?', [$partyguid]);
            if (!$exists) {
                return response()->json([
                    'success'   => false,
                    'partyguid' => $partyguid,
                    'error'     => 'Invalid PartyGUID - not found in database'
                ], 422);
            }
            $partyId = auth('api')->id();

            // 4) Dates (default: current FY India)
            $tz = 'Asia/Kolkata';
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $startDate = Carbon::createFromFormat('d-m-Y', $request->start_date, $tz)->format('Y-m-d');
                $endDate   = Carbon::createFromFormat('d-m-Y', $request->end_date,   $tz)->format('Y-m-d');
            } else {
                $today = Carbon::now($tz);
                $fyStart = (int)$today->format('n') >= 4
                    ? Carbon::create($today->year, 4, 1, 0, 0, 0, $tz)
                    : Carbon::create($today->year - 1, 4, 1, 0, 0, 0, $tz);
                $fyEnd = $fyStart->copy()->addYear()->subDay(); // Mar 31
                $startDate = $fyStart->format('Y-m-d');
                $endDate   = $fyEnd->format('Y-m-d');
            }

            // 5) Execute the SP for the currently requested type (for monthly series)
            $type = (int)$request->type;
            $dateStyleDefault = 23;

            $series = [1 => null, 2 => null, 3 => null, 4 => null]; // cache for reuse
            $months  = [];
            $cashIn  = [];
            $cashOut = [];

            if ($type === 1) {
                $series[1] = DB::select(
                    'EXEC dbo.usp_VchMonthlyCashInOut @PartyId=?, @Start=?, @End=?, @DateStyle=?',
                    [$partyId, $startDate, $endDate, $dateStyleDefault]
                );
            } elseif ($type === 2) {
                $series[2] = DB::select(
                    'EXEC dbo.usp_MonthlyCashFlow_CreditorsDebtors @PartyId=?, @Start=?, @End=?, @DateStyle=?, @GroupList=?',
                    [$partyId, $startDate, $endDate, $dateStyleDefault, 'Sundry Creditors,Sundry Debtors']
                );
            } elseif ($type === 3) {
                $outflowNegative = $request->boolean('outflow_negative', false);
                $groups          = $request->input('groups', 'Sundry Creditors,Sundry Debtors');
                $excludeTypes    = $request->input('exclude_types', 'Rcpt,Pymt');

                $series[3] = DB::select(
                    'EXEC dbo.usp_MonthlyRcptPay_CreditorsDebtors @PartyId=?, @Start=?, @End=?, @DateStyle=?, @OutflowNegative=?, @GroupList=?, @ExcludeVchTypes=?',
                    [$partyId, $startDate, $endDate, $dateStyleDefault, $outflowNegative ? 1 : 0, $groups, $excludeTypes]
                );
            } else /* type 4 */ {
                $dateStyle4 = $request->has('date_style') ? $request->input('date_style') : 23;
                if ($dateStyle4 === '' || strtolower((string)$dateStyle4) === 'null') {
                    $dateStyle4 = null; // DATE/DATETIME column
                }
                $groups4       = $request->input('groups', 'Cash-in-Hand,Bank Accounts');
                $excludeTypes4 = $request->input('exclude_types', null);

                $series[4] = DB::select(
                    'EXEC dbo.usp_MonthlyCashBankFlow @PartyId=?, @Start=?, @End=?, @DateStyle=?, @GroupList=?, @ExcludeVchTypes=?',
                    [$partyId, $startDate, $endDate, $dateStyle4, $groups4, $excludeTypes4]
                );
            }

            // Normalize monthly series for the selected type -> arrays
            $selectedRows = $series[$type] ?? [];
            foreach ($selectedRows as $r) {
                $months[]  = (string)($r->label ?? '');
                $cashIn[]  = isset($r->cashIn)  ? (float)$r->cashIn  : 0.0;
                $cashOut[] = isset($r->cashOut) ? (float)$r->cashOut : 0.0;
            }

            // 6) Helper to sum in/out from an SP result set
            $sumInOut = function ($rows) {
                $in = 0.0;
                $out = 0.0;
                foreach ($rows as $r) {
                    $in  += isset($r->cashIn)  ? (float)$r->cashIn  : 0.0;
                    $out += isset($r->cashOut) ? (float)$r->cashOut : 0.0;
                }
                return [$this->fmt($in), $this->fmt($out)];
            };

            // 7) Ensure we have rows for ALL four types (reuse where possible; otherwise fetch now)
            if ($series[1] === null) {
                $series[1] = DB::select(
                    'EXEC dbo.usp_VchMonthlyCashInOut @PartyId=?, @Start=?, @End=?, @DateStyle=?',
                    [$partyId, $startDate, $endDate, $dateStyleDefault]
                );
            }
            if ($series[2] === null) {
                $series[2] = DB::select(
                    'EXEC dbo.usp_MonthlyCashFlow_CreditorsDebtors @PartyId=?, @Start=?, @End=?, @DateStyle=?, @GroupList=?',
                    [$partyId, $startDate, $endDate, $dateStyleDefault, 'Sundry Creditors,Sundry Debtors']
                );
            }
            if ($series[3] === null) {
                $outflowNegative = $request->boolean('outflow_negative', false);
                $groups          = $request->input('groups', 'Sundry Creditors,Sundry Debtors');
                $excludeTypes    = $request->input('exclude_types', 'Rcpt,Pymt');
                $series[3] = DB::select(
                    'EXEC dbo.usp_MonthlyRcptPay_CreditorsDebtors @PartyId=?, @Start=?, @End=?, @DateStyle=?, @OutflowNegative=?, @GroupList=?, @ExcludeVchTypes=?',
                    [$partyId, $startDate, $endDate, $dateStyleDefault, $outflowNegative ? 1 : 0, $groups, $excludeTypes]
                );
            }
            //if ($series[4] === null) {
            // $dateStyle4 = $request->has('date_style') ? $request->input('date_style') : 23;
            // if ($dateStyle4 === '' || strtolower((string)$dateStyle4) === 'null') {
            //     $dateStyle4 = null;
            // }
            // $groups4       = $request->input('groups', 'Cash-in-Hand,Bank Accounts');
            // $excludeTypes4 = $request->input('exclude_types', null);
            // $series[4] = DB::select(
            //     'EXEC dbo.usp_MonthlyCashBankFlow @PartyId=?, @Start=?, @End=?, @DateStyle=?, @GroupList=?, @ExcludeVchTypes=?',
            //     [$partyId, $startDate, $endDate, $dateStyle4, $groups4, $excludeTypes4]
            // );

            $groups4 = $request->input('groups', 'Cash-in-Hand,Bank Accounts');
            // Get all vouchers up to end_date for these groups
            $closingRows = DB::select(
                'EXEC dbo.usp_ClosingBalanceCashBank @PartyId=?, @EndDate=?, @GroupList=?',
                [$partyId, $endDate, $groups4]
            );

            //$series[4] = $closingRows; // will hold closing values instead of monthly series
            $closingMap = collect($closingRows)->mapWithKeys(function ($r) {
                return [trim($r->strGroupName) => (float)$r->Closing];
            })->all();

            $closingCash = $closingMap['Cash-in-Hand']  ?? 0.0;
            $closingBank = $closingMap['Bank Accounts'] ?? 0.0;

            //}

            // 8) Compute totals for ALL categories
            [$in1, $out1] = $sumInOut($series[1]); // Sale & Purchase
            [$in2, $out2] = $sumInOut($series[2]); // Credit & Debit
            [$in3, $out3] = $sumInOut($series[3]); // Receipt & Payment
            //[$closingCash, $closingBank] = $sumInOut($series[4]); // Cash & Bank balance (assuming cashIn=cash, cashOut=bank)

            // Selected-type generic totals (still useful for the chart legend)
            $totalInSelected  = $this->fmt(array_sum($cashIn));
            $totalOutSelected = $this->fmt(array_sum($cashOut));

            $sales_group = DB::table('GroupMaster')
                ->where('strGroupName', 'Sales Accounts')
                ->where('iPartyId', $partyId)
                ->first();

            $purchase_group = DB::table('GroupMaster')
                ->where('strGroupName', 'Purchase Accounts')
                ->where('iPartyId', $partyId)
                ->first();


            $creditors_group = DB::table('GroupMaster')
                ->where('strGroupName', 'Sundry Creditors')
                ->where('iPartyId', $partyId)
                ->first();

            $debtors_group = DB::table('GroupMaster')
                ->where('strGroupName', 'Sundry Debtors')
                ->where('iPartyId', $partyId)
                ->first();

            $cash_group = DB::table('GroupMaster')
                ->where('strGroupName', 'Cash-in-Hand')
                ->where('iPartyId', $partyId)
                ->first();

            $bank_group = DB::table('GroupMaster')
                ->where('strGroupName', 'Bank Accounts')
                ->where('iPartyId', $partyId)
                ->first();
            // 9) Build response
            return response()->json([
                'success' => true,
                'message' => __('response_message.dashboard.graph_list'),
                'data'    => [
                    // monthly series for the requested type
                    'type'     => $type,
                    'range'    => [$startDate, $endDate],
                    'months'   => $months,
                    'cashIn'   => $cashIn,
                    'cashOut'  => $cashOut,

                    // generic totals for the selected type
                    'totals'   => [
                        'totalIn'  => $totalInSelected,
                        'totalOut' => $totalOutSelected,
                    ],

                    // ALWAYS show all eight named totals
                    'allTotals' => [
                        'totalSale'     => ["iGroupId" => $sales_group->iGroupId, "value" => $in1],
                        'totalPurchase' => ["iGroupId" => $purchase_group->iGroupId, "value" => $out1],
                        'totalCredit'   => ["iGroupId" => $creditors_group->iGroupId, "value" => $in2],
                        'totalDebit'    => ["iGroupId" => $debtors_group->iGroupId, "value" => $out2],
                        'totalReceipt'  => ["iGroupId" => $creditors_group->iGroupId, "value" => $in3],
                        'totalPayment'  => ["iGroupId" => $debtors_group->iGroupId, "value" => $out3],
                        // 'totalCash'     => ["iGroupId" => $cash_group->iGroupId, "value" => $in4],
                        // 'totalBank'     => ["iGroupId" => $bank_group->iGroupId, "value" => $out4],
                        'totalCash'     => ["iGroupId" => $cash_group->iGroupId, "value" => $this->fmt($closingCash)],
                        'totalBank'     => ["iGroupId" => $bank_group->iGroupId, "value" => $this->fmt($closingBank)]
                    ],
                ],
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => __('response_message.dashboard.graph_list_error'),
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    private function fmt($v): string
    {
        // normalize input
        if ($v === null || $v === '') {
            $v = 0;
        }
        $num = is_numeric(trim((string)$v)) ? (float)trim((string)$v) : 0.0;

        // Prefer intl NumberFormatter (en_IN) if the extension is available
        if (class_exists(\NumberFormatter::class)) {
            $nf = new \NumberFormatter('en_IN', \NumberFormatter::DECIMAL);
            $nf->setAttribute(\NumberFormatter::FRACTION_DIGITS, 2);
            $out = $nf->format($num);
            if ($out !== false) {
                return $out; // e.g., 20,00,000.00
            }
        }

        // Fallback: manual Indian grouping with 2 decimals
        $sign = $num < 0 ? '-' : '';
        $abs  = abs($num);
        $fixed = sprintf('%.2f', $abs);         // no grouping, just 2 decimals
        [$int, $dec] = explode('.', $fixed);

        if (strlen($int) > 3) {
            $last3 = substr($int, -3);
            $rest  = substr($int, 0, -3);
            // insert commas every 2 digits in the rest
            $rest = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', $rest);
            $int  = $rest . ',' . $last3;
        }

        return $sign . $int . '.' . $dec;       // e.g., 20,00,000.00
    }
}
