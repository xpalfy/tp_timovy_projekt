<?php
define('PYTHON_EXECUTABLE', 'python3');
define('PYTHON_SCRIPT', 'test.py');

$x = "as";
$output =exec(PYTHON_EXECUTABLE . ' ' . PYTHON_SCRIPT . ' ' . $x);
echo $output;
?>

<footer class="footer bg-dark">
    © Proje      ct Site <a href="https://tptimovyprojekt.ddns.net/">tptimovyprojekt.ddns.net</a>
</footer>