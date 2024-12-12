<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'query' => 'required|min:2|max:50'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid input',
                'errors' => $validator->errors()
            ], 422);
        }

        $query = $request->input('query');
        
        try {
            if (is_numeric($query)) {
                // Query A - For GLN search
                $results = DB::table('tblcompanies')
                    ->select('informationProvider', 'companyName')
                    ->where('GLN', $query)
                    ->get();
            } else {
                // Query B - For company name search
                $results = DB::table('tblcompanies')
                    ->select('informationProvider', 'companyName')
                    ->where('companyName', 'LIKE', "%{$query}%")
                    ->get();
            }

            return response()->json([
                'status' => 'success',
                'data' => $results
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Database error occurred'
            ], 500);
        }
    }
}
