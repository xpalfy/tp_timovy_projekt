<?php
$secureKey = bin2hex(openssl_random_pseudo_bytes(32)); 
echo "Your generated key: " . $secureKey . PHP_EOL;
