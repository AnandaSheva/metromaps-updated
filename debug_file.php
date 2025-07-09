<?php
$filename = 'PXL_20240517_011540362.PHOTOSPHERE';
$publicPath = realpath(__DIR__ . '/public');
$assetPathJpg = $publicPath . "/assets/sphere/{$filename}.jpg";

echo "Filename: {$filename}\n";
echo "Public path: {$publicPath}\n";
echo "Full path: {$assetPathJpg}\n";
echo "File exists: " . (file_exists($assetPathJpg) ? 'YES' : 'NO') . "\n";

// Check actual file
$actualFile = $publicPath . "/assets/sphere/PXL_20240517_011540362.PHOTOSPHERE.jpg";
echo "Actual file: {$actualFile}\n";
echo "Actual exists: " . (file_exists($actualFile) ? 'YES' : 'NO') . "\n";

// List all files that match pattern
$pattern = $publicPath . "/assets/sphere/PXL_20240517_01154*";
$files = glob($pattern);
echo "Matching files:\n";
foreach ($files as $file) {
    echo "  " . basename($file) . "\n";
}
