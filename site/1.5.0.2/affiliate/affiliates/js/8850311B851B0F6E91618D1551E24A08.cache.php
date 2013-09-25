<?php
if(isset($_SERVER["HTTP_CACHE_CONTROL"]) && $_SERVER["HTTP_CACHE_CONTROL"] == "max-age=0") {
    header("HTTP/1.1 304 Not Modified");
    die;
}

@ini_set('zlib.output_compression', "0");
if(ini_get('zlib.output_compression') > 0) {
    $name = substr($_SERVER['SCRIPT_FILENAME'], 0, -3) . 'html';
} else {
    header("Content-Encoding: gzip");
    $name = substr($_SERVER['SCRIPT_FILENAME'], 0, -3) . 'html.gz';
}
$offset = 31536000; 
header("Cache-Control: max-age=$offset, public");
$now = getdate(time());
header("Expires: " . gmdate("D, d M Y H:i:s", mktime(0, 0, 0, 1, 1, $now['year']+1)) . " GMT");      
header("Connection: Keep-Alive");
header("Last-Modified: " . gmdate("D, d M Y H:i:s", mktime(0, 0, 0, 1, 1, 2008)) . " GMT");
header("Content-Type: text/html");
header("Content-Length: " . filesize($name));
if (@readfile($name) == null) {
  if (strstr(ini_get("disable_functions"), 'fpassthru')) {
    echo file_get_contents($name);
  } else {
    $fp = fopen($name, 'r');
    fpassthru($fp);
    fclose($fp);
  }
}
?>