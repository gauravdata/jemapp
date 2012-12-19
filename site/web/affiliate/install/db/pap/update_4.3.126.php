<?php

class pap_update_4_3_126 {
    public function execute() {
        $impressionProcessor = new Pap_Tracking_Impression_ImpressionProcessor();
        $impressionProcessor->insertTask();
        
        $visitProcessor = new Pap_Tracking_Visit_Processor();
        $visitProcessor->insertTask();
    }
}
?>
