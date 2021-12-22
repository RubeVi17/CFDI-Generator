<?php

class cfdi_extract{

    function __construct($xml_file){
        $this->xml_file = $xml_file;
    }

    function extract_xml_data(){

        //convertir xml a json incluyendo namespace
        $xml = simplexml_load_file($this->xml_file);
        $data = array();
        $namesapce = $xml->getNamespaces(true);
        $xml->registerXPathNamespace('t', $namesapce['tfd']);
        $xml->registerXPathNamespace('pago10', $namesapce['pago10']);

        $comprobante = $xml->xpath('//cfdi:Comprobante');
        $emisor = $xml->xpath('//cfdi:Comprobante//cfdi:Emisor');
        $receptor = $xml->xpath('//cfdi:Comprobante//cfdi:Receptor');
        $concepto = $xml->xpath('//cfdi:Comprobante//cfdi:Conceptos//cfdi:Concepto');
        $impuestos_totales = $xml->xpath('//cfdi:Comprobante//cfdi:Impuestos');
        $complemento = $xml->xpath('//cfdi:Comprobante//cfdi:Complemento');
        $timbre = $xml->xpath('//cfdi:Comprobante//t:TimbreFiscalDigital');
        $relacionados = $xml->xpath('//cfdi:Comprobante//cfdi:CfdiRelacionados');
        $pagos = $xml->xpath('//cfdi:Comprobante//pago10:Pagos');



        //comprobante
        foreach ($comprobante[0]->attributes() as $key => $value) {
            $data[$key] = (string)$value;
        }
        //emisor
        foreach ($emisor[0]->attributes() as $key => $value) {
            $data['Emisor'][$key] = (string)$value;
        }
        //receptor
        foreach ($receptor[0]->attributes() as $key => $value) {
            $data['Receptor'][$key] = (string)$value;
        }
        //conceptos
        for($i=0; $i<count($concepto); $i++){
            $impuestos = $concepto[$i]->xpath('cfdi:Impuestos');
            $data['Conceptos']['Concepto'][$i]['Cantidad'] = (string)$concepto[$i]->attributes()->Cantidad;
            $data['Conceptos']['Concepto'][$i]['ClaveUnidad'] = (string)$concepto[$i]->attributes()->ClaveUnidad;
            $data['Conceptos']['Concepto'][$i]['NoIdentificacion'] = (string)$concepto[$i]->attributes()->NoIdentificacion;
            $data['Conceptos']['Concepto'][$i]['Descripcion'] = (string)$concepto[$i]->attributes()->Descripcion;
            $data['Conceptos']['Concepto'][$i]['ValorUnitario'] = (string)$concepto[$i]->attributes()->ValorUnitario;
            $data['Conceptos']['Concepto'][$i]['Importe'] = (string)$concepto[$i]->attributes()->Importe;
            $data['Conceptos']['Concepto'][$i]['ClaveProdServ'] = (string)$concepto[$i]->attributes()->ClaveProdServ;

            //obtener traslados
            if(count($impuestos)>0){
                $traslados = $impuestos[0]->xpath('cfdi:Traslados//cfdi:Traslado');
                for($j=0; $j<count($traslados); $j++){
                    $data['Conceptos']['Concepto'][$i]['Impuestos']['Traslados']['Traslado']['Base'] = (string)$traslados[$j]->attributes()->Base;
                    $data['Conceptos']['Concepto'][$i]['Impuestos']['Traslados']['Traslado']['Impuesto'] = (string)$traslados[$j]->attributes()->Impuesto;
                    $data['Conceptos']['Concepto'][$i]['Impuestos']['Traslados']['Traslado']['TipoFactor'] = (string)$traslados[$j]->attributes()->TipoFactor;
                    $data['Conceptos']['Concepto'][$i]['Impuestos']['Traslados']['Traslado']['TasaOCuota'] = (string)substr($traslados[$j]->attributes()->TasaOCuota, 0, -4);
                    $data['Conceptos']['Concepto'][$i]['Impuestos']['Traslados']['Traslado']['Importe'] = (string)$traslados[$j]->attributes()->Importe;
                }

                //obtener retenciones
                $retenciones = $impuestos[0]->xpath('cfdi:Retenciones//cfdi:Retencion');
                if($retenciones){

                    if(count($retenciones) > 1){
                        for($j=0; $j<count($retenciones); $j++){
                            $data['Conceptos']['Concepto'][$i]['Impuestos']['Retenciones']['Retencion'][$j]['Base'] = (string)$retenciones[$j]->attributes()->Base;
                            $data['Conceptos']['Concepto'][$i]['Impuestos']['Retenciones']['Retencion'][$j]['Impuesto'] = (string)$retenciones[$j]->attributes()->Impuesto;
                            $data['Conceptos']['Concepto'][$i]['Impuestos']['Retenciones']['Retencion'][$j]['TipoFactor'] = (string)$retenciones[$j]->attributes()->TipoFactor;
                            $data['Conceptos']['Concepto'][$i]['Impuestos']['Retenciones']['Retencion'][$j]['TasaOCuota'] = (string)substr($retenciones[$j]->attributes()->TasaOCuota, 0, -2);
                            $data['Conceptos']['Concepto'][$i]['Impuestos']['Retenciones']['Retencion'][$j]['Importe'] = (string)$retenciones[$j]->attributes()->Importe;
                        }
                    }else{
                        $data['Conceptos']['Concepto'][$i]['Impuestos']['Retenciones']['Retencion']['Base'] = (string)$retenciones[0]->attributes()->Base;
                        $data['Conceptos']['Concepto'][$i]['Impuestos']['Retenciones']['Retencion']['Impuesto'] = (string)$retenciones[0]->attributes()->Impuesto;
                        $data['Conceptos']['Concepto'][$i]['Impuestos']['Retenciones']['Retencion']['TipoFactor'] = (string)$retenciones[0]->attributes()->TipoFactor;
                        $data['Conceptos']['Concepto'][$i]['Impuestos']['Retenciones']['Retencion']['TasaOCuota'] = (string)substr($retenciones[0]->attributes()->TasaOCuota, 0, -2);
                        $data['Conceptos']['Concepto'][$i]['Impuestos']['Retenciones']['Retencion']['Importe'] = (string)$retenciones[0]->attributes()->Importe;
                    }

                }
                
            }
            

        }

        //cfdi relacionados
        if(count($relacionados)>0){
            $data['Relacionados']['TipoRelacion'] = (string)$relacionados[0]->attributes()->TipoRelacion;
            $relacionado = $relacionados[0]->xpath('cfdi:CfdiRelacionado');
            $data['Relacionados']['CfdiRelacionado'] = (string)$relacionado[0]->attributes()->UUID;
        }

        if($comprobante[0]->attributes()->TipoDeComprobante != 'P'){

            //impuestos totales del ultimo impusto
            foreach(end($impuestos_totales)->attributes() as $key => $value){
                $data['Impuestos'][$key] = (string)$value;
            }
            //impuestos totales del ultimo impuesto (traslados)
            $traslados = end($impuestos_totales)->xpath('cfdi:Traslados//cfdi:Traslado');
            for($i=0; $i<count($traslados); $i++){
                $data['Impuestos']['Traslados']['Traslado']['Impuesto'] = (string)$traslados[$i]->attributes()->Impuesto;
                $data['Impuestos']['Traslados']['Traslado']['TipoFactor'] = (string)$traslados[$i]->attributes()->TipoFactor;
                $data['Impuestos']['Traslados']['Traslado']['TasaOCuota'] = (string)substr($traslados[$i]->attributes()->TasaOCuota, 0, -4);
                $data['Impuestos']['Traslados']['Traslado']['Importe'] = (string)$traslados[$i]->attributes()->Importe;
            }

            //impuestos totales del ultimo impuesto (retenciones)
            $retenciones = end($impuestos_totales)->xpath('cfdi:Retenciones//cfdi:Retencion');
            if($retenciones){
                
                if(count($retenciones) > 1){
                    for($i=0; $i<count($retenciones); $i++){
                        $data['Impuestos']['Retenciones']['Retencion'][$i]['Impuesto'] = (string)$retenciones[$i]->attributes()->Impuesto;
                        $data['Impuestos']['Retenciones']['Retencion'][$i]['Importe'] = (string)$retenciones[$i]->attributes()->Importe;
                    }
                }else{
                    $data['Impuestos']['Retenciones']['Retencion']['Impuesto'] = (string)$retenciones[0]->attributes()->Impuesto;
                    $data['Impuestos']['Retenciones']['Retencion']['Importe'] = (string)$retenciones[0]->attributes()->Importe;
                }

            }
        }

        //pagos en caso de ser complemento de pago
        if($pagos){
            $data["Complemento"]['Pagos']['Version'] = (string)$pagos[0]->attributes()->Version;
            $pago = $pagos[0]->xpath('pago10:Pago');
            foreach($pago[0]->attributes() as $key => $value){
                $data["Complemento"]['Pagos']['Pago'][$key] = (string)$value;
                $doctoRelacionado = $pago[0]->xpath('pago10:DoctoRelacionado');
                for($i=0; $i<count($doctoRelacionado); $i++){
                    
                    $data["Complemento"]['Pagos']['Pago']['DoctoRelacionado'][$i]['IdDocumento'] = (string)$doctoRelacionado[$i]->attributes()->IdDocumento;
                    $data["Complemento"]['Pagos']['Pago']['DoctoRelacionado'][$i]['Folio'] = (string)$doctoRelacionado[$i]->attributes()->Folio;
                    $data["Complemento"]['Pagos']['Pago']['DoctoRelacionado'][$i]['Serie'] = (string)$doctoRelacionado[$i]->attributes()->Serie;
                    $data["Complemento"]['Pagos']['Pago']['DoctoRelacionado'][$i]['MonedaDR'] = (string)$doctoRelacionado[$i]->attributes()->MonedaDR;
                    $data["Complemento"]['Pagos']['Pago']['DoctoRelacionado'][$i]['MetodoDePagoDR'] = (string)$doctoRelacionado[$i]->attributes()->MetodoDePagoDR;
                    $data["Complemento"]['Pagos']['Pago']['DoctoRelacionado'][$i]['NumParcialidad'] = (string)$doctoRelacionado[$i]->attributes()->NumParcialidad;
                    $data["Complemento"]['Pagos']['Pago']['DoctoRelacionado'][$i]['ImpPagado'] = (string)$doctoRelacionado[$i]->attributes()->ImpPagado;
                    $data["Complemento"]['Pagos']['Pago']['DoctoRelacionado'][$i]['ImpSaldoAnt'] = (string)$doctoRelacionado[$i]->attributes()->ImpSaldoAnt;
                    $data["Complemento"]['Pagos']['Pago']['DoctoRelacionado'][$i]['ImpSaldoInsoluto'] = (string)$doctoRelacionado[$i]->attributes()->ImpSaldoInsoluto;
                }
            }
            
        }


        //complemento
        foreach($timbre[0]->attributes() as $key => $value){
            $data['Complemento']['TimbreFiscalDigital'][$key] = (string)$value;
        }
        

        return $data;

    }



}