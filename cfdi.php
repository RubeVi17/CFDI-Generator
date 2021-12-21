<?php
include 'xml_to_cfdi_pdf.php';
include 'xml_cfdi_extract.class.php';

//mostramos lista de archivos xml de carpeta cfdi_xml
$action = $_GET["action"];
if($action == "massive_files"){
    $dir = 'cfdi_xml';
    $files = scandir($dir);
    //mostrar solo archivos xml
    $files = array_filter($files, function($file) {
        return strpos($file, '.xml') !== false;
    });
    
    foreach ($files as $file) {
        $xml_file = $dir . '/' . $file;
        $xml = new cfdi_extract($xml_file);
        $data = $xml->extract_xml_data();
    
        $pdf = new cfdi_pdf();
        $pdf->init_data($data);
        $pdf->print_data('F');
    }
}else{
    $id = $_GET["id"];
    if(!$id){
        $xml = new cfdi_extract('cfdi.xml');
        $data = $xml->extract_xml_data();
        
        $pdf = new cfdi_pdf();
        $pdf->init_data($data);
        $pdf->print_data('I');
    }else{

        //get json data from url
        $json = file_get_contents("https://satws.com/invoices/$id/cfdi");
        $data = json_decode($json, true);
        $pdf = new cfdi_pdf();
        $pdf->init_data($data);
        $pdf->print_data('I');

    }

}