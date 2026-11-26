<?php
use App\Models\VisitRecord;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
Route::get('/', function () {
    return view('welcome');
});
Route::get('/lang/{locale}', function (string $locale) {
    if (in_array($locale, ['ar', 'en'])) {
        Session::put('locale', $locale);
    }

    return redirect()->back();
})->name('lang.switch');



Route::get('/visit-records/{visitRecord}/print-badge', function (VisitRecord $visitRecord) {
    $visitRecord->load(['visitor', 'directorate', 'room.department']);

    // المحتوى داخل الـ QR (إنجليزي منظم لضمان دقة السكانر)
    $qrPayload = "BADGE:{$visitRecord->badge_number}|ID:{$visitRecord->id}|ROOM:{$visitRecord->room?->room_number}";

    $renderer = new ImageRenderer(
        new RendererStyle(140, 1),
        new SvgImageBackEnd()
    );
    $writer = new Writer($renderer);
    $qrSvg = $writer->writeString($qrPayload);

    return view('badges.visit-badge', [
        'visit' => $visitRecord,
        'qrSvg' => $qrSvg,
    ]);
})->middleware(['auth'])->name('visit-records.print-badge');