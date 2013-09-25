<?php
/**
 * Update step will delete plugin configuration file in every account
 *
 */
class gpf_update_1_1_3 {
    public function execute() {
        try {
            Gpf_DbEngine_Database::getDatabase()->execute('ALTER TABLE qu_g_logs DROP INDEX `IDX_qu_g_logs_1`');
        } catch (Exception $e) {
        }
        Gpf_DbEngine_Database::getDatabase()->execute('ALTER TABLE qu_g_logs ADD INDEX `IDX_qu_g_logs_1` (`accountuserid` ASC, `created` ASC)');
    }
}
?>
