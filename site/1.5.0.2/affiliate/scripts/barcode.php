<?php
require_once 'bootstrap.php';

$barCodeImage = new Gpf_BarCode_BarCode();
$barCodeImage->getImage($_REQUEST[Gpf_BarCode_BarCode::IMAGE_CODE]);
?>
