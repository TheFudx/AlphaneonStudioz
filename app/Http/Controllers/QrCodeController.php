<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\QrCode;
use SimpleSoftwareIO\QrCode\Facades\QrCode as QrCodeGenerator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\AppClick;

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

        $fileName = 'qr_' . Str::random(10) . '.svg';
        $path = 'qrcodes/' . $fileName;

        // SVG format — no imagick or GD extension required
        $qr = QrCodeGenerator::format('svg')->size(200)->generate($request->url);
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

        if (Storage::disk('public')->exists($qr->qr_code_path)) {
            Storage::disk('public')->delete($qr->qr_code_path);
        }

        $qr->delete();

        return redirect()->back()->with('success', 'QR Code deleted successfully!');
    }

    public function redirectToStore(Request $request)
    {
        $androidLink = config('app.android_app_url', 'https://play.google.com/store/apps/details?id=com.alphastudioz.alphaneonstudioz');
        $iosLink = config('app.ios_app_url', 'https://apps.apple.com/app/id6723896639');

        $ua = strtolower($request->userAgent() ?? '');

        if (str_contains($ua, 'iphone') || str_contains($ua, 'ipad') || str_contains($ua, 'ipod')) {
            $device = 'ios';
            $redirectUrl = $iosLink;
        } elseif (str_contains($ua, 'android')) {
            $device = 'android';
            $redirectUrl = $androidLink;
        } else {
            $device = 'other';
            $redirectUrl = $androidLink;
        }

        try {
            AppClick::create([
                'device_type' => $device,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
        } catch (\Exception $e) {
            // Ignore if DB log fails
        }

        return redirect()->away($redirectUrl);
    }

    public function storeUniversalQr()
    {
        $fileName = 'universal_qr_' . Str::random(8) . '.svg';
        $path = 'qrcodes/' . $fileName;

        $redirectUrl = route('qrcode.redirect');

        // SVG format — no imagick or GD extension required
        $qr = QrCodeGenerator::format('svg')->size(200)->generate($redirectUrl);
        Storage::disk('public')->put($path, $qr);

        QrCode::create([
            'url' => $redirectUrl,
            'qr_code_path' => $path,
        ]);

        return redirect()->back()->with('success', 'Universal QR Code generated!');
    }

    public function storeAppLinks(Request $request)
    {
        $request->validate([
            'android_url' => 'nullable|url',
            'ios_url' => 'nullable|url',
        ]);

        return redirect()->back()->with('success', 'App links updated successfully!');
    }

    public function redirectToStoreUpdatedLink()
    {
        $androidLink = 'https://play.google.com/store/apps/details?id=com.alphastudioz.alphaneonstudioz';
        $iosLink = 'https://apps.apple.com/app/id6723896639';

        $ua = strtolower(request()->userAgent());

        if (str_contains($ua, 'iphone') || str_contains($ua, 'ipad') || str_contains($ua, 'ipod')) {
            return redirect()->away($iosLink);
        } elseif (str_contains($ua, 'android')) {
            return redirect()->away($androidLink);
        } else {
            return redirect()->to(config('app.website_url'));
        }
    }

    public function redirectToStoreLink()
    {
        $androidLink = 'https://play.google.com/store/apps/details?id=com.alphastudioz.alphaneonstudioz';
        $iosLink = 'https://apps.apple.com/app/id6723896639';

        $ua = strtolower(request()->userAgent());

        if (str_contains($ua, 'iphone') || str_contains($ua, 'ipad') || str_contains($ua, 'ipod')) {
            return redirect()->away($iosLink);
        } elseif (str_contains($ua, 'android')) {
            return redirect()->away($androidLink);
        } else {
            return redirect()->to(config('app.website_url'));
        }
    }
}