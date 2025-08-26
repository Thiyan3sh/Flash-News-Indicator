<?php
$path = "live_preview.txt";
if (file_exists($path)) {
    echo "<pre>" . htmlspecialchars(file_get_contents($path)) . "</pre>";
} else {
    echo "Live preview file not found.";
}
?>
