<?php
include 'fpdf/fpdf.php';

class cfdi_pdf extends FPDF{


    function init_data($data){
        $this->data = $data;
    }

    function Header(){
        $this->SetTitle($this->data['Serie']. $this->data['Folio'].'-'.$this->data['Receptor']['Rfc']);
        //datos de emisor 
        $this->SetFont('Arial','B',10);
        $this->SetTextColor(106,106,106);
        $y = $this->GetY();
        $this->Cell(50,5,$this->data["Emisor"]["Nombre"],0,0,'L');
        $this->Ln();
        $this->SetFont('Arial','',10);
        $this->Cell(10,5,'RFC ',0,0,'L');
        $this->SetFont('Arial','B',10);
        $this->Cell(40,5,$this->data["Emisor"]["Rfc"],0,0,'L');
        $this->Ln(7);
        $this->SetFont('Arial','',8);
        $this->Cell(10,5,'Regimen Fiscal ',0,0,'L');
        $this->Ln(4);
        $this->SetFont('Arial','B',8);
        $this->Cell(50,5,$this->data["Emisor"]["RegimenFiscal"].' - '.$this->convert_fiscal_reg($this->data["Emisor"]["RegimenFiscal"]),0,0,'L');
        $this->Ln(4);
        $this->SetFont('Arial','',8);
        $this->Cell(10,5,utf8_decode('Número de certificado'),0,0,'L');
        $this->Ln(4);
        $this->SetFont('Arial','B',8);
        $this->Cell(40,5,$this->data["NoCertificado"],0,0,'L');

        //datos de cfdi
        $this->SetXY(120,$y);
        $this->SetFont('Arial','B',10);
        $this->SetTextColor(106,106,106);
        $this->SetFillColor(230,230,230);
        $this->Cell(80,7,'CFDI de ingreso',0,0,'C',true);
        $this->Ln(8);
        $this->SetX(120);
        $this->SetFont('Arial','',8);
        $this->Cell(40,5,'Serie',0,0,'L');
        $this->Cell(40,5,'Folio',0,0,'L');
        $this->Ln();
        $this->SetX(120);
        $this->SetFont('Arial','B',10);
        $this->Cell(40,5,$this->data["Serie"],0,0,'L');
        $this->Cell(40,5,$this->data["Folio"],0,0,'L');
        $this->Ln();
        $this->SetX(120);
        $this->SetFont('Arial','',8);
        $this->Cell(40,5,'Lugar de emision',0,0,'L');
        $this->Cell(40,5,'Fecha y hora de emision',0,0,'L');
        $this->Ln();
        $this->SetX(120);
        $this->SetFont('Arial','B',10);
        $this->Cell(40,5,$this->data["LugarExpedicion"],0,0,'L');
        $this->Cell(40,5,$this->data["Complemento"]["TimbreFiscalDigital"]["FechaTimbrado"],0,0,'L');
        $this->Ln(15);

        //datos de receptor
        $this->SetFont('Arial','',8);
        $this->Cell(92.5,5,'Cliente',0,0,'L',true);
        $this->Cell(5);
        $this->Cell(92.5,5,'',0,0,'L',true);
        $this->Ln(6);
        $this->SetFont('Arial','B',10);
        $y = $this->GetY();
        $this->Cell(92.5,5,$this->data["Receptor"]{"Nombre"},0,0,'L');
        $this->Ln();
        $this->SetFont('Arial','',10);
        $this->Cell(8,5,'RFC',0,0,'L');
        $this->SetFont('Arial','B',10);
        $this->Cell(84.5,5,$this->data["Receptor"]["Rfc"],0,0,'L');
        $this->Ln();
        $this->SetXY(107.5, $y);
        $this->SetFont('Arial','B',10);
        $this->Cell(92.5,5,'Uso del CFDI',0,0,'L');
        $this->Ln();
        $this->SetX(107.5);
        $this->SetFont('Arial','',10);
        $this->Cell(8,5,$this->data["Receptor"]["UsoCFDI"].' - '.$this->convert_cfdi_use($this->data["Receptor"]["UsoCFDI"]),0,0,'L');

        $this->Ln(15);
    }

    function Footer(){
        //agregamos el timbre fiscal
        $this->SetFont('Arial','',8);
        $this->SetFillColor(230,230,230);
        $this->SetTextColor(106,106,106);
        $this->SetY(-53);
        $this->Line(10,$this->GetY(),200,$this->GetY());
        $this->Image("http://localhost/qr_code.php?uuid=".$this->data['Complemento']['TimbreFiscalDigital']['UUID']."&emisor=".$this->data['Emisor']['Rfc']."&receptor=".$this->data['Receptor']['Rfc']."&total=".$this->data['Total']."&sello=".substr($this->data['Sello'],-8),10,$this->GetY()+.5,30,0,'PNG');
        $this->SetXY(40,-52);
        $this->Cell(65,5,'Folio Fiscal',0,0,'L',true);
        $this->Cell(5);
        $this->Cell(42.5,5,'No. Certificado SAT',0,0,'L',true);
        $this->Cell(5);
        $this->Cell(42.5,5,'Fecha y hora de certificacion',0,0,'L',true);
        $this->Ln();
        $this->SetX(40);
        $this->SetFont('Arial','B',8);
        $this->Cell(65,5,$this->data["Complemento"]["TimbreFiscalDigital"]["UUID"],0,0,'L');
        $this->Cell(5);
        $this->SetFont('Arial','',8);
        $this->Cell(42.5,5,$this->data["NoCertificado"],0,0,'L');
        $this->Cell(5);
        $this->Cell(42.5,5,$this->data["Complemento"]["TimbreFiscalDigital"]["FechaTimbrado"],0,0,'L');
        $this->Ln();
        $this->SetX(40);
        $this->SetFont('Arial','',8);
        $this->Cell(65,5,'RFC proveedor de certificacion',0,0,'L',true);
        $this->Cell(5);
        $this->Cell(42.5,5,'Sello digital del SAT',0,0,'L',true);
        $this->Cell(5);
        $this->Cell(42.5,5,'Sello digital CFDI',0,0,'L',true);
        $this->Ln(6);
        $this->SetX(40);
        $this->Cell(65,5,$this->data["Complemento"]["TimbreFiscalDigital"]["RfcProvCertif"],0,0,'L');
        $this->Cell(5);
        $this->SetFont('Arial','',5);
        $y = $this->GetY();
        $x = $this->GetX();
        $this->MultiCell(42.5,2,$this->data["Complemento"]["TimbreFiscalDigital"]["SelloSAT"],0,'L');
        $this->SetXY($x+42.5,$y);
        $this->Cell(5);
        $this->SetFont('Arial','',5);
        $this->MultiCell(42.5,2,$this->data["Sello"],0,'L');
        $this->Ln(5);
        $this->SetXY(40,$y+5);
        $this->SetFont('Arial','',8);
        $this->Cell(65,5,'Cadena original del timbre',0,0,'L',true);




        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(15,10,'Desarrollado por CRS Software MX',0,0,'L');
        $this->Cell(0,10,utf8_decode('Este documento es una representación impresa de un CFDI versión 3.3'),0,0,'C');
        $this->SetX(-20);
        $this->Cell(10,10,'Pagina '.$this->PageNo().' de {nb}',0,0,'R');
    }

    function print_data($save_file = false){
        setlocale(LC_MONETARY,"en_US");
        $this->AliasNbPages();
        $this->AddPage();
        
        //tabla de conceptos
        $this->SetTextColor(106,106,106);
        $this->SetFont('Arial','B',8);
        $this->SetFillColor(230,230,230);
        $this->Cell(20,5,'Codigo',0,0,'L',true);
        $this->Cell(25,5,'Clave de unidad',0,0,'L',true);
        $this->Cell(65,5,'Descripcion',0,0,'L',true);
        $this->Cell(20,5,'Cantidad',0,0,'L',true);
        $this->Cell(20,5,'Valor Unitario',0,0,'L',true);
        $this->Cell(20,5,'Descuentos',0,0,'C',true);
        $this->Cell(20,5,'Importe',0,0,'C',true);
        $this->Ln();

        foreach($this->data["Conceptos"] as $row){
            $this->SetFont('Arial','',7);
            $this->SetTextColor(106,106,106);
            $this->Cell(28,5,$row["NoIdentificacion"],0,0,'L');
            $this->Cell(17,5,$row["ClaveUnidad"],0,0,'L');
            $x = $this->GetX();
            $y = $this->GetY();
            $this->MultiCell(65,5,$row["Descripcion"],0,'L');
            $y2 = $this->GetY();
            $this->SetX($x);
            $this->Cell(65,5,'Codigo SAT:'.$row["ClaveProdServ"],0,0,'L');
            $this->SetXY($x+65,$y);
            $this->SetFont('Arial','',8);
            $this->Cell(10,10,$row["Cantidad"],0,0,'R');
            $this->Cell(25,10,money_format('%.2n',$row["ValorUnitario"]),0,0,'R');
            $this->Cell(20,10,money_format('%.2n',$row["Descuento"]),0,0,'R');
            $this->Cell(25,10,money_format('%.2n',$row["Importe"]),0,0,'R');
            $this->Ln();
            $this->SetY($y2+5);
            $this->SetFont('Arial','B',7);
            foreach($row["Impuestos"] as $impuestos => $val){
                $this->Cell(13,5,$impuestos,0,0,'L');
                $this->SetFont('Arial','',7);
                $this->Cell(17,5,'Impuesto: '.$this->convert_taxes($val["Impuesto"]),0,0,'L');
                $this->Cell(22,5,'Tipo Factor: '.$val["TipoFactor"],0,0,'L');
                $this->Cell(25,5,'Tasa o Cuota: '.$val["TasaOCuota"].'%',0,0,'L');
                $this->Cell(25,5,'Base: '.money_format('%.2n',$val["Base"]),0,0,'L');
                $this->Cell(20,5,'Importe: '.money_format('%.2n',$val["Importe"]),0,0,'L');
            }
            $this->Ln();
            $this->SetLineWidth(0.2);
            $this->Line(10,$this->GetY(),200,$this->GetY());
            $this->Ln(1);
        }

        //totales
        $this->SetFont('Arial','',10);
        $this->SetX(155);
        $this->Cell(20,5,'Subtotal',0,0,'R');
        $this->Cell(25,5,money_format('%.2n',$this->data["SubTotal"]),0,0,'R');
        $this->Ln();
        $this->SetX(155);
        $this->Cell(20,5,'Descuento',0,0,'R');
        $this->Cell(25,5,money_format('%.2n',$this->data["Descuento"]),0,0,'R');
        $this->Ln();
        $this->SetX(155);
        foreach($this->data["Impuestos"] as $impuestos => $val){
            if(is_array($val)){
                $this->Cell(20,5,$this->convert_taxes($val["Impuesto"]).' '.$impuestos.' ('.($val["TasaOCuota"]*100).'%)',0,0,'R');
                $this->Cell(25,5,money_format('%.2n',$val["Importe"]),0,0,'R');
                $this->Ln();
            }
        }
        $this->Ln();
        //convertir importe a letras
        $importe_string = NumberFormatter::create('es_MX', NumberFormatter::SPELLOUT)->format($this->data["Total"]);
        $this->SetFont('Arial','B',8);
        $this->SetX(50);
        $this->Cell(40,5,'IMPORTE CON LETRAS:',0,0,'R');
        $this->SetFont('Arial','',8);
        $this->Cell(20,5,strtoupper($importe_string.' PESOS'),0,0,'L');
        $this->Ln(7);

        //total
        $this->SetFont('Arial','',12);
        $this->SetFillColor(230,230,230);
        $this->Cell(143,10,'Total',0,0,'R',true);
        $this->Cell(12,10,$this->data["Moneda"],0,0,'R',true);
        $this->SetFont('Arial','B',12);
        $this->Cell(35,10,money_format('%.2n',$this->data["Total"]),0,0,'R',true);

        //informacion adicional
        $this->Ln(15);
        $this->SetFont('Arial','B',8);
        $this->Cell(60,5,'Metodo de pago',0,0,'L');
        $this->Cell(10);
        $this->Cell(60,5,'Forma de pago',0,0,'L');
        $this->Cell(10);
        $this->Cell(60,5,'Tipo de cambio',0,0,'L');
        $this->Ln();
        $this->SetFont('Arial','',8);
        $this->Cell(60,5,$this->data["MetodoPago"].' - '.utf8_decode($this->convert_payment_method($this->data["MetodoPago"])),0,0,'L');
        $this->Cell(10);
        $this->Cell(60,5,$this->data["FormaPago"].' - '.utf8_decode($this->convert_payment_gateway($this->data["FormaPago"])),0,0,'L');
        $this->Cell(10);
        $this->Cell(60,5,$this->data["TipoCambio"],0,0,'L');
        $this->Ln(10);
        



        if($save_file==false){
            $this->Output('I',$this->data['Serie']. $this->data['Folio'].'-'.$this->data['Receptor']['Rfc'].'.pdf');
        }else{
            $this->Output('D',$this->data['Serie']. $this->data['Folio'].'-'.$this->data['Receptor']['Rfc'].'.pdf');
        }
    }
    
    function convert_taxes($id){
        switch ($id) {
            case '01':
                return 'ISR';
                break;
            case '002':
                return 'IVA';
                break;
            case '03':
                return 'IEPS';
                break;
            case '04':
                return 'IVA';
                break;
            default:
                return '';
                break;
        }
        
    }

    function convert_cfdi_use($id){
        switch ($id){
            case 'G01':
                return 'Adquisición de mercancias';
                break;
            case 'G02':
                return 'Devoluciones, descuentos o bonificaciones';
                break;
            case 'G03':
                return 'Gastos en general';
                break;
            case 'I01':
                return 'Construcciones';
                break;
            case 'I02':
                return 'Mobilario y equipo de oficina por inversiones';
                break;
            case 'I03':
                return 'Equipo de transporte';
                break;
            case 'I04':
                return 'Equipo de cómputo y accesorios';
                break;
            case 'I05':
                return 'Dados, troqueles, moldes, matrices y herramental';
                break;
            case 'I06':
                return 'Comunicaciones telefónicas';
                break;
            case 'I07':
                return 'Comunicaciones satelitales';
                break;
            case 'I08':
                return 'Otra maquinaria y equipo';
                break;
            case 'D01':
                return 'Honorarios médicos, dentales y gastos hospitalarios';
                break;
            case 'D02':
                return 'Gastos médicos por incapacidad o discapacidad';
                break;
            case 'D03':
                return 'Gastos funerales';
                break;
            case 'D04':
                return 'Donativos';
                break;
            case 'D05':
                return 'Intereses reales efectivamente pagados por créditos hipotecarios (casa habitación)';
                break;
            case 'D06':
                return 'Aportaciones voluntarias al SAR';
                break;
            case 'D07':
                return 'Primas por seguros de gastos médicos';
                break;
            case 'D08':
                return 'Gastos de transportación escolar obligatoria';
                break;
            case 'D09':
                return 'Depósitos en cuentas para el ahorro, primas que tengan como base planes de pensiones';
                break;
            case 'D10':
                return 'Pagos por servicios educativos (colegiaturas)';
                break;
            case 'P01':
                return 'Por definir';
                break;
        }
    }

    function convert_payment_method($id){
        switch ($id){
            case 'PUE':
                return 'Pago en una sola exhibición';
                break;
            case 'PPD':
                return 'Pago en parcialidades o diferido';
                break;
        }
    }

    function convert_payment_gateway($id){
        switch ($id){
            case '01':
                return 'Efectivo';
                break;
            case '02':
                return 'Cheque nominativo';
                break;
            case '03':
                return 'Transferencia electrónica de fondos';
                break;
            case '04':
                return 'Tarjeta de crédito';
                break;
            case '05':
                return 'Monedero electrónico';
                break;
            case '06':
                return 'Dinero electrónico';
                break;
            case '08':
                return 'Vales de despensa';
                break;
            case '12':
                return 'Dación en pago';
                break;
            case '13':
                return 'Pago por subrogación';
                break;
            case '14':
                return 'Pago por consignación';
                break;
            case '15':
                return 'Condonación';
                break;
            case '17':
                return 'Compensación';
                break;
            case '23':
                return 'Novación';
                break;
            case '24':
                return 'Confusión';
                break;
            case '25':
                return 'Remisión de deuda';
                break;
            case '26':
                return 'Prescripción o caducidad';
                break;
            case '27':
                return 'A satisfacción del acreedor';
                break;
            case '28':
                return 'Tarjeta de débito';
                break;
            case '29':
                return 'Tarjeta de servicios';
                break;
            case '30':
                return 'Aplicación de anticipos';
                break;
            case '31':
                return 'Intermediario pagos';
                break;
            case '99':
                return 'Por definir';
                break;
        }

    }

    function convert_fiscal_reg($id){
        switch ($id){
            case '601':
                return 'General de Ley Personas Morales';
                break;
            case '603':
                return 'Personas Morales con Fines no Lucrativos';
                break;
            case '605':
                return 'Sueldos y Salarios Personas Morales';
                break;
            case '606':
                return 'Arrendamiento';
                break;
            case '607':
                return 'Demás ingresos';
                break;
            case '608':
                return 'Consolidación';
                break;
            case '609':
                return 'Residentes en el Extranjero sin Establecimiento Permanente en México';
                break;
            case '610':
                return 'Ingresos por Dividendos (socios y accionistas)';
                break;
            case '611':
                return 'Personas Físicas con Actividades Empresariales y Profesionales';
                break;
            case '612':
                return 'Ingresos por intereses';
                break;
            case '614':
                return 'Sin obligaciones fiscales';
                break;
            case '616':
                return 'Sociedades Cooperativas de Producción que optan por diferir sus ingresos';
                break;
            case '620':
                return 'Actividades Agrícolas, Ganaderas, Silvícolas y Pesqueras';
                break;
            case '621':
                return 'Opcional para Grupos de Sociedades';
                break;
            case '622':
                return 'Coordinados';
                break;
            case '623':
                return 'Hidrocarburos';
                break;
            case '624':
                return 'Régimen de Enajenación o Adquisición de Bienes';
                break;
            case '628':
                return 'De los Regímenes Fiscales Preferentes y de las Empresas Multinacionales';
                break;
            case '629':
                return 'Enajenación de acciones en bolsa de valores';
                break;
            case '630':
                return 'Régimen de los ingresos por obtención de premios';
                break;
            case '631':
                return 'Régimen de los ingresos por realización de obras';
                break;
            case '632':
                return 'Régimen de ingresos por Arrendamiento';
                break;
            case '633':
                return 'Régimen de ingresos por Concesión';
                break;
            case '634':
                return 'Régimen de ingresos por operaciones de entretenimiento y recreativo';
                break;
            case '635':
                return 'Ingresos por Dividendos (socios y accionistas)';
                break;
            case '636':
                return 'Ingresos por intereses';
                break;
            case '637':
                return 'Régimen de ingresos por actividades de servicios';
                break;
            case '638':
                return 'Régimen de ingresos por productos y bienes';
                break;
            case '640':
                return 'Régimen de ingresos por obtención de ganados';
                break;
        }
    }

}

