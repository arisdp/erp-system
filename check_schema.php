<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$columns = DB::select("
    SELECT column_name, data_type, udt_name 
    FROM information_schema.columns 
    WHERE table_name = 'audit_logs'
");

foreach ($columns as $column) {
    echo "Column: {$column->column_name} | Type: {$column->data_type} | UDT: {$column->udt_name}\n";
}
