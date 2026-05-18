<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$events = App\Models\Event::orderBy('id', 'desc')->take(3)->get();
foreach ($events as $e) {
    $judges = App\Models\User::where('role', 'judge')->where('event_id', $e->id)->count();
    echo "Event {$e->id}: {$e->name} | Judges assigned: {$judges}\n";
}
