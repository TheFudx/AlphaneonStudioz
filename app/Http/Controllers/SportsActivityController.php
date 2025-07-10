<?php
namespace App\Http\Controllers;
use App\Models\Category;
use App\Models\Video;
use App\Models\Notification;
use App\Models\Watchlists;
use App\Models\SportsArmWrestlingRegistration;
use Illuminate\Http\Request;
use Razorpay\Api\Api;
use Barryvdh\DomPDF\Facade\Pdf;
use Session;
class SportsActivityController extends Controller
{
    public function comingSoonPage(){
        
        
        return view('coming-soon', [ ]);
    }
    public function index(){
        
        
        $totalRegistrations = SportsArmWrestlingRegistration::count();
        $watchlist = app('wishlist');
        session(['watchlist' => $watchlist]);
        return view('sports', [  'watchlist' => $watchlist, 'totalRegistrations' => $totalRegistrations]);
    }
    
    public function register(Request $request)
{
    // Validate form data
    $request->validate([
        'fullname'            => 'required|string|max:255',
        'email'                => 'required|email|max:255',
        'age'                  => 'required|integer|min:18',
        'weight'               => 'required|numeric|min:1',
        'dhand'               => 'required|string',
        'experience'           => 'required|string',
        'injury'               => 'required|string',
        'mobile'               => 'required|digits:10',
        'razorpay_payment_id'  => 'required|string',
        'amount'               => 'required|numeric|min:1',
    ]);
    $payment_id = $request->input('razorpay_payment_id');
    // Check if this payment_id has already been used
    $existingRegistration = SportsArmWrestlingRegistration::where('payment_id', $payment_id)->first();
    if ($existingRegistration) {
        return redirect()->route('registration.success', ['id' => $existingRegistration->id, 'name' => $existingRegistration->fullname ])->with('success', 'Registration successful!');
    }
    $amount = $request->input('amount');
    // Initialize Razorpay API
    $api = new Api('rzp_test_FZs1ciE3h1febU', 'Np504J9Rq1L28GkNdPhqNita');
    try {
        // Fetch payment details
        $payment = $api->payment->fetch($payment_id);
        if ($payment->status == 'authorized' || $payment->status == 'captured') {
            // Capture the payment if not already captured
            if ($payment->status == 'authorized') {
                $payment->capture(['amount' => $payment->amount]);
            }
            // Store registration data
            $registration = SportsArmWrestlingRegistration::create([
                'fullname'  => $request->fullname,
                'email'      => $request->email,
                'age'        => $request->age,
                'weight'     => $request->weight,
                'dhand'     => $request->dhand,
                'experience' => $request->experience,
                'injury'     => $request->injury,
                'mobile'     => $request->mobile,
                'payment_id' => $payment_id,
                'amount'     => $amount,
            ]);
            return redirect()->route('registration.success', ['id' => $registration->id, 'name' => $registration->fullname ])->with('success', 'Registration successful!');
        } else {
            return redirect()->back()->with('error', 'Payment failed or incomplete!');
        }
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
    }
}
public function successArmReg($name, $id)
{
    $registration = SportsArmWrestlingRegistration::findOrFail($id);
    
    
    return view('sportsregistsuccess', compact('registration',  'notify'));
}
public function downloadPdf($id)
{
    $registration = SportsArmWrestlingRegistration::findOrFail($id);
    
    $pdf = Pdf::loadView('arm-reg-pdf', compact('registration'));
    return $pdf->download('registration_details.pdf');
}
}
