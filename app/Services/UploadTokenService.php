<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

class UploadTokenService
{
    private const COOKIE_NAME = 'upload_token';

    public function resolve(Request $request): string
    {
        return $request->cookie(self::COOKIE_NAME) ?? Str::random(64);
    }

    public function remember(string $token): void
    {
        Cookie::queue(cookie(
            self::COOKIE_NAME,
            $token,
            60 * 24 * 30,
            '/',
            null,
            app()->environment('production'),
            true,
            false,
            'lax',
        ));
    }
}
