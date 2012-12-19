<?php
/**
 * Upgrading from PAP3 database
 */

require_once '../../scripts/bootstrap.php';

$time1 = microtime();

Gpf_Session::create();

$migration = new Pap3Compatibility_Migration_UpgradeFromPap3();
$finished = false;

try {

	$migration->run();
	$finished = true;

} catch (Gpf_Tasks_LongTaskInterrupt $e) {
?>
<hr>
Data migration was interrupted due to PHP timeout - this is normal, it is not an error.
Migration is not finished, please wait for page refresh.
<meta http-equiv="refresh" content="5" />
<br/>
If the page doesn't refresh within 5 second, click on the link <a href="pap3topap4.php">refresh</a>
</hr>
<?php
} catch (Exception $e) {
    die('ERROR: ' . $e->getMessage());
}

if($finished) {
?>
<hr>
DATA MIGRATION FINISHED SUCCESSFULLY
<hr>
<?php
}
?>
