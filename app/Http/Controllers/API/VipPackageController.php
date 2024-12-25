<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VipPackage;

class VipPackageController extends Controller
{
    public function index()
    {
        try {
            $packages = VipPackage::all();
            return response()->json([
                'status' => 'success',
                'data' => $packages
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $package = VipPackage::findOrFail($id);
            return response()->json([
                'status' => 'success',
                'data' => $package
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}