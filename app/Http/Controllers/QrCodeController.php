<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\QrCode;
use SimpleSoftwareIO\QrCode\Facades\QrCode as QrCodeGenerator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
class QrCodeController extends Controller
{
    public function index()
    {
        $qrcodes = QrCode::latest()->get();
        return view('qrcode', compact('qrcodes'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'url' => 'required|url'
        ]);
        $fileName = 'qr_' . Str::random(10) . '.png';
        $path = 'qrcodes/' . $fileName;
        // Generate QR and save to public storage
        $qr = QrCodeGenerator::format('png')->size(200)->generate($request->url);
        Storage::disk('public')->put($path, $qr);
        $qrRecord = QrCode::create([
            'url' => $request->url,
            'qr_code_path' => $path,
        ]);
        return redirect()->back()->with('success', 'QR Code generated!');
    }
    
    public function destroy($id)
    {
        $qr = QrCode::findOrFail($id);
    
        // Delete the image file
        if (Storage::disk('public')->exists($qr->qr_code_path)) {
            Storage::disk('public')->delete($qr->qr_code_path);
        }
    
        // Delete the database record
        $qr->delete();
    
        return redirect()->back()->with('success', 'QR Code deleted successfully!');
    }
    
    public function redirectToStore(Request $request)
    {
        // You can hardcode or fetch these from DB
        $androidLink = 'https://play.google.com/store/apps/details?id=com.alphastudioz.alphaneonstudioz';
        $iosLink = 'https://apps.apple.com/app/id6723896639';
    
        $userAgent = $request->userAgent();
    
        if (stripos($userAgent, 'iPhone') !== false || stripos($userAgent, 'iPad') !== false) {
            return redirect()->away($iosLink);
        } elseif (stripos($userAgent, 'Android') !== false) {
            return redirect()->away($androidLink);
        } else {
            return redirect()->to('https://alphastudioz.in/'); // fallback
        }
    }
    public function storeUniversalQr()
    {
        $fileName = 'universal_qr_' . Str::random(8) . '.jpg';
        $path = 'qrcodes/' . $fileName;
    
        $redirectUrl = route('qrcode.redirect'); // this is /redirect-to-store
    
  
        // Generate QR and save to public storage
        $qr = QrCodeGenerator::format('png')->size(200)->generate($redirectUrl);
        Storage::disk('public')->put($path, $qr);
       
    
        QrCode::create([
            'url' => $redirectUrl,
            'qr_code_path' => $path,
        ]);
    
        return redirect()->back()->with('success', 'Universal QR Code generated!');
    }
}
