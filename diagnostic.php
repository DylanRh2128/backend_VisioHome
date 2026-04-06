<?php
$file = 'backend/app/Http/Controllers/UsuarioController.php';
if (!file_exists($file)) {
    die("File not found\n");
}
$content = file_get_contents($file);
$lines = explode("\n", $content);

echo "Checking syntax...\n";
exec("php -l " . escapeshellarg($file), $output, $return);
echo implode("\n", $output) . "\n";

echo "Analyzing braces...\n";
$open = 0;
foreach ($lines as $i => $line) {
    $ln = $i + 1;
    $o = substr_count($line, '{');
    $c = substr_count($line, '}');
    $open += $o - $c;
    if ($open < 0) {
        echo "Line $ln: Negative brace balance ($open). Extra '}' found.\n";
        echo "Content: $line\n";
        $open = 0; // reset to keep searching
    }
}
echo "Final balance: $open\n";
