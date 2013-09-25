<?php
interface Lib_ConvertableToArray {
    public function toArray();
}

class Lib_ImpParams implements Lib_ConvertableToArray {
    private $date = '';
    private $rtype = '';
    private $userid = '';
    private $bannerid = '';
    private $parentbannerid = '';
    private $channel = '';
    private $ip = '';
    private $data1 = '';
    private $data2 = '';

    public function setData2($data2) {
        $this->data2 = $data2;
    }

    public function getData2() {
        return $this->data2;
    }
    
    public function setData1($data1) {
        $this->data1 = $data1;
    }

    public function getData1() {
        return $this->data1;
    }
    
    public function setIp($ip) {
        $this->ip = $ip;
    }

    public function getIp() {
        return $this->ip;
    }
    
    public function setChannel($channel) {
        $this->channel = $channel;
    }

    public function getChannel() {
        return $this->channel;
    }
    
    public function setParentbannerid($parentbannerid) {
        $this->parentbannerid = $parentbannerid;
    }

    public function getParentbannerid() {
        return $this->parentbannerid;
    }
    
    public function setBannerid($bannerid) {
        $this->bannerid = $bannerid;
    }

    public function getBannerid() {
        return $this->bannerid;
    }
    
    public function setUserid($userid) {
        $this->userid = $userid;
    }

    public function getUserid() {
        return $this->userid;
    }
    
    public function setRtype($rtype) {
        $this->rtype = $rtype;
    }

    public function getRtype() {
        return $this->rtype;
    }
    
    public function setDate($date) {
        $this->date = $date;
    }

    public function getDate() {
        return $this->date;
    }
    
    public function toArray() {
        $variables = array();
        foreach ($this as $var => $value) {
            $variables[$var] = $value;
        }
        return $variables;
    }
}

class Lib_Server {
    public static function getRemoteIp() {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return @$_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        if (isset($_SERVER['REMOTE_ADDR'])) {
            return @$_SERVER['REMOTE_ADDR'];
        }
        return '';
    }

    public static function getUserAgent() {
        return @$_SERVER['HTTP_USER_AGENT'];
    }

    public static function getReferer() {
        return @$_SERVER['HTTP_REFERER'];
    }
}

class Lib_SettingFile {
    const PARAM_NAME_BANNER_ID = 'param_name_banner_id';
    const PARAM_NAME_USER_ID = 'param_name_user_id';
    const PARAM_NAME_ROTATOR_ID = 'param_name_rotator_id';

    /**
     * @var Lib_Db
     */
    private $db;
    protected $fileName;
    static protected $values = null;

    public function __construct($fileName = '../accounts/settings.php') {
        $this->fileName = $fileName;
    }

    public function setValues($values) {
        self::$values = $values;
    }

    public function load() {
        if(self::$values !== null) {
            return;
        }
        self::$values = array();
        $lines = $this->getFileContent();

        foreach($lines as $line) {
            if(false !== strpos($line, '<?') || false !== strpos($line, '?>')) {
                continue;
            }
            $pos = strpos($line, '=');
            if($pos === false) {
                continue;
            }
            $name = substr($line, 0, $pos);
            $value = substr($line, $pos + 1);
            self::$values[$name] = rtrim($value);
        }
        $this->setTimezone();
    }

    public function get($name) {
        return @self::$values[$name];
    }

    public function set($name, $value) {
        self::$values[$name] = $value;
    }

    public function isOfflineVisitProcessing(){
        if($this->isOfflineVisitDisabled()){
            return false;
        }
        return $this->isCronRunning();
    }

    public function isOfflineImpressionProcessing(){
        return $this->isCronRunning();
    }

    private function isCronRunning() {
        return (@self::$values['cronLastRun'] != '' && time() < strtotime(self::$values['cronLastRun']) + 600);
    }

    private function isOfflineVisitDisabled() {
        if(isset(self::$values['offlineVisitProcessingDisabled'])){
            return self::$values['offlineVisitProcessingDisabled'] == 'Y';
        }
        return false;
    }

    /**
     *
     * @return Lib_Db
     */
    public function getDb() {
        if ($this->db == null) {
            $this->db = new Lib_Db($this);
        }
        return $this->db;
    }

    public function saveVisit(Lib_VisitParams $visitParams, $tableIndex) {
        $this->getDb()->saveToDb($visitParams, 'qu_pap_visits' . $tableIndex);
    }

    protected function getFileContent() {
        $lines = file($this->fileName);
        return $lines;
    }

    private function setTimezone() {
        if (@self::$values['TIMEZONE'] != '') {
            @date_default_timezone_set(self::$values['TIMEZONE']);
        } else {
            @date_default_timezone_set('America/Los_Angeles');
        }
    }
}
class Lib_Db {
    private $db;
    /**
     *
     * @var Lib_SettingFile
     */
    private $settings;

    public function __construct(Lib_SettingFile $settings) {
        $this->settings = $settings;
        $this->initDatabase();
        $this->query("SET NAMES utf8");
        $this->query("SET CHARACTER_SET utf8");
    }

    public function query($sql) {
        $res = @mysql_query($sql, $this->db);
        return $res;
    }

    public function getOneRow($sql) {
        $result = $this->query($sql);
        if (false === $result) {
            throw new Exception('Error getting one row. Error in query.');
        }
        if (@mysql_num_rows($result) != 1) {
            throw new Exception('Error getting one row. Got ' . mysql_num_rows($result) . ' rows.');
        }
        return @mysql_fetch_array($result);
    }

    public function escape($value) {
        return @mysql_real_escape_string($value, $this->db);
    }

    public function saveToDb(Lib_ConvertableToArray $params, $tableName) {
        $columns = '';
        $values = '';
        foreach ($params->toArray() as $key => $value) {
            $columns .= $key.', ';
            $values .= '\''. $this->escape($value).'\', ';
        }
        $insert = 'INSERT INTO '.$tableName.' ('.rtrim($columns, ' ,').')'.
                  ' VALUES ('.rtrim($values, ' ,').')';
        $this->query($insert);
    }

    protected function initDatabase() {
        if (function_exists('mysql_pconnect') && strstr(ini_get("disable_functions"), 'mysql_pconnect') === false) {
            $name = 'mysql_pconnect';
        } else {
            $name = 'mysql_connect';
        }
        $this->db = @call_user_func($name, $this->settings->get('DB_HOSTNAME'), $this->settings->get('DB_USERNAME'), $this->settings->get('DB_PASSWORD'));
         
        @mysql_select_db($this->settings->get('DB_DATABASE'), $this->db);
    }
}
?>
