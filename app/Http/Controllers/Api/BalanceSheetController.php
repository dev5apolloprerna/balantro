<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\BalanceSheetExport;
use App\Services\ReportsService;

class BalanceSheetController extends Controller
{
    public function index(Request $request)
    {
        // try {
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

            $BalanceSheetDatas = DB::table('BalanceSheetData')
                ->where('PartyGUID', $partyguid)
                ->where('iParentId', 0)
                //->where("strDispName", '!=', 'Cost of Sales :')
                ->get();

            if ($BalanceSheetDatas->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No Balance Sheet data found for this PartyGUID'
                ], 404);
            }

            $data = [];
            $totalCr = 0;
            $totalDr = 0;
            foreach ($BalanceSheetDatas as $BalanceSheetData) {

                if ($BalanceSheetData->BSMAINAMT > 0) {
                    $BalanceSheetChildDatas = DB::table('BalanceSheetData')
                        ->where('PartyGUID', $partyguid)
                        ->where('iParentID', $BalanceSheetData->iBalaceSheetID)
                        ->get();
                    $child = [];
                    foreach ($BalanceSheetChildDatas as $BalanceSheetChildData) {
                        $child[] = array(
                            "iBalaceSheetID" => $BalanceSheetChildData->iBalaceSheetID,
                            "strDispName" => $BalanceSheetChildData->DSPDISPNAME,
                            "decSubAmount" => $BalanceSheetChildData->BSSUBAMT,
                            "decMainAmount" => round($BalanceSheetChildData->BSMAINAMT, 2),
                            "PartyGUID" => $BalanceSheetChildData->PartyGUID,
                            "iParentId" => $BalanceSheetChildData->iParentID,
                            "iYearId" => $BalanceSheetChildData->iYearId
                        );
                    }
                    $data['cr'][] = array(
                        "iBalaceSheetID" => $BalanceSheetData->iBalaceSheetID,
                        "strDispName" => $BalanceSheetData->DSPDISPNAME,
                        "decSubAmount" => $BalanceSheetData->BSSUBAMT,
                        "decMainAmount" => round($BalanceSheetData->BSMAINAMT, 2),
                        "PartyGUID" => $BalanceSheetData->PartyGUID,
                        "iParentId" => $BalanceSheetData->iParentID,
                        "iYearId" => $BalanceSheetData->iYearId,
                        "ChildData" => $child
                    );
                    $totalCr += $BalanceSheetData->BSMAINAMT;
                } else {
                    $BalanceSheetChildDatas = DB::table('BalanceSheetData')
                        ->where('PartyGUID', $partyguid)
                        ->where('iParentID', $BalanceSheetData->iBalaceSheetID)
                        ->get();
                    $child = [];
                    foreach ($BalanceSheetChildDatas as $BalanceSheetChildData) {
                        $child[] = array(
                            "iBalaceSheetID" => $BalanceSheetChildData->iBalaceSheetID,
                            "strDispName" => $BalanceSheetChildData->DSPDISPNAME,
                            "decSubAmount" => $BalanceSheetChildData->BSSUBAMT,
                            "decMainAmount" => (-1) * round($BalanceSheetChildData->BSMAINAMT, 2),
                            "PartyGUID" => $BalanceSheetChildData->PartyGUID,
                            "iParentId" => $BalanceSheetChildData->iParentID,
                            "iYearId" => $BalanceSheetChildData->iYearId
                        );
                    }
                    $data['dr'][] = array(
                        "iBalaceSheetID" => $BalanceSheetData->iBalaceSheetID,
                        "strDispName" => $BalanceSheetData->DSPDISPNAME,
                        "decSubAmount" => $BalanceSheetData->BSSUBAMT,
                        "decMainAmount" => (-1) * round($BalanceSheetData->BSMAINAMT, 2),
                        "PartyGUID" => $BalanceSheetData->PartyGUID,
                        "iParentId" => $BalanceSheetData->iParentID,
                        "iYearId" => $BalanceSheetData->iYearId,
                        "ChildData" => $child
                    );
                    $totalDr += $BalanceSheetData->BSMAINAMT;
                }
            }

            $data['totalCr'] = round($totalCr, 2);
            $data['totalDr'] = (-1) * round($totalDr, 2);

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        // } catch (\Exception $e) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Failed to retrieve code master records',
        //         'error' => $e->getMessage()
        //     ], 500);
        // }
    }
	
	public function index_new(Request $request)
	{
		try {
			// --- Auth check (kept as-is) ---
			$user = auth()->user();
			if (!$user) {
				return response()->json(['User not authenticated'], 401);
			}

			// --- Validate inputs (kept as-is) ---
			$validator = Validator::make([
				'partyguid'  => $request->partyguid,
				'start_date' => $request->start_date,
				'end_date'   => $request->end_date
			], [
				'partyguid'  => 'required|uuid',
				'start_date' => 'nullable|date|date_format:d-m-Y',
				'end_date'   => 'nullable|date|date_format:d-m-Y|after_or_equal:start_date'
			]);

			if ($validator->fails()) {
				return response()->json([
					'success' => false,
					'error'   => $validator->errors()
				], 422);
			}

			// --- Inputs ---
			$partyguid = $request->partyguid;
			$startDate = $request->start_date ? date('Y-m-d', strtotime($request->start_date)) : null;
			$endDate   = $request->end_date   ? date('Y-m-d', strtotime($request->end_date))   : null;

			// You were using the authenticated API id as PartyId
			$partyId = auth('api')->id();

			// --- Call the stored procedure and read multiple result sets ---
			$pdo  = DB::connection()->getPdo();
			$stmt = $pdo->prepare("EXEC dbo.GetBalanceSheetDataByParty :guid, :pid, :sd, :ed");

			$stmt->bindValue(':guid', $partyguid);       // UNIQUEIDENTIFIER (string binding OK)
			$stmt->bindValue(':pid',  (int) $partyId, \PDO::PARAM_INT);
			$stmt->bindValue(':sd',   $startDate);
			$stmt->bindValue(':ed',   $endDate);

			$stmt->execute();

			// 1) First result set: detail rows
			$details = $stmt->fetchAll(\PDO::FETCH_OBJ);

			// 2) Second result set: totals
			$stmt->nextRowset();
			$totals = $stmt->fetch(\PDO::FETCH_OBJ);

			// --- Get Closing Stock amount separately ---
			$closingStockAmount = 0;
			try {
				$closingStock = DB::table('ClosingStock')
					->where('iPartyId', $partyId)
					->where('ClosingStockDate', $endDate)
					->first();

				if ($closingStock) {
					$closingStockAmount = $closingStock->ClosingStockAmount ?? 0;
				} else {
					// If no exact date match, get the latest closing stock before end date
					$latestStock = DB::table('ClosingStock')
						->where('iPartyId', $partyId)
						->where('ClosingStockDate', '<=', $endDate)
						->orderBy('ClosingStockDate', 'desc')
						->first();

					if ($latestStock) {
						$closingStockAmount = $latestStock->ClosingStockAmount ?? 0;
					}
				}
			} catch (\Exception $e) {
				// Silently handle error - closing stock will be 0
				$closingStockAmount = 0;
			}

			// --- If no details, mirror your "no data" behavior ---
			if (!$details || count($details) === 0) {
				return response()->json([
					'success' => false,
					'message' => 'No Balance Sheet data found for this PartyGUID'
				], 404);
			}

			// --- Organize data like web view ---
			$data = [
				'cr' => [],
				'dr' => [],
			];

			// Separate rows by Side for calculations
			$drRows = array_filter($details, function($row) {
				return isset($row->Side) && strtoupper($row->Side) === 'DR';
			});

			$crRows = array_filter($details, function($row) {
				return isset($row->Side) && strtoupper($row->Side) === 'CR';
			});

			// Build individual entries
			foreach ($details as $row) {
				$amount = abs((float) $row->decMainAmount);

				$entry = [
					"iPrimaryGroupId" => $row->iPrimaryGroupId,
					"strGroupName"    => $row->strGroupName,
					"decMainAmount"   => $this->fmt($amount),
					"iPartyId"        => $partyId,
					"iYearId"         => $row->iYearId ?? 0,
				];
				if (isset($row->Side) && strtoupper($row->Side) === 'CR') {
					$data['cr'][] = $entry;
				} else {
					$data['dr'][] = $entry;
				}
			}

			// --- CALCULATE TOTALS LIKE WEB VIEW WITH CLOSING STOCK ---

			// ASSETS (DR) - Manual grouping like web view
			$currentAssetsRaw = 0;
			$fixedAssetsRaw = 0;
			$investmentsRaw = 0;
			$otherAssetsRaw = 0;

			foreach ($drRows as $row) {
				$amount = abs((float) $row->decMainAmount);
				$groupName = $row->strGroupName ?? '';

				if ($groupName === 'Current Assets') {
					$currentAssetsRaw += $amount;
				} elseif ($groupName === 'Fixed Assets') {
					$fixedAssetsRaw += $amount;
				} elseif ($groupName === 'Investments') {
					$investmentsRaw += $amount;
				} else {
					$otherAssetsRaw += $amount;
				}
			}

			// Subtract closing stock from current assets to show separately
			$currentAssetsWithoutStock = max(0, $currentAssetsRaw - $closingStockAmount);

			// Calculate totals like web view (including closing stock as separate component)
			$assetsTotal = $currentAssetsWithoutStock + $closingStockAmount + $fixedAssetsRaw + $investmentsRaw + $otherAssetsRaw;

			// LIABILITIES & EQUITY (CR) - Manual grouping like web view
			$capitalAccountRaw = 0;
			$loansRaw = 0;
			$currentLiabilitiesRaw = 0;
			$otherLiabilitiesRaw = 0;

			foreach ($crRows as $row) {
				$amount = abs((float) $row->decMainAmount);
				$groupName = $row->strGroupName ?? '';

				if ($groupName === 'Capital Account') {
					$capitalAccountRaw += $amount;
				} elseif ($groupName === 'Loans (Liability)') {
					$loansRaw += $amount;
				} elseif ($groupName === 'Current Liabilities') {
					$currentLiabilitiesRaw += $amount;
				} else {
					$otherLiabilitiesRaw += $amount;
				}
			}

			// Calculate totals like web view
			$liabilitiesTotal = $loansRaw + $currentLiabilitiesRaw + $otherLiabilitiesRaw;
			$equityTotal = $capitalAccountRaw;
			$liabsPlusEquityTotal = $liabilitiesTotal + $equityTotal;

			// Use web view calculated totals instead of stored procedure totals
			$data['totalDr'] = $this->fmt($assetsTotal);
			$data['totalCr'] = $this->fmt($liabsPlusEquityTotal);

			// Add closing stock to the data
			$data['closing_stock'] = $this->fmt($closingStockAmount);
			$data['closing_stock_date'] = $endDate;

			// Add additional breakdown for clarity
			$data['breakdown'] = [
				'assets' => [
					'current_assets_without_stock' => $this->fmt($currentAssetsWithoutStock),
					'closing_stock' => $this->fmt($closingStockAmount),
					'fixed_assets' => $this->fmt($fixedAssetsRaw),
					'investments' => $this->fmt($investmentsRaw),
					'other_assets' => $this->fmt($otherAssetsRaw),
					'total_assets' => $this->fmt($assetsTotal)
				],
				'liabilities' => [
					'current_liabilities' => $this->fmt($currentLiabilitiesRaw),
					'loans' => $this->fmt($loansRaw),
					'other_liabilities' => $this->fmt($otherLiabilitiesRaw),
					'total_liabilities' => $this->fmt($liabilitiesTotal)
				],
				'equity' => [
					'capital_account' => $this->fmt($capitalAccountRaw),
					'total_equity' => $this->fmt($equityTotal)
				],
				'summary' => [
					'total_dr' => $this->fmt($assetsTotal),
					'total_cr' => $this->fmt($liabsPlusEquityTotal),
					'balance_difference' => $this->fmt(abs($assetsTotal - $liabsPlusEquityTotal)),
					'is_balanced' => abs($assetsTotal - $liabsPlusEquityTotal) <= 0.01
				]
			];

			// Add closing stock summary
			$data['closing_stock_summary'] = [
				'amount' => $this->fmt($closingStockAmount),
				'date' => $endDate,
				'percentage_of_assets' => $assetsTotal > 0 ? round(($closingStockAmount / $assetsTotal) * 100, 1) : 0
			];

			return response()->json([
				'success' => true,
				'data'    => $data,
				'meta' => [
					'from_date' => $startDate,
					'to_date' => $endDate,
					'party_id' => $partyId,
					'closing_stock_available' => $closingStockAmount > 0
				]
			]);
		} catch (\Throwable $e) {
			return response()->json([
				'success' => false,
				'message' => 'Failed to retrieve balance sheet data',
				'error'   => $e->getMessage()
			], 500);
		}
	}
	
	/*public function index_new(Request $request)
	{
		try {
			// --- Auth check (kept as-is) ---
			$user = auth()->user();
			if (!$user) {
				return response()->json(['User not authenticated'], 401);
			}

			// --- Validate inputs (kept as-is) ---
			$validator = Validator::make([
				'partyguid'  => $request->partyguid,
				'start_date' => $request->start_date,
				'end_date'   => $request->end_date
			], [
				'partyguid'  => 'required|uuid',
				'start_date' => 'nullable|date|date_format:d-m-Y',
				'end_date'   => 'nullable|date|date_format:d-m-Y|after_or_equal:start_date'
			]);

			if ($validator->fails()) {
				return response()->json([
					'success' => false,
					'error'   => $validator->errors()
				], 422);
			}

			// --- Inputs ---
			$partyguid = $request->partyguid;
			$startDate = $request->start_date ? date('Y-m-d', strtotime($request->start_date)) : null;
			$endDate   = $request->end_date   ? date('Y-m-d', strtotime($request->end_date))   : null;

			// You were using the authenticated API id as PartyId
			$partyId = auth('api')->id();

			// --- Call the stored procedure and read multiple result sets ---
			$pdo  = DB::connection()->getPdo();
			$stmt = $pdo->prepare("EXEC dbo.GetBalanceSheetDataByParty :guid, :pid, :sd, :ed");

			$stmt->bindValue(':guid', $partyguid);       // UNIQUEIDENTIFIER (string binding OK)
			$stmt->bindValue(':pid',  (int) $partyId, \PDO::PARAM_INT);
			$stmt->bindValue(':sd',   $startDate);
			$stmt->bindValue(':ed',   $endDate);

			$stmt->execute();

			// 1) First result set: detail rows
			$details = $stmt->fetchAll(\PDO::FETCH_OBJ);

			// 2) Second result set: totals
			$stmt->nextRowset();
			$totals = $stmt->fetch(\PDO::FETCH_OBJ);

			// --- If no details, mirror your "no data" behavior ---
			if (!$details || count($details) === 0) {
				return response()->json([
					'success' => false,
					'message' => 'No Balance Sheet data found for this PartyGUID'
				], 404);
			}

			// --- Organize data like web view ---
			$data = [
				'cr' => [],
				'dr' => [],
			];

			// Separate rows by Side for calculations
			$drRows = array_filter($details, function($row) {
				return isset($row->Side) && strtoupper($row->Side) === 'DR';
			});

			$crRows = array_filter($details, function($row) {
				return isset($row->Side) && strtoupper($row->Side) === 'CR';
			});

			// Build individual entries
			foreach ($details as $row) {
				$amount = abs((float) $row->decMainAmount);

				$entry = [
					"iPrimaryGroupId" => $row->iPrimaryGroupId,
					"strGroupName"    => $row->strGroupName,
					"decMainAmount"   => $this->fmt($amount),
					"iPartyId"        => $row->iPartyId,
					"iYearId"         => $row->iYearId,
				];
				if (isset($row->Side) && strtoupper($row->Side) === 'CR') {
					$data['cr'][] = $entry;
				} else {
					$data['dr'][] = $entry;
				}
			}

			// --- CALCULATE TOTALS LIKE WEB VIEW ---

			// ASSETS (DR) - Manual grouping like web view
			$currentAssetsRaw = 0;
			$fixedAssetsRaw = 0;
			$investmentsRaw = 0;
			$otherAssetsRaw = 0;

			foreach ($drRows as $row) {
				$amount = abs((float) $row->decMainAmount);
				$groupName = $row->strGroupName ?? '';

				if ($groupName === 'Current Assets') {
					$currentAssetsRaw += $amount;
				} elseif ($groupName === 'Fixed Assets') {
					$fixedAssetsRaw += $amount;
				} elseif ($groupName === 'Investments') {
					$investmentsRaw += $amount;
				} else {
					$otherAssetsRaw += $amount;
				}
			}

			// LIABILITIES & EQUITY (CR) - Manual grouping like web view
			$capitalAccountRaw = 0;
			$loansRaw = 0;
			$currentLiabilitiesRaw = 0;
			$otherLiabilitiesRaw = 0;

			foreach ($crRows as $row) {
				$amount = abs((float) $row->decMainAmount);
				$groupName = $row->strGroupName ?? '';

				if ($groupName === 'Capital Account') {
					$capitalAccountRaw += $amount;
				} elseif ($groupName === 'Loans (Liability)') {
					$loansRaw += $amount;
				} elseif ($groupName === 'Current Liabilities') {
					$currentLiabilitiesRaw += $amount;
				} else {
					$otherLiabilitiesRaw += $amount;
				}
			}

			// Calculate totals like web view
			$assetsTotal = $currentAssetsRaw + $fixedAssetsRaw + $investmentsRaw + $otherAssetsRaw;
			$liabilitiesTotal = $loansRaw + $currentLiabilitiesRaw + $otherLiabilitiesRaw;
			$equityTotal = $capitalAccountRaw;
			$liabsPlusEquityTotal = $liabilitiesTotal + $equityTotal;

			// Use web view calculated totals instead of stored procedure totals
			$data['totalDr'] = $this->fmt($assetsTotal);
			$data['totalCr'] = $this->fmt($liabsPlusEquityTotal);

			// Add additional breakdown for clarity
			$data['breakdown'] = [
				'assets' => [
					'current_assets' => $this->fmt($currentAssetsRaw),
					'fixed_assets' => $this->fmt($fixedAssetsRaw),
					'investments' => $this->fmt($investmentsRaw),
					'other_assets' => $this->fmt($otherAssetsRaw),
					'total_assets' => $this->fmt($assetsTotal)
				],
				'liabilities' => [
					'current_liabilities' => $this->fmt($currentLiabilitiesRaw),
					'loans' => $this->fmt($loansRaw),
					'other_liabilities' => $this->fmt($otherLiabilitiesRaw),
					'total_liabilities' => $this->fmt($liabilitiesTotal)
				],
				'equity' => [
					'capital_account' => $this->fmt($capitalAccountRaw),
					'total_equity' => $this->fmt($equityTotal)
				],
				'summary' => [
					'total_dr' => $this->fmt($assetsTotal),
					'total_cr' => $this->fmt($liabsPlusEquityTotal),
					'balance_difference' => $this->fmt(abs($assetsTotal - $liabsPlusEquityTotal))
				]
			];

			return response()->json([
				'success' => true,
				'data'    => $data
			]);
		} catch (\Throwable $e) {
			return response()->json([
				'success' => false,
				'message' => 'Failed to retrieve balance sheet data',
				'error'   => $e->getMessage()
			], 500);
		}
	}*/


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

	private function getBalanceSheetData($partyId, $startDate, $endDate)
    {
        $partyguid = request()->partyguid; // Get from request
        
        try {
            $pdo = DB::connection()->getPdo();
            $stmt = $pdo->prepare("EXEC dbo.GetBalanceSheetDataByParty :guid, :pid, :sd, :ed");

            $stmt->bindValue(':guid', $partyguid);
            $stmt->bindValue(':pid', (int) $partyId, \PDO::PARAM_INT);
            $stmt->bindValue(':sd', $startDate);
            $stmt->bindValue(':ed', $endDate);

            $stmt->execute();

            // First result set: detail rows
            $details = $stmt->fetchAll(\PDO::FETCH_OBJ);

            // Second result set: totals
            $stmt->nextRowset();
            $totals = $stmt->fetch(\PDO::FETCH_OBJ);

            if (!$details || count($details) === 0) {
                return null;
            }

            // Organize data similar to your web structure
            $data = [
                'cr' => [], // Liabilities & Capital
                'dr' => [], // Assets
            ];

            foreach ($details as $row) {
                $entry = [
                    "iPrimaryGroupId" => $row->iPrimaryGroupId,
                    "strGroupName"    => $row->strGroupName,
                    "decMainAmount"   => (float) $row->decMainAmount,
                    "iPartyId"        => $row->iPartyId,
                    "iYearId"         => $row->iYearId,
                    "Side"            => $row->Side ?? '',
                ];
                
                if (isset($row->Side) && strtoupper($row->Side) === 'CR') {
                    $data['cr'][] = $entry;
                } else {
                    $data['dr'][] = $entry;
                }
            }

            // Calculate totals
            $totalCr = 0.0;
            $totalDr = 0.0;
            
            foreach ($data['cr'] as $item) {
                $totalCr += $item['decMainAmount'];
            }
            
            foreach ($data['dr'] as $item) {
                $totalDr += $item['decMainAmount'];
            }

            return [
                'data' => $data,
                'totals' => [
                    'totalCr' => $totalCr,
                    'totalDr' => $totalDr,
                    'balance' => abs($totalCr - $totalDr),
                    'isBalanced' => abs($totalCr - $totalDr) < 0.01, // Consider balanced if difference is very small
                ]
            ];

        } catch (\Throwable $e) {
            \Log::error('Balance Sheet Data Error: ' . $e->getMessage());
            return null;
        }
    }

    public function exportExcel(Request $request, ReportsService $svc)
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

            // Use the SAME service as web
            $resp = $svc->balanceSheet($partyguid, $partyId, $request->start_date, $request->end_date);
            $data = data_get($resp, 'data', []);

            if (!$data) {
                return response()->json(['success' => false, 'message' => 'No Balance Sheet data found'], 404);
            }

            $filename = 'balance-sheet-' . ($request->start_date ?: 'start') . '-to-' . ($request->end_date ?: 'end') . '.xlsx';

            // Use public disk
            $disk = Storage::disk('public');
            $directory = 'exports';
            
            // Create directory if it doesn't exist
            if (!$disk->exists($directory)) {
                $disk->makeDirectory($directory, 0755, true);
            }

            $filePath = $directory . '/' . $filename;
            
            // Store file using public disk - use same parameters as web
            $exportResult = Excel::store(
                new BalanceSheetExport($data, $request->start_date, $request->end_date), 
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
            ], 200);

        } catch (\Throwable $e) {
            \Log::error('Balance Sheet Excel Export Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false, 
                'message' => 'Failed to generate Excel file', 
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function exportPdf(Request $request, ReportsService $svc)
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
			
            // Use the SAME service as web
            $resp = $svc->balanceSheet($partyguid, $partyId, $request->start_date, $request->end_date);
            $data = data_get($resp, 'data', []);
			
			$partyName = $user->name;
			
            if (!$data) {
                return response()->json(['success' => false, 'message' => 'No Balance Sheet data found'], 404);
            }

            $filename = 'balance-sheet-' . ($request->start_date ?: 'start') . '-to-' . ($request->end_date ?: 'end') . '.pdf';

            // Generate PDF - use same parameters as web
            $pdf = Pdf::setOptions([
                'isRemoteEnabled' => true,
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
            ])->loadView('reports.pdf.balance_sheet_pdf', [
                'data' => $data,  // Same structure as web
                'from' => $request->start_date,
                'to' => $request->end_date,
				'partyName' => $partyName
                // Remove 'totals' as web doesn't use it
            ]);

            // Use public disk
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
                'filename' => $filename,
                'file_size' => $disk->size($filePath),
            ], 200);

        } catch (\Throwable $e) {
            \Log::error('Balance Sheet PDF Export Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false, 
                'message' => 'Failed to generate PDF file', 
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Direct download endpoints
    public function downloadExcel(Request $request)
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
            $balanceSheetData = $this->getBalanceSheetData($partyId, $startDate, $endDate);

            if (!$balanceSheetData) {
                return response()->json(['success' => false, 'message' => 'No Balance Sheet data found'], 404);
            }

            $filename = 'balance-sheet-' . ($request->start_date ?: 'start') . '-to-' . ($request->end_date ?: 'end') . '.xlsx';

            return Excel::download(
                new BalanceSheetExport($balanceSheetData, $request->start_date, $request->end_date), 
                $filename
            );

        } catch (\Throwable $e) {
            \Log::error('Balance Sheet Direct Excel Download Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false, 
                'message' => 'Failed to generate Excel file', 
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function downloadPdf(Request $request)
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
            $balanceSheetData = $this->getBalanceSheetData($partyId, $startDate, $endDate);

            if (!$balanceSheetData) {
                return response()->json(['success' => false, 'message' => 'No Balance Sheet data found'], 404);
            }

            $filename = 'balance-sheet-' . ($request->start_date ?: 'start') . '-to-' . ($request->end_date ?: 'end') . '.pdf';

            $pdf = Pdf::setOptions(['isRemoteEnabled' => true])
                    ->loadView('reports.pdf.balance_sheet_pdf', [
                        'data' => $balanceSheetData['data'],
                        'from' => $request->start_date,
                        'to' => $request->end_date,
                        'totals' => $balanceSheetData['totals']
                    ]);

            return $pdf->download($filename);

        } catch (\Throwable $e) {
            \Log::error('Balance Sheet Direct PDF Download Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false, 
                'message' => 'Failed to generate PDF file', 
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
