<?php

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
	$c = new SoapClient('http://no-tomatoes.mirjana.seth.twm.eu/api/v2_soap?wsdl=1', array('stream_context' => $context, 'trace' => 1));
	//$c = new SoapClient('https://www.jemappelle.nl/api/v2_soap?wsdl=1', array('trace' => 1));
	$s = $c->login('webmen', '751f45817cfc4229a97a863c401dd6eb');

} catch (SoapFault $soapFault) {
	var_dump($soapFault);
	echo "Request :<br>", htmlentities($c->__getLastRequest()), "<br>";
	echo "Response :<br>", htmlentities($c->__getLastResponse()), "<br>";
	die();
}

//$l = $c->salesOrderList();
//var_dump($s);
try {
    //$fs = $c->__getFunctions();
  //  foreach ($fs as $f){
   //     var_dump($fs[150]);
   // }
   // $l = $c->salesOrderInfo($s,'500229060');
   // $l = $c->salesOrderReceiveRma($s,'500229057');
 //   var_dump($l);


} catch (SoapFault $soapFault) {
	var_dump($soapFault);
	echo "Request :<br>", htmlentities($c->__getLastRequest()), "<br>";
	echo "Response :<br>", htmlentities($c->__getLastResponse()), "<br>";
}
//$l = $c->salesOrderList($s);
