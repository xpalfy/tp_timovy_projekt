<?php
// Generate a secure random key
$secureKey = bin2hex(openssl_random_pseudo_bytes(32)); // Generates a 64-character hexadecimal string
echo "Your generated key: " . $secureKey . PHP_EOL;
