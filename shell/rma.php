<?php
//ini_set("soap.wsdl_cache_enabled", "0");
//ini_set('soap.wsdl_cache_ttl', '0');

$opts = array(
	'http'=>array(
		'user_agent' => 'PHPSoapClient'
	),
	'ssl' => [
        // set some SSL/TLS specific options
        	'verify_peer' => false,
	        'verify_peer_name' => false,
        	'allow_self_signed' => true
    	]
);
//
$context = stream_context_create($opts);
try{
	//$c = new SoapClient('http://no-tomatoes.mirjana.seth.twm.eu/api/v2_soap?wsdl=1', array('stream_context' => $context, 'trace' => 1));
	$c = new SoapClient('https://www.jemappelle.nl/api/v2_soap?wsdl=1',  array('stream_context' => $context,'trace' => 1));
	$params = [
        'username' =>'bizbloqs',
        'apiKey' => '595bf013d860449d9b055e583df40aa5'
    ];
	//$s = $c->login($params);
	$s = $c->login('bizbloqs','595bf013d860449d9b055e583df40aa5');
	var_dump($s);
    //$sessionId = $s->result;
//    $rmaParams = [
//        'sessionId'=>$sessionId,
//        'orderIncrementId'=>'500174852'
//    ];
//    $l = $c->salesOrderReceiveRma($rmaParams);
    $l = $c->salesOrderReceiveRma($s,'500097978');
//    var_dump($l);

} catch (SoapFault $soapFault) {
	var_dump($soapFault);
	echo "Request :<br>", htmlentities($c->__getLastRequest()), "<br>";
	echo "Response :<br>", htmlentities($c->__getLastResponse()), "<br>";
	die();
}

//$l = $c->salesOrderList();
//var_dump($s);
try {
    $fs = $c->__getFunctions();
    var_dump(count($fs));
    foreach ($fs as $f){
        if (strpos($f, 'salesOrder') !==false)
            var_dump($f);
    }
   // $l = $c->salesOrderInfo($s,'500229060');



} catch (SoapFault $soapFault) {
	var_dump($soapFault);
	echo "Request :<br>", htmlentities($c->__getLastRequest()), "<br>";
	echo "Response :<br>", htmlentities($c->__getLastResponse()), "<br>";
}
//$l = $c->salesOrderList($s);
