<?php
include 'trackjs.php';

if (isset($_GET['accountId'])) {
    echo "PostAffTracker.setAccountId('".$_GET['accountId']."');\n"; 
}
?>
PostAffTracker.notifySale();
