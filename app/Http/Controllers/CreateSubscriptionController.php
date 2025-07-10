<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Razorpay\Api\Api;
class CreateSubscriptionController extends Controller
{
    private $razorpayKey;
    private $razorpaySecret;
    public function __construct()
    {
        $this->razorpayKey = env('RAZORPAY_KEY'); // Set this in your .env file
        $this->razorpaySecret = env('RAZORPAY_SECRET'); // Set this in your .env file
    }
    public function createSubscription(Request $request)
    {
        // Validate the incoming request (optional but recommended)
            $request->validate([
                'plan_id' => 'required|string',
                'total_count' => 'required|integer|min:1',
                'user_id' => 'required|string'
            ]);
        // Initialize Razorpay API
        $api = new Api($this->razorpayKey, $this->razorpaySecret);
        try {
            $planId = $request->input('plan_id');
            $totalCount = $request->input('total_count');
            // Subscription details
            $subscriptionData = [
                'plan_id' => $planId,    // Replace with your plan ID
                'customer_notify' => 1,
                'quantity' => 1,                       // Quantity of the subscription
                'total_count' => $totalCount,                    // Total billing cycles
                'start_at' => now()->addMinutes(5)->timestamp,  // Subscription starts 5 mins from now
                'addons' => [
                    [
                        'item' => [
                            'name' => 'Delivery charges',
                            'amount' => 500,          // ₹5 in paise (₹5 * 100)
                            'currency' => 'INR'
                        ]
                    ]
                ],
                'notes' => [
                    'user_id' => $request->input('user_id'),
                    'key2' => 'value2'
                ]
            ];
            // Create subscription
            $subscription = $api->subscription->create($subscriptionData);
            return response()->json([
                'success' => true,
                'message' => 'Subscription created successfully!',
                'subscription' => $subscription
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Subscription creation failed!',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
