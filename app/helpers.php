<?php

use Carbon\Carbon;

if (! function_exists('rupiah')) {
    function rupiah(float|int|string|null $value, bool $withPrefix = true): string
    {
        $formatted = number_format((float) $value, 0, ',', '.');

        return $withPrefix ? 'Rp '.$formatted : $formatted;
    }
}

if (! function_exists('tanggal_id')) {
    function tanggal_id(\DateTimeInterface|string|null $date, string $format = 'd M Y'): string
    {
        if (! $date) {
            return '-';
        }

        return Carbon::parse($date)->locale('id')->translatedFormat($format);
    }
}

if (! function_exists('bulan_id')) {
    function bulan_id(int $month): string
    {
        return Carbon::create()->month($month)->locale('id')->translatedFormat('F');
    }
}

if (! function_exists('owner_share_percent')) {
    function owner_share_percent(): string
    {
        return rtrim(rtrim(number_format((float) config('rental.owner_share_percent', 70), 2, '.', ''), '0'), '.');
    }
}

if (! function_exists('admin_share_percent')) {
    function admin_share_percent(): string
    {
        return rtrim(rtrim(number_format(100 - (float) config('rental.owner_share_percent', 70), 2, '.', ''), '0'), '.');
    }
}
