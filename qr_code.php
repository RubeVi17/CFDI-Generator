<?php
include 'phpqrcode/qrlib.php';

$uuid = $_GET['uuid'];
$emisor = $_GET['emisor'];
$receptor = $_GET['receptor'];
$total = $_GET['total'];
$sello = $_GET['sello'];
QRCode::png("https://verificacfdi.facturaelectronica.sat.gob.mx/default.aspx?id=$uuid&re=$emisor&rr=$receptor&tt=$total&fe=$sello");

