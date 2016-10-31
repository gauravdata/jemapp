<pre>
<?php

if ($_GET['key'] != 'OGbH43x2Jp') die();

opcache_reset();
apc_clear_cache();

echo shell_exec('redis-cli FLUSHALL');
echo shell_exec('n98-magerun --root-dir=/home/notomatoes/domains/jemappelle.nl/public_html/ c:f');
echo shell_exec('n98-magerun --root-dir=/home/notomatoes/domains/jemappelle.nl/public_html/ c:cl');

//echo shell_exec('n98-magerun --root-dir=/home/test/public_html/ c:f');
//echo shell_exec('n98-magerun --root-dir=/home/test/public_html/ c:cl');
?>
<pre>
