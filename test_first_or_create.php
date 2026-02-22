<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use SimpleChat\Models\Conversation;

// Assuming there's a soft deleted conversation...
$c = Conversation::withTrashed()->first();
if ($c) {
    echo "ID: " . $c->id . "\n";
    echo "Trashed: " . ($c->trashed() ? "Yes" : "No") . "\n";
    
    try {
        $found = Conversation::withTrashed()->firstOrCreate(['id' => $c->id], ['participants' => []]);
        echo "firstOrCreate withTrashed worked! Found ID: " . $found->id . "\n";
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
} else {
    echo "No conversation found to test.\n";
}
