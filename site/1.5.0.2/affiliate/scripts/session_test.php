<?php

session_start();

if (!isset($_SESSION['a'])) {
  $_SESSION['a'] = 0;
}

echo "Refresh this page and the number below should be incremented by 1:<br>";
echo $_SESSION['a']++;

?>
