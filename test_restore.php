<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Storage;

$files = Storage::disk('google')->allFiles();
$zips = array_filter($files, fn($f) => str_ends_with($f, '.zip'));
rsort($zips);
$latest = $zips[0] ?? null;

if (!$latest) {
    die("No zip found");
}

echo "Restoring: " . $latest . "\n";

$service = app(App\Services\Backups\BackupService::class);
$result = $service->restoreBackup(base64_encode($latest));
dump($result);
