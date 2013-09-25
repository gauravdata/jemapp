<?php

require_once 'bootstrap.php';
Gpf_Session::create(new Pap_Tracking_ModuleBase(), null, false);

try {
    $replicator = new Pap_Features_SiteReplication_Replicator();
} catch (Gpf_Exception $e) {
    die('<h3 style="color: red;">Error: '.$e->getMessage().'</h3>');
}

if (!$replicator->shouldBeProcessed()) {
    $replicator->passthru();
    exit();
}

echo $replicator->getReplicatedContent();

?>
