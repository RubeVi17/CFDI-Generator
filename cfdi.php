<?php
include 'xml_to_cfdi_pdf.php';
include 'xml_cfdi_extract.class.php';

$xml = new cfdi_extract('cfdi.xml');
$data = $xml->extract_xml_data();

$pdf = new cfdi_pdf();
$pdf->init_data($data);
$pdf->print_data();