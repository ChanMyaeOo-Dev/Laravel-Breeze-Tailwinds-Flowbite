<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command("php -d memory_limit=1G artisan ml:train-sentiment --test-size=0.2")
    ->timezone('Asia/Yangon')
    ->monthly()
    ->withoutOverlapping();

Schedule::command("php artisan feedback:analyze-ml --batch-size=200")
    ->timezone('Asia/Yangon')
    ->dailyAt("22:00")
    ->withoutOverlapping();
