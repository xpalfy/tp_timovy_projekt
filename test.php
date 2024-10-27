<?php
define('PYTHON_EXECUTABLE', 'C:\\Python312\\python.exe');
define('PYTHON_SCRIPT', 'test.py');

$x = "as";
$output =exec(PYTHON_EXECUTABLE . ' ' . PYTHON_SCRIPT . ' ' . $x);
echo $output;
?>

<footer class="footer bg-dark">
    © Project Site <a href="https://tptimovyprojekt.ddns.net/">tptimovyprojekt.ddns.net</a>
</footer>