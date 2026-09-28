<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Type;
use App\Models\User;
use App\Models\Transction;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class SubscriptionPackageController extends Controller
{
    public function packageData(Request $request)
    {
        try {
            $userId = null;

            $bearerToken = $request->bearerToken();
            if ($bearerToken) {
                $accessToken = PersonalAccessToken::findToken($bearerToken);
                if ($accessToken) {
                    $userId = $accessToken->tokenable_id;
                }
            }

            if (!$userId && ($request->has('user_id') || $request->has('id'))) {
                $userId = $request->user_id ?? $request->id;
            }

            if (!$userId && auth('sanctum')->check()) {
                $userId = auth('sanctum')->id();
            }

            if (!$userId && auth()->check()) {
                $userId = auth()->id();
            }

            if (!$userId && session()->has('user_id')) {
                $userId = session('user_id');
            }

            // Ensure $userId is integer for array comparison
            $userIdInt = $userId ? (int) $userId : null;

            // Allowed user IDs for special/trial plans (1rs, 2rs)
            $allowedUserIds = [443, 1112, 1406, 680, 545, 1293, 1365, 1664];

            $query = Package::query()->where('is_delete', 0)->where('status', 1);

            // Hide trial/special packages (price <= 2 or package IDs 4, 5) for non-allowed users
            if (!$userIdInt || !in_array($userIdInt, $allowedUserIds)) {
                $query->where(function ($q) {
                    $q->where('price', '>', 2)
                      ->whereNotIn('id', [4, 5]);
                });
            }

            // Upgrade filtering: If user is actively subscribed, show only higher-priced plans for upgrade
            if ($userIdInt && (!$request->has('all') || $request->all != '1')) {
                $currentUser = User::find($userIdInt);

                if ($currentUser && $currentUser->subscription === 'Yes') {
                    // Find active package from transaction or user_subscriptions
                    $activeTransaction = Transction::where('user_id', $userIdInt)
                        ->where('status', 1)
                        ->latest('id')
                        ->first();

                    $activeUserSub = UserSubscription::where('user_id', $userIdInt)
                        ->where('status', 1)
                        ->latest('id')
                        ->first();

                    $activePackageId = $activeTransaction?->package_id ?? $activeUserSub?->package_id ?? null;

                    if ($activePackageId) {
                        $activePackage = Package::find($activePackageId);
                        if ($activePackage) {
                            $query->where('price', '>', $activePackage->price)
                                  ->where('id', '!=', $activePackage->id);
                        }
                    }
                }
            }

            $packages = $query->get();

            if ($packages->isEmpty()) {
                return successResponse([], 'No packages found.');
            }

            $packagesWithTypes = $packages->map(function ($package) {
                $typeIds = explode(',', $package->type_id);
                $types = Type::whereIn('id', $typeIds)->pluck('name');
                $package->type_names = $types;
                return $package;
            });

            return successResponse($packagesWithTypes, 'Success');

        } catch (\Exception $e) {
            return errorResponse('Internal server error: ' . $e->getMessage(), 500);
        }
    }
}