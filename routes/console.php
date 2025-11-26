<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Agendar envio de reminders de devolução todos os dias às 9h
Schedule::command('reminders:devolucao')->dailyAt('09:00');

// Sincronizar livros ativos semanalmente aos domingos às 3h da manhã

Schedule::command('sync:active-books --queue')

    ->weekly()
    ->sundays()
    ->at('03:00')
    ->withoutOverlapping();
