<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CodeMasterController extends Controller
{
    public function search(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'search' => 'nullable|string|max:150',
        ]);
        

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Execute stored procedure
            //$results = DB::select('select * from ');
            DB::connection('sqlsrv')->getPdo();
            dd($request);
            $results = DB::select(
                'EXEC sp_SearchCodeMaster ?', 
                [$request->input('search')]
            );

            return response()->json([
                'success' => true,
                'data' => $results
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve code master records',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}