<?php
include 'xml_to_cfdi_pdf.php';
include 'xml_cfdi_extract.class.php';
//upload file to server
if($_SERVER["REQUEST_METHOD"] == "POST"){
    $target_dir = "cfdi_xml/";
    $target_file = $target_dir . basename($_FILES["cfdi_xml"]["name"]);
    $uploadOk = 1;
    $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
    // Check if image file is a actual image or fake image
    if(isset($_POST["submit"])) {
        $check = getimagesize($_FILES["cfdi_xml"]["tmp_name"]);
        if($check !== false) {
            echo "File is an image - " . $check["mime"] . ".";
            $uploadOk = 1;
        } else {
            echo "File is not an image.";
            $uploadOk = 0;
        }
    }
    // Check file size
    if ($_FILES["cfdi_xml"]["size"] > 500000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }
    // Allow certain file formats
    if($imageFileType != "xml") {
        echo "Sorry, only XML files are allowed.";
        $uploadOk = 0;
    }
    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($_FILES["cfdi_xml"]["tmp_name"], $target_file)) {
            header('Content-Type: application/pdfdownload');
            $xml = new cfdi_extract($target_file);
            $data = $xml->extract_xml_data();
        
            $pdf = new cfdi_pdf();
            $pdf->init_data($data);
            $pdf->print_data('D');

        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Convert xml to pdf</title>
</head>
<body>
    <form action="" method="post" enctype="multipart/form-data">
        <input type="file" name="cfdi_xml" id="cfdi_xml">
        <input type="submit" value="Convert">
    </form>
</body>
</html>