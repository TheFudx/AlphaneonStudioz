<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Razorpay\Api\Api;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class ProfileUpdateController extends Controller
{
    public function deleteUserAccount(Request $request)
    {
        // Manual check instead of automatic validation
        if (!$request->has('user_id') || empty($request->user_id)) {
            return response()->json([
                'status' => 'failed',
                'message' => 'User ID is required.'
            ], 200);
        }
    
        $user = User::find($request->user_id);
    
        if (!$user) {
            return response()->json([
                'status' => 'failed',
                'message' => 'User ID not found in system.'
            ], 200);
        }
    
        try {
            // If user has an active subscription, cancel it
            if (!empty($user->razorpay_subscription_id)) {
    
                $api = new Api(config('app.razorpay_key'), config('app.razorpay_secret')); 
                $subscription = $api->subscription->fetch($user->razorpay_subscription_id);
    
                if ($subscription) {
                    $subscription->cancel(['cancel_at_cycle_end' => false]);
    
                    DB::table('users')
                        ->where('id', $user->id)
                        ->update([
                            'subscription' => 'No',
                            'razorpay_subscription_id' => null,
                            'subscription_end_date' => now(),
                        ]);
    
                    DB::table('transaction')
                        ->where('user_id', $user->id)
                        ->where('status', 1)
                        ->update([
                            'status' => 0,
                            'expiry_date' => now(),
                        ]);
    
                    DB::table('user_subscriptions')
                        ->where('user_id', $user->id)
                        ->delete();
                }
            }
    
            // Finally delete the user account
            $user->delete();
    
            return response()->json([
                'status' => 'success',
                'message' => 'User subscription cancelled and account deleted successfully.'
            ]);
    
        } catch (\Exception $e) {
            Log::error('Delete user API error: ' . $e->getMessage());
    
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete account. Please try again later.'
            ], 500);
        }
    }
    
}
