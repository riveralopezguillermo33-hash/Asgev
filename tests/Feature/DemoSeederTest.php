<?php

use Database\Seeders\DemoSeeder;
use Illuminate\Support\Facades\DB;

it('keeps the demo records in an opt-in seeder and creates the requested data volume', function () {
    $previousEnv = $_ENV['ASGEV_DEMO_PASSWORD'] ?? null;
    $previousServer = $_SERVER['ASGEV_DEMO_PASSWORD'] ?? null;
    $_ENV['ASGEV_DEMO_PASSWORD'] = 'LocalDemoPassword2026!';
    $_SERVER['ASGEV_DEMO_PASSWORD'] = 'LocalDemoPassword2026!';

    try {
        app(DemoSeeder::class)->run();
    } finally {
        if ($previousEnv === null) {
            unset($_ENV['ASGEV_DEMO_PASSWORD']);
        } else {
            $_ENV['ASGEV_DEMO_PASSWORD'] = $previousEnv;
        }

        if ($previousServer === null) {
            unset($_SERVER['ASGEV_DEMO_PASSWORD']);
        } else {
            $_SERVER['ASGEV_DEMO_PASSWORD'] = $previousServer;
        }
    }

    expect(DB::table('estudiantes')->count())->toBe(3)
        ->and(DB::table('pensiones')->count())->toBe(9)
        ->and(DB::table('competencias')->count())->toBe(4)
        ->and(DB::table('sesiones')->count())->toBe(6)
        ->and((float) DB::table('notas')->min('nota'))->toBe(0.0)
        ->and((float) DB::table('notas')->max('nota'))->toBe(20.0)
        ->and(DB::table('roles')->count())->toBe(6);
});
