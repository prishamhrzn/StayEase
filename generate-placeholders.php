<?php
// generate-placeholders.php
// Run this ONCE from browser: http://localhost/stayease/generate-placeholders.php
// It creates placeholder hotel images so the site renders without real photos.

$dir = __DIR__ . '/images/hotels/';
if (!is_dir($dir)) mkdir($dir, 0755, true);

$hotels = [
    'grand_himalayan'  => ['#1B4332', '#C9A84C', 'The Grand Himalayan'],
    'azure_shores'     => ['#0C4A6E', '#38BDF8', 'Azure Shores Resort'],
    'heritage_courtyard'=> ['#78350F', '#FCD34D', 'Heritage Courtyard Inn'],
    'mountain_breeze'  => ['#1E3A5F', '#93C5FD', 'Mountain Breeze Lodge'],
    'urban_nest'       => ['#374151', '#D1D5DB', 'Urban Nest Hotel'],
    'tranquil_valley'  => ['#14532D', '#86EFAC', 'Tranquil Valley Retreat'],
];

foreach ($hotels as $filename => [$bg, $accent, $label]) {
    $file = $dir . $filename . '.jpg';
    if (!file_exists($file)) {
        // Create a simple colored SVG and save as "image"
        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="800" height="500">
  <rect width="800" height="500" fill="{$bg}"/>
  <rect x="0" y="380" width="800" height="120" fill="rgba(0,0,0,0.4)"/>
  <text x="400" y="280" font-family="Georgia, serif" font-size="52" font-weight="bold"
        fill="{$accent}" text-anchor="middle">🏨</text>
  <text x="400" y="440" font-family="Georgia, serif" font-size="28" font-weight="bold"
        fill="white" text-anchor="middle">{$label}</text>
</svg>
SVG;
        // Save as SVG with .jpg name (browsers will still display it)
        file_put_contents($file, $svg);
        echo "Created: $filename.jpg<br>";
    } else {
        echo "Exists: $filename.jpg<br>";
    }
}

// Create default.jpg
$default = $dir . 'default.jpg';
if (!file_exists($default)) {
    $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="800" height="500"><rect width="800" height="500" fill="#1B4332"/><text x="400" y="260" font-family="Georgia" font-size="60" fill="#C9A84C" text-anchor="middle">🏨</text><text x="400" y="320" font-family="Georgia" font-size="24" fill="white" text-anchor="middle">StayEase Hotel</text></svg>';
    file_put_contents($default, $svg);
    echo "Created: default.jpg<br>";
}

echo "<br><strong style='color:green'>✓ All placeholder images created!</strong><br>";
echo "<a href='index.php'>→ Go to Homepage</a>";
?>
