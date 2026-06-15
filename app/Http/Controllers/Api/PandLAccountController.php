<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

use App\Exports\PandLExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Client;
use App\Services\ReportsService;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon; // Add this import

class PandLAccountController extends Controller
{
    public function index(Request $request)
    {
        try {
            $partyguid = $request->header('partyguid');
            $validator = Validator::make([
                'partyguid' => $request->header('partyguid'),
                'start_date' => $request->header('start_date'),
                'end_date' => $request->header('end_date')
            ], [
                'partyguid' => 'required|uuid',
                'start_date' => 'nullable|date|date_format:Y-m-d',
                'end_date' => 'nullable|date|date_format:Y-m-d|after_or_equal:start_date'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'error' => $validator->errors()
                ], 422);
            }

            // If validation passes, continue with your logic
            $partyguid = $request->header('partyguid');
            $startDate = $request->header('start_date');
            $endDate = $request->header('end_date');

            $results = DB::select(
                'EXEC CheckPartyGUIDCount ?',
                [$partyguid]
            );

            if (!$results) {
                return response()->json([
                    'success' => false,
                    'partyguid' => $partyguid,
                    'error' =>  'Invalid PartyGUID - not found in database'
                ], 422);
            }

            $pandlDatas = DB::table('PandLAccount')
                ->where('PartyGUID', $partyguid)
                ->where('iParentId', 0)
                ->where("strDispName", '!=', 'Cost of Sales :')
                ->get();

            if ($pandlDatas->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No P&L account data found for this PartyGUID'
                ], 404);
            }

            $data = [];
            $totalCr = 0;
            $totalDr = 0;
            $IndirectIncomes = 0;
            $IndirectExpenses = 0;
            foreach ($pandlDatas as $pandlData) {

                if ($pandlData->decMainAmount > 0) {
                    if ($pandlData->strDispName != "Indirect Incomes") {
                        $pandlChildDatas = DB::table('PandLAccount')
                            ->where('PartyGUID', $partyguid)
                            ->where('iParentId', $pandlData->iProfitLossId)
                            ->get();
                        $child = [];
                        foreach ($pandlChildDatas as $pandlChildData) {
                            $child[] = array(
                                "iProfitLossId" => $pandlChildData->iProfitLossId,
                                "strDispName" => $pandlChildData->strDispName,
                                "decSubAmount" => $pandlChildData->decSubAmount,
                                "decMainAmount" => round($pandlChildData->decMainAmount, 2),
                                "PartyGUID" => $pandlChildData->PartyGUID,
                                "iParentId" => $pandlChildData->iParentId,
                                "iYearId" => $pandlChildData->iYearId
                            );
                        }
                        $data['cr'][] = array(
                            "iProfitLossId" => $pandlData->iProfitLossId,
                            "strDispName" => $pandlData->strDispName,
                            "decSubAmount" => $pandlData->decSubAmount,
                            "decMainAmount" => round($pandlData->decMainAmount, 2),
                            "PartyGUID" => $pandlData->PartyGUID,
                            "iParentId" => $pandlData->iParentId,
                            "iYearId" => $pandlData->iYearId,
                            "ChildData" => $child
                        );
                        $totalCr += $pandlData->decMainAmount;
                    } else {
                        $pandlChildDatas = DB::table('PandLAccount')
                            ->where('PartyGUID', $partyguid)
                            ->where('iParentId', $pandlData->iProfitLossId)
                            ->get();
                        $child = [];
                        foreach ($pandlChildDatas as $pandlChildData) {
                            $child[] = array(
                                "iProfitLossId" => $pandlChildData->iProfitLossId,
                                "strDispName" => $pandlChildData->strDispName,
                                "decSubAmount" => $pandlChildData->decSubAmount,
                                "decMainAmount" => round($pandlChildData->decMainAmount, 2),
                                "PartyGUID" => $pandlChildData->PartyGUID,
                                "iParentId" => $pandlChildData->iParentId,
                                "iYearId" => $pandlChildData->iYearId
                            );
                        }
                        $data['IndirectIncomes'][] = array(
                            "iProfitLossId" => $pandlData->iProfitLossId,
                            "strDispName" => $pandlData->strDispName,
                            "decSubAmount" => $pandlData->decSubAmount,
                            "decMainAmount" => round($pandlData->decMainAmount, 2),
                            "PartyGUID" => $pandlData->PartyGUID,
                            "iParentId" => $pandlData->iParentId,
                            "iYearId" => $pandlData->iYearId,
                            "ChildData" => $child
                        );
                        $IndirectIncomes += $pandlData->decMainAmount;
                    }
                } else {

                    if ($pandlData->strDispName != "Indirect Expenses") {
                        $pandlChildDatas = DB::table('PandLAccount')
                            ->where('PartyGUID', $partyguid)
                            ->where('iParentId', $pandlData->iProfitLossId)
                            ->get();
                        $child = [];
                        foreach ($pandlChildDatas as $pandlChildData) {
                            $child[] = array(
                                "iProfitLossId" => $pandlChildData->iProfitLossId,
                                "strDispName" => $pandlChildData->strDispName,
                                "decSubAmount" => $pandlChildData->decSubAmount,
                                "decMainAmount" => round($pandlChildData->decMainAmount, 2),
                                "PartyGUID" => $pandlChildData->PartyGUID,
                                "iParentId" => $pandlChildData->iParentId,
                                "iYearId" => $pandlChildData->iYearId
                            );
                        }
                        $data['dr'][] = array(
                            "iProfitLossId" => $pandlData->iProfitLossId,
                            "strDispName" => $pandlData->strDispName,
                            "decSubAmount" => $pandlData->decSubAmount,
                            "decMainAmount" => round($pandlData->decMainAmount, 2),
                            "PartyGUID" => $pandlData->PartyGUID,
                            "iParentId" => $pandlData->iParentId,
                            "iYearId" => $pandlData->iYearId,
                            "ChildData" => $child
                        );
                        $totalDr += $pandlData->decMainAmount;
                    } else {
                        $pandlChildDatas = DB::table('PandLAccount')
                            ->where('PartyGUID', $partyguid)
                            ->where('iParentId', $pandlData->iProfitLossId)
                            ->get();
                        $child = [];
                        foreach ($pandlChildDatas as $pandlChildData) {
                            $child[] = array(
                                "iProfitLossId" => $pandlChildData->iProfitLossId,
                                "strDispName" => $pandlChildData->strDispName,
                                "decSubAmount" => $pandlChildData->decSubAmount,
                                "decMainAmount" => round($pandlChildData->decMainAmount, 2),
                                "PartyGUID" => $pandlChildData->PartyGUID,
                                "iParentId" => $pandlChildData->iParentId,
                                "iYearId" => $pandlChildData->iYearId
                            );
                        }
                        $data['IndirectExpenses'][] = array(
                            "iProfitLossId" => $pandlData->iProfitLossId,
                            "strDispName" => $pandlData->strDispName,
                            "decSubAmount" => $pandlData->decSubAmount,
                            "decMainAmount" => round($pandlData->decMainAmount, 2),
                            "PartyGUID" => $pandlData->PartyGUID,
                            "iParentId" => $pandlData->iParentId,
                            "iYearId" => $pandlData->iYearId,
                            "ChildData" => $child
                        );
                        $IndirectExpenses += $pandlData->decMainAmount;
                    }
                }
            }

            $data['totalCr'] = round($totalCr, 2);
            $data['totalDr'] = round($totalDr, 2);

            $data['GrossPandL'] = round($totalCr - (-1 * $totalDr), 2);

            $data['NetPandL'] = round($data['GrossPandL'] + $IndirectIncomes - (-1 * $IndirectExpenses), 2);

            //DB::connection('sqlsrv')->getPdo();
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve code master records',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function index_new(Request $request)
    {
        try {
            $user = auth('api')->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated'
                ], 401);
            }

            $validator = Validator::make($request->all(), [
                'partyguid'  => 'required|uuid',
                'start_date' => 'nullable|date|date_format:d-m-Y',
                'end_date'   => 'nullable|date|date_format:d-m-Y|after_or_equal:start_date',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $partyguid = trim((string)$request->partyguid);
            $exists = DB::select('EXEC CheckPartyGUIDCount ?', [$partyguid]);

            if (!$exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid PartyGUID'
                ], 422);
            }

            $partyId = $user->id;
            /*
            |--------------------------------------------------------------------------
            | DATE RANGE
            |--------------------------------------------------------------------------
            */
            $toYmd = function (?string $d): ?string {
                if (!$d) return null;
                return Carbon::parse($d)->format('Y-m-d');
            };

            $startDate = $toYmd($request->start_date);
            $endDate   = $toYmd($request->end_date);

            if (!$startDate || !$endDate) {
                $today = Carbon::today();
                if ($today->month < 4) {
                    $fyStart = Carbon::create($today->year - 1, 4, 1);
                    $fyEnd   = Carbon::create($today->year, 3, 31);
                } else {
                    $fyStart = Carbon::create($today->year, 4, 1);
                    $fyEnd   = Carbon::create($today->year + 1, 3, 31);
                }
                $startDate = $startDate ?: $fyStart->format('Y-m-d');
                $endDate   = $endDate ?: $fyEnd->format('Y-m-d');
            }

            /*
            |--------------------------------------------------------------------------
            | GET DATA
            |--------------------------------------------------------------------------
            */
            $pandlDatas = collect(DB::select(
                'EXEC dbo.GetBalanceSheetBase_new ?, ?, ?',
                [
                    $partyId,
                    $startDate,
                    $endDate
                ]
            ));

            if ($pandlDatas->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'IndirectIncomes' => [],
                        'cr' => [],
                        'dr' => [],
                        'IndirectExpenses' => [],
                        'totalCr' => '0.00',
                        'totalDr' => '0.00',
                        'TotalIncome' => '0.00',
                        'TotalExpenses' => '0.00',
                        'GrossPandL' => '0.00',
                        'NetPandL' => '0.00',
                        'OpeningStock' => '0.00',
                        'ClosingStock' => '0.00',
                        'COGS' => '0.00',
                    ]
                ], 200);
            }

            /*
            |--------------------------------------------------------------------------
            | VARIABLES
            |--------------------------------------------------------------------------
            */

            $data = [
                'cr' => [],
                'dr' => [],
                'IndirectIncomes' => [],
                'IndirectExpenses' => []
            ];
            $totII = 0;
            $totIE = 0;
            $openingStock = 0;
            $closingStock = 0;
            /*
            |--------------------------------------------------------------------------
            | LOOP
            |--------------------------------------------------------------------------
            */
            foreach ($pandlDatas as $r) {
                $dr = (float)($r->DrAmount ?? 0);
                $cr = (float)($r->CRAmount ?? 0);
                /*
                |--------------------------------------------------------------------------
                | OPENING STOCK
                |--------------------------------------------------------------------------
                */
                if ($r->strGroupName === 'Opening Stock') {
                    $val = abs($dr + $cr);
                    $openingStock = $val;
                    continue;
                }
                /*
                |--------------------------------------------------------------------------
                | CLOSING STOCK
                |--------------------------------------------------------------------------
                */
                if ($r->strGroupName === 'Closing Stock') {
                    $val = abs($dr + $cr);
                    $closingStock = $val;
                    continue;
                }
                /*
                |--------------------------------------------------------------------------
                | SALES + DIRECT INCOME
                |--------------------------------------------------------------------------
                */
                if (in_array($r->strGroupName, [
                    'Sales Accounts',
                    'Direct Incomes'
                ])) {
                    $val = $cr + $dr;
                    if ($dr > 0) {
                        $val = -1 * $val;
                    }
                    $group = DB::table('GroupMaster')
                        ->where('strGroupName', $r->strGroupName)
                        ->where('iPartyId', $r->iPartyId)
                        ->first();
                    $data['cr'][] = [
                        'iPrimaryGroupId' => $group->iGroupId ?? null,
                        'strGroupName'    => $r->strGroupName ?? '',
                        'decMainAmount'   => abs($val),
                        'formattedAmount' => $this->fmt(abs($val)),
                        'iPartyId'        => $r->iPartyId ?? null,
                        'iYearId'         => $r->iYearId ?? null,
                    ];
                    continue;
                }
                /*
                |--------------------------------------------------------------------------
                | PURCHASE + DIRECT EXPENSE
                |--------------------------------------------------------------------------
                */
                if (in_array($r->strGroupName, [
                    'Purchase Accounts',
                    'Direct Expenses'
                ])) {
                    $val = abs($dr + $cr);
                    if (abs($cr) > abs($dr)) {
                        $val = -1 * $val;
                    }
                    $group = DB::table('GroupMaster')
                        ->where('strGroupName', $r->strGroupName)
                        ->where('iPartyId', $r->iPartyId)
                        ->first();
                    $data['dr'][] = [
                        'iPrimaryGroupId' => $group->iGroupId ?? null,
                        'strGroupName'    => $r->strGroupName ?? '',
                        'decMainAmount'   => abs($val),
                        'formattedAmount' => $this->fmt(abs($val)),
                        'iPartyId'        => $r->iPartyId ?? null,
                        'iYearId'         => $r->iYearId ?? null,
                    ];
                    continue;
                }
                /*
                |--------------------------------------------------------------------------
                | INDIRECT INCOME
                |--------------------------------------------------------------------------
                */
                if ($r->strGroupName === 'Indirect Incomes') {
                    $val = $cr + $dr;
                    if ($dr > 0) {
                        $val = -1 * $val;
                    }
                    $group = DB::table('GroupMaster')
                        ->where('strGroupName', 'Indirect Incomes')
                        ->where('iPartyId', $r->iPartyId)
                        ->first();
                    $data['IndirectIncomes'][] = [
                        'iPrimaryGroupId' => $group->iGroupId ?? null,
                        'strGroupName'    => $r->strGroupName ?? '',
                        'decMainAmount'   => abs($val),
                        'formattedAmount' => $this->fmt(abs($val)),
                        'iPartyId'        => $r->iPartyId ?? null,
                        'iYearId'         => $r->iYearId ?? null,
                    ];
                    $totII += abs($val);
                    continue;
                }
                /*
                |--------------------------------------------------------------------------
                | INDIRECT EXPENSE
                |--------------------------------------------------------------------------
                */
                if ($r->strGroupName === 'Indirect Expenses') {
                    $val = abs($dr + $cr);
                    if (abs($cr) > abs($dr)) {
                        $val = -1 * $val;
                    }
                    $group = DB::table('GroupMaster')
                        ->where('strGroupName', 'Indirect Expenses')
                        ->where('iPartyId', $r->iPartyId)
                        ->first();
                    $data['IndirectExpenses'][] = [
                        'iPrimaryGroupId' => $group->iGroupId ?? null,
                        'strGroupName'    => $r->strGroupName ?? '',
                        'decMainAmount'   => abs($val),
                        'formattedAmount' => $this->fmt(abs($val)),
                        'iPartyId'        => $r->iPartyId ?? null,
                        'iYearId'         => $r->iYearId ?? null,
                    ];
                    $totIE += abs($val);
                    continue;
                }
            }
            /*
            |--------------------------------------------------------------------------
            | TOTALS
            |--------------------------------------------------------------------------
            */
            $salesAccounts = 0;
            $directIncomes = 0;
            foreach ($data['cr'] as $row) {
                if (($row['strGroupName'] ?? '') === 'Sales Accounts') {
                    $salesAccounts += (float)$row['decMainAmount'];
                }
                if (($row['strGroupName'] ?? '') === 'Direct Incomes') {
                    $directIncomes += (float)$row['decMainAmount'];
                }
            }
            $purchaseAccounts = 0;
            $directExpenses = 0;
            foreach ($data['dr'] as $row) {
                if (($row['strGroupName'] ?? '') === 'Purchase Accounts') {
                    $purchaseAccounts += (float)$row['decMainAmount'];
                }
                if (($row['strGroupName'] ?? '') === 'Direct Expenses') {
                    $directExpenses += (float)$row['decMainAmount'];
                }
            }
            /*
            |--------------------------------------------------------------------------
            | EXACT VIEW + TALLY LOGIC
            |--------------------------------------------------------------------------
            */
            $totalIncome = $salesAccounts + $directIncomes + max($closingStock, 0) + max(-$openingStock, 0);

            $totalExpenses = max($openingStock, 0) + $purchaseAccounts + $directExpenses + abs(min($closingStock, 0));

            /*
            |--------------------------------------------------------------------------
            | COGS
            |--------------------------------------------------------------------------
            */

            $cogs = max($openingStock, 0) + $purchaseAccounts + $directExpenses - max($closingStock, 0);

            /*
            |--------------------------------------------------------------------------
            | GROSS P/L
            |--------------------------------------------------------------------------
            */

            $grossProfit = $totalIncome - $totalExpenses;

            /*
            |--------------------------------------------------------------------------
            | NET P/L
            |--------------------------------------------------------------------------
            */

            $netProfit = $grossProfit + $totII - $totIE;

            /*
            |--------------------------------------------------------------------------
            | RESPONSE
            |--------------------------------------------------------------------------
            */

            $responseData = [
                'IndirectIncomes' => $data['IndirectIncomes'],
                'cr' => $data['cr'],
                'dr' => $data['dr'],
                'IndirectExpenses' => $data['IndirectExpenses'],
                'totalCr' => $this->fmt($totalIncome),
                'totalDr' => $this->fmt($totalExpenses),
                'TotalIncome' => $this->fmt($totalIncome),
                'TotalExpenses' => $this->fmt($totalExpenses),
                'GrossPandL' => $this->fmt($grossProfit),
                'NetPandL' => $this->fmt($netProfit),
                'OpeningStock' => $this->fmt($openingStock),
                'ClosingStock' => $this->fmt($closingStock),
                'COGS' => $this->fmt($cogs),
                'SalesAccounts' => $this->fmt($salesAccounts),
                'PurchaseAccounts' => $this->fmt($purchaseAccounts),
                'DirectIncomes' => $this->fmt($directIncomes),
                'DirectExpenses' => $this->fmt($directExpenses),
                'TotalIndirectIncome' => $this->fmt($totII),
                'TotalIndirectExpenses' => $this->fmt($totIE),
            ];
            return response()->json([
                'success' => true,
                'data' => $responseData
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function fmt($v): string
    {
        if ($v === null || $v === '') {
            $v = 0;
        }

        $num = (float)$v;

        if (class_exists(\NumberFormatter::class)) {

            $nf = new \NumberFormatter('en_IN', \NumberFormatter::DECIMAL);

            $nf->setAttribute(\NumberFormatter::FRACTION_DIGITS, 2);

            return $nf->format($num);
        }

        return number_format($num, 2);
    }

   
	
	private function getPandLData($partyId, $startDate, $endDate)
    {
        $pandlDatas = collect(DB::select('EXEC dbo.GetBalanceSheetBase ?, ?, ?', [$partyId, $startDate, $endDate]));
        
        if ($pandlDatas->isEmpty()) {
            return null;
        }

        $data = ['cr' => [], 'dr' => [], 'IndirectIncomes' => [], 'IndirectExpenses' => []];
        $totalCr = 0.0;
        $totalDr = 0.0;
        $totII = 0.0;
        $totIE = 0.0;

        foreach ($pandlDatas as $r) {
            $dr = (float)($r->DrAmount ?? 0);
            $cr = (float)($r->CRAmount ?? 0);

            switch ($r->strGroupName) {
                case 'Sales Accounts':
                case 'Direct Incomes':
                    $val = $cr - $dr;
                    $data['cr'][] = [
                        'iPrimaryGroupId' => $r->iPrimaryGroupId ?? null,
                        'strGroupName'    => $r->strGroupName ?? '',
                        'decMainAmount'   => $this->fmt($val),
                        'iPartyId'        => $r->iPartyId ?? null,
                        'iYearId'         => $r->iYearId ?? null,
                    ];
                    $totalCr += $val;
                    break;

                case 'Purchase Accounts':
                case 'Direct Expenses':
                    $neg = (-1 * $dr) - $cr;
                    $pos = -1 * $neg;
                    $data['dr'][] = [
                        'iPrimaryGroupId' => $r->iPrimaryGroupId ?? null,
                        'strGroupName'    => $r->strGroupName ?? '',
                        'decMainAmount'   => $this->fmt($pos),
                        'iPartyId'        => $r->iPartyId ?? null,
                        'iYearId'         => $r->iYearId ?? null,
                    ];
                    $totalDr += $pos;
                    break;

                case 'Indirect Incomes':
                    $val = $cr - $dr;
                    $data['IndirectIncomes'][] = [
                        'iPrimaryGroupId' => $r->iPrimaryGroupId ?? null,
                        'strGroupName'    => $r->strGroupName ?? '',
                        'decMainAmount'   => $this->fmt($val),
                        'iPartyId'        => $r->iPartyId ?? null,
                        'iYearId'         => $r->iYearId ?? null,
                    ];
                    $totII += $val;
                    break;

                case 'Indirect Expenses':
                    $neg = (-1 * $dr) - $cr;
                    $pos = -1 * $neg;
                    $data['IndirectExpenses'][] = [
                        'iPrimaryGroupId' => $r->iPrimaryGroupId ?? null,
                        'strGroupName'    => $r->strGroupName ?? '',
                        'decMainAmount'   => $this->fmt($pos),
                        'iPartyId'        => $r->iPartyId ?? null,
                        'iYearId'         => $r->iYearId ?? null,
                    ];
                    $totIE += $pos;
                    break;
            }
        }

        $gross = $totalCr - $totalDr;
        $net   = $gross + $totII - $totIE;

        return [
            'data' => $data,
            'totals' => [
                'totalCr' => $this->fmt($totalCr),
                'totalDr' => $this->fmt($totalDr),
                'gross' => $this->fmt($gross),
                'net' => $this->fmt($net),
                'grossIsProfit' => $gross >= 0,
                'netIsProfit' => $net >= 0,
            ]
        ];
    }

    /*public function exportExcel(Request $request)
	{
		try {
			$user = auth('api')->user();
			if (!$user) {
				return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
			}

			$validator = Validator::make($request->all(), [
				'partyguid'  => 'required|uuid',
				'start_date' => 'nullable|date|date_format:d-m-Y',
				'end_date'   => 'nullable|date|date_format:d-m-Y|after_or_equal:start_date',
			]);

			if ($validator->fails()) {
				return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
			}

			$partyguid = $request->partyguid;
			$startDate = $request->start_date ? date('Y-m-d', strtotime($request->start_date)) : null;
			$endDate   = $request->end_date ? date('Y-m-d', strtotime($request->end_date)) : null;

			// Verify party GUID
			$exists = DB::select('EXEC CheckPartyGUIDCount ?', [$partyguid]);
			if (!$exists) {
				return response()->json(['success' => false, 'message' => 'Invalid PartyGUID'], 422);
			}

			$partyId = $user->id;
			$svc = app(ReportsService::class); 
			// $pandlData = $this->getPandLData($partyId, $startDate, $endDate);
			$pandlData = $svc->pandl($partyId, $startDate, $endDate);

			if (!$pandlData) {
				return response()->json(['success' => false, 'message' => 'No P&L data found'], 404);
			}

			// Create a custom service instance for the export
			//$svc = new ReportsService();

			$filename = 'profit-loss-report-' . ($request->start_date ?: 'start') . '-to-' . ($request->end_date ?: 'end') . '.xlsx';

			// Use public disk with explicit configuration
			$disk = Storage::disk('public');
			$directory = 'exports';

			// Create directory if it doesn't exist
			if (!$disk->exists($directory)) {
				$disk->makeDirectory($directory, 0755, true);
			}

			$filePath = $directory . '/' . $filename;

			// Store file using public disk
			$exportResult = Excel::store(
				new PandLExport($svc, $partyId, $request->start_date, $request->end_date, $pandlData), 
				$filePath, 
				'public' // Explicitly specify public disk
			);

			if (!$exportResult) {
				throw new \Exception('Failed to store Excel file');
			}

			// Verify file was created
			if (!$disk->exists($filePath)) {
				throw new \Exception('Excel file was not created in storage');
			}

			// Get file URL - use asset() for public disk
			$fileUrl = asset('storage/' . $filePath);

			// Verify the URL is accessible
			$fullPath = storage_path('app/public/' . $filePath);
			if (!file_exists($fullPath)) {
				throw new \Exception('File exists in storage but not at expected path: ' . $fullPath);
			}

			return response()->json([
				'success' => true,
				'message' => 'Excel file generated successfully',
				'download_url' => $fileUrl,
				'filename' => $filename,
				'file_size' => $disk->size($filePath),
				'file_path' => $filePath
			], 200);

		} catch (\Throwable $e) {
			\Log::error('Excel Export Error: ' . $e->getMessage());
			\Log::error('Excel Export Trace: ' . $e->getTraceAsString());

			return response()->json([
				'success' => false, 
				'message' => 'Failed to generate Excel file', 
				'error' => $e->getMessage()
			], 500);
		}
	} */
	
	public function exportExcel(Request $request)
    {
        try {
            $user = auth('api')->user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
            }

            $validator = Validator::make($request->all(), [
                'partyguid'  => 'required|uuid',
                'start_date' => 'nullable|date|date_format:d-m-Y',
                'end_date'   => 'nullable|date|date_format:d-m-Y|after_or_equal:start_date',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            $partyguid = $request->partyguid;
            
            // Verify party GUID
            $exists = DB::select('EXEC CheckPartyGUIDCount ?', [$partyguid]);
            if (!$exists) {
                return response()->json(['success' => false, 'message' => 'Invalid PartyGUID'], 422);
            }

            $partyId = $user->id;
            
            // Use the SAME service and date format as web
            $svc = app(ReportsService::class);
            
            $filename = 'profit-loss-report-' . ($request->start_date ?: 'start') . '-to-' . ($request->end_date ?: 'end') . '.xlsx';

            // Use public disk with explicit configuration
            $disk = Storage::disk('public');
            $directory = 'exports';

            // Create directory if it doesn't exist
            if (!$disk->exists($directory)) {
                $disk->makeDirectory($directory, 0755, true);
            }

            $filePath = $directory . '/' . $filename;

            // Store file using public disk - WITHOUT custom data
            $exportResult = Excel::store(
                new PandLExport($svc, $partyId, $request->start_date, $request->end_date), 
                $filePath, 
                'public'
            );

            if (!$exportResult) {
                throw new \Exception('Failed to store Excel file');
            }

            // Get file URL - use asset() for public disk
            $fileUrl = asset('storage/' . $filePath);

            return response()->json([
                'success' => true,
                'message' => 'Excel file generated successfully',
                'download_url' => $fileUrl,
                'filename' => $filename,
                'file_size' => $disk->size($filePath),
                'file_path' => $filePath
            ], 200);

        } catch (\Throwable $e) {
            \Log::error('Excel Export Error: ' . $e->getMessage());
            \Log::error('Excel Export Trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false, 
                'message' => 'Failed to generate Excel file', 
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function exportPdf(Request $request)
	{
		try {
			$user = auth('api')->user();
			if (!$user) {
				return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
			}

			$validator = Validator::make($request->all(), [
				'partyguid'  => 'required|uuid',
				'start_date' => 'nullable|date|date_format:d-m-Y',
				'end_date'   => 'nullable|date|date_format:d-m-Y|after_or_equal:start_date',
			]);

			if ($validator->fails()) {
				return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
			}

			$partyguid = $request->partyguid;
			$startDate = $request->start_date ? date('Y-m-d', strtotime($request->start_date)) : null;
			$endDate   = $request->end_date ? date('Y-m-d', strtotime($request->end_date)) : null;

			// Verify party GUID
			$exists = DB::select('EXEC CheckPartyGUIDCount ?', [$partyguid]);
			if (!$exists) {
				return response()->json(['success' => false, 'message' => 'Invalid PartyGUID'], 422);
			}

			$partyId = $user->id;
			$svc = app(ReportsService::class); 
			//$pandlData = $this->getPandLData($partyId, $startDate, $endDate);
			$pandlData = $svc->pandl($partyId, $startDate, $endDate);
			$partyName = $user->name;
			if (!$pandlData) {
				return response()->json(['success' => false, 'message' => 'No P&L data found'], 404);
			}

			$filename = 'profit-loss-report-' . ($request->start_date ?: 'start') . '-to-' . ($request->end_date ?: 'end') . '.pdf';

			// Generate PDF
			$pdf = Pdf::setOptions([
				'isRemoteEnabled' => true,
				'isHtml5ParserEnabled' => true,
				'isPhpEnabled' => true,
			])->loadView('reports.pdf.pl-pdf', [
				'pl' => $pandlData['data'],
				'from' => $request->start_date,
				'to' => $request->end_date,
				'totals' => $pandlData['totals'] ?? 0,
				'partyName' => $partyName
			]);

			// Use public disk and ensure directory exists
			$disk = Storage::disk('public');
			$directory = 'exports';

			// Create directory if it doesn't exist
			if (!$disk->exists($directory)) {
				$disk->makeDirectory($directory, 0755, true);
			}

			$filePath = $directory . '/' . $filename;

			// Store PDF using public disk
			$disk->put($filePath, $pdf->output());

			// Get file URL - use asset() for public disk
			$fileUrl = asset('storage/' . $filePath);

			return response()->json([
				'success' => true,
				'message' => 'PDF file generated successfully',
				'download_url' => $fileUrl,
				'filename' => $filename
			], 200);

		} catch (\Throwable $e) {
			\Log::error('PDF Export Error: ' . $e->getMessage());
			\Log::error('PDF Export Trace: ' . $e->getTraceAsString());

			return response()->json([
				'success' => false, 
				'message' => 'Failed to generate PDF file', 
				'error' => $e->getMessage()
			], 500);
		}
	}

    // Direct download endpoints (if preferred)
    public function downloadExcel(Request $request)
    {
        // Similar to exportExcel but returns file directly
        $user = auth('api')->user();
        if (!$user) {
            abort(401);
        }

        $validator = Validator::make($request->all(), [
            'partyguid'  => 'required|uuid',
            'start_date' => 'nullable|date|date_format:d-m-Y',
            'end_date'   => 'nullable|date|date_format:d-m-Y|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            abort(422, 'Validation failed');
        }

        $partyId = $user->id;
        $svc = new ReportsService();
        $filename = 'profit-loss-report-' . ($request->start_date ?: 'start') . '-to-' . ($request->end_date ?: 'end') . '.xlsx';

        return Excel::download(new PandLExport($svc, $partyId, $request->start_date, $request->end_date), $filename);
    }

    public function downloadPdf(Request $request)
    {
        // Similar to exportPdf but returns file directly
        $user = auth('api')->user();
        if (!$user) {
            abort(401);
        }

        $validator = Validator::make($request->all(), [
            'partyguid'  => 'required|uuid',
            'start_date' => 'nullable|date|date_format:d-m-Y',
            'end_date'   => 'nullable|date|date_format:d-m-Y|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            abort(422, 'Validation failed');
        }

        $partyId = $user->id;
		$partyName = $user->name;
        $pandlData = $this->getPandLData($partyId, 
            $request->start_date ? date('Y-m-d', strtotime($request->start_date)) : null,
            $request->end_date ? date('Y-m-d', strtotime($request->end_date)) : null
        );

        if (!$pandlData) {
            abort(404, 'No P&L data found');
        }

        $filename = 'profit-loss-report-' . ($request->start_date ?: 'start') . '-to-' . ($request->end_date ?: 'end') . '.pdf';

        $pdf = Pdf::setOptions(['isRemoteEnabled' => true])
                ->loadView('reports.pdf.pl-pdf', [
                    'pl' => $pandlData['data'],
                    'from' => $request->start_date,
                    'to' => $request->end_date,
                    'totals' => $pandlData['totals'] ?? 0,
					'partyName' => $partyName
                ]);

        return $pdf->download($filename);
    }
}
