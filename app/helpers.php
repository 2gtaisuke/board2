<?php

use Illuminate\Support\Facades\Storage;

if (!function_exists('get_user_profile_image')) {
    /**
     * ユーザーのプロフィール画像のパスを返す
     *
     * @param string|null $path
     * @return string
     */
    function get_user_profile_image($path): string
    {
        return Storage::url($path ?? config('app.unknown_user_profile_image'));
    }
}