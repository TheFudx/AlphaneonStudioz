<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\Package;
Use App\Models\Type;
use Illuminate\Http\Request;
class SubscriptionPackageController extends Controller
{
    public function packageData()
    {
        try {
            $packages = Package::all();
    
            if ($packages->isEmpty()) {
                return response()->json([
                    'status' => 400,
                    'message' => 'No packages found.',
                    'data' => []
                ], 400);
            }
            $packagesWithTypes = $packages->map(function ($package) {
                $typeIds = explode(',', $package->type_id);
                $types = Type::whereIn('id', $typeIds)->pluck('name');
                $package->type_names = $types;
                return $package;
            });
    
            return response()->json([
                'status' => 200,
                'message' => 'Package Details',
                'data' => $packagesWithTypes
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Internal server error.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
