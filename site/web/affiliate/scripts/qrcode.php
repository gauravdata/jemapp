<?php
require_once 'bootstrap.php';

$qrCodeImage = new Gpf_QrCode_QrCode();
$qrCodeImage->getImage($_REQUEST[Gpf_QrCode_QrCode::QR_IMAGE_CODE]);
?>
