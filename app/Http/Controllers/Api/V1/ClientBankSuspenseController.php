<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BankTransaction;

class ClientBankSuspenseController extends Controller
{
    public function suspense(Request $request)
    {
        try {
            $auth = auth()->user();
            if (!$auth) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not authenticated',
                    'code' => 401
                ], 401);
            }

            $query = BankTransaction::where('is_suspense',1)
                ->where('iPartyId',$auth->id);
            /*
            |--------------------------------------------------------------------------
            | DATE FILTER
            |--------------------------------------------------------------------------
            */
            if ($request->from_date) {
                $query->whereDate('txn_date','>=',$request->from_date);
            }

            if ($request->to_date) {
                $query->whereDate('txn_date','<=',$request->to_date);
            }
            $transactions = $query->get()->map(function ($transaction) {
                if ($transaction->txn_type === 'Debit') {
                    $transaction->txn_type = 'Payment';
                } elseif ($transaction->txn_type === 'Credit') {
                    $transaction->txn_type = 'Receipt';
                }

                return $transaction;
            });
            // $transactions = $query->latest()
            //     ->paginate(
            //         $request->per_page ?? 10
            //     );

            return response()->json([
                'status' => true,
                'message' => 'Suspense transactions fetched successfully',
                'data' => $transactions,
                'code' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch suspense transactions',
                'error' => $e->getMessage(),
                'code' => 500
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Resolve Suspense API
    |--------------------------------------------------------------------------
    */
    public function resolveSuspense(Request $request)
    {
        try {
            $auth = auth()->user();
            if (!$auth) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not authenticated',
                    'code' => 401
                ], 401);
            }

            /*
            |--------------------------------------------------------------------------
            | BULK UPDATE
            |--------------------------------------------------------------------------
            */
            if ($request->has('txn_ids')) {
                $ids = $request->txn_ids;
                BankTransaction::whereIn('id', $ids)
                    ->where('iPartyId', $auth->id)
                    ->update([
                        'is_suspense' => 0,
                        'resolution_remark' => $request->remark
                    ]);
                return response()->json([
                    'status' => true,
                    'type' => 'bulk',
                    'message' => 'Suspense resolved successfully',
                    'code' => 200
                ], 200);
            }

            /*
            |--------------------------------------------------------------------------
            | SINGLE UPDATE
            |--------------------------------------------------------------------------
            */
            if ($request->has('txn_id')) {
                $row = BankTransaction::where('id',$request->txn_id)
                    ->where('iPartyId',$auth->id)
                    ->first();

                if (!$row) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Transaction not found',
                        'code' => 404
                    ], 404);
                }
                $row->update([
                    'is_suspense' => 0,
                    'resolution_remark' => $request->remark
                ]);
                return response()->json([
                    'status' => true,
                    'type' => 'single',
                    'message' => 'Suspense resolved successfully',
                    'code' => 200
                ], 200);
            }

            return response()->json([
                'status' => false,
                'message' => 'No transaction data found',
                'code' => 422
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to resolve suspense',
                'error' => $e->getMessage(),
                'code' => 500
            ], 500);
        }
    }
}
