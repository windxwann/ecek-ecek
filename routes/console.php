<?php

use App\Services\RentalService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// PRD 5.2: pemesanan yang sudah dikonfirmasi otomatis menjadi Ongoing
// ketika tanggal mulai penyewaan tiba.
Schedule::call(function () {
    app(RentalService::class)->syncOngoing();
})->everyMinute()->name('sync-ongoing-rentals')->withoutOverlapping();
