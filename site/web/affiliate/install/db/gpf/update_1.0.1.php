<?php
/**
 * Update step will delete plugin configuration file in every account 
 *
 */
class gpf_update_1_0_1 {
    public function execute() {
        $dir = new Gpf_Io_DirectoryIterator(Gpf_Paths::getInstance()->getAccountsPath(), '', true);
        foreach ($dir as $fullPath => $filename) {
        	if ($filename == 'engineconfig.php') {
        	    $file = new Gpf_Io_File($fullPath);
        	    $file->delete();
        	}
        }
    }
}
?>
