<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $event = App\Models\Event::first();
    $criterias = App\Models\Criteria::where('event_id', $event->id)->get();
    $results = []; // mock empty results
    $pdf = Barryvdh\DomPDF\Facade\Pdf::loadView('admin.tabulation.print', compact('event', 'results', 'criterias'));
    file_put_contents('test.pdf', $pdf->output());
    echo 'Done!';
} catch (\Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n" . $e->getTraceAsString();
} catch (\Error $e) {
    echo 'Fatal Error: ' . $e->getMessage() . "\n" . $e->getTraceAsString();
}
