<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$part2 = App\Models\Event::where('name', 'like', '% - Part 2%')->first();
if ($part2) {
    $parentName = str_replace(' - Part 2', '', $part2->name);
    $parent = App\Models\Event::where('name', $parentName)->first();
    if ($parent) {
        $part2->parent_id = $parent->id;
        $part2->save();
        echo "Successfully linked {$part2->name} to {$parent->name}!\n";
    }
}
