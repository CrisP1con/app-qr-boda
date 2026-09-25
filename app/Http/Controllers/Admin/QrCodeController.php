<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\QrCodeService;
use Illuminate\Http\Response;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class QrCodeController extends Controller
{
    public function show(QrCodeService $qrCodeService): InertiaResponse
    {
        return Inertia::render('Admin/QrCode', [
            'destination' => $qrCodeService->destination(),
            'png_data_uri' => $qrCodeService->result('png')->getDataUri(),
            'svg_data_uri' => $qrCodeService->result('svg')->getDataUri(),
            'png_download_url' => route('admin.qr.download', ['format' => 'png']),
            'svg_download_url' => route('admin.qr.download', ['format' => 'svg']),
        ]);
    }

    public function download(string $format, QrCodeService $qrCodeService): Response
    {
        abort_unless(in_array($format, ['png', 'svg'], true), 404);

        $result = $qrCodeService->result($format);
        $extension = $format === 'png' ? 'png' : 'svg';

        return response($result->getString(), 200, [
            'Content-Type' => $result->getMimeType(),
            'Content-Disposition' => "attachment; filename=qr-boda.{$extension}",
        ]);
    }
}
