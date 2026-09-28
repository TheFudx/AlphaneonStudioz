<?php
namespace App\Http\Controllers;
use App\Models\Transction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Category;
use App\Models\Notification;
class TransactionController extends Controller
{
    public function thankyou(Request $request)
    {
        $user = auth()->user() ?? (app()->bound('logged-in-user') ? app('logged-in-user') : null);
        $userId = $user?->id ?? session('user_id');

        $transaction = null;
        if ($userId) {
            $transaction = Transction::where('user_id', $userId)
                ->where('status', 1)
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$transaction) {
                $transaction = Transction::where('user_id', $userId)
                    ->orderBy('created_at', 'desc')
                    ->first();
            }
        }

        if (!$transaction && $request->filled('payment_id')) {
            $transaction = Transction::where('payment_id', $request->payment_id)->first();
        }

        if (!$transaction && $request->filled('subscription_id')) {
            $transaction = Transction::where('razorpay_subscription_id', $request->subscription_id)->first();
        }

        if (!$user && $transaction && $transaction->user_id) {
            $user = User::find($transaction->user_id);
            if ($user && !auth()->check()) {
                auth()->login($user);
            }
        }

        return view('thankyou', [
            'transaction' => $transaction,
            'user' => $user
        ]);
    }
     
   
}
