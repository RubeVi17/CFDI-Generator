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

        $comprobante = $xml->xpath('//cfdi:Comprobante');
        $emisor = $xml->xpath('//cfdi:Comprobante//cfdi:Emisor');
        $receptor = $xml->xpath('//cfdi:Comprobante//cfdi:Receptor');
        $concepto = $xml->xpath('//cfdi:Comprobante//cfdi:Conceptos//cfdi:Concepto');
        $impuestos_totales = $xml->xpath('//cfdi:Comprobante//cfdi:Impuestos');
        $complemento = $xml->xpath('//cfdi:Comprobante//cfdi:Complemento');
        $timbre = $xml->xpath('//cfdi:Comprobante//t:TimbreFiscalDigital');



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
            $data['Conceptos'][$i]['Cantidad'] = (string)$concepto[$i]->attributes()->Cantidad;
            $data['Conceptos'][$i]['ClaveUnidad'] = (string)$concepto[$i]->attributes()->ClaveUnidad;
            $data['Conceptos'][$i]['NoIdentificacion'] = (string)$concepto[$i]->attributes()->NoIdentificacion;
            $data['Conceptos'][$i]['Descripcion'] = (string)$concepto[$i]->attributes()->Descripcion;
            $data['Conceptos'][$i]['ValorUnitario'] = (string)$concepto[$i]->attributes()->ValorUnitario;
            $data['Conceptos'][$i]['Importe'] = (string)$concepto[$i]->attributes()->Importe;
            $data['Conceptos'][$i]['ClaveProdServ'] = (string)$concepto[$i]->attributes()->ClaveProdServ;

            //obtener traslados
            if(count($impuestos)>0){
                $traslados = $impuestos[0]->xpath('cfdi:Traslados//cfdi:Traslado');
                for($j=0; $j<count($traslados); $j++){
                    $data['Conceptos'][$i]['Impuestos']['Traslados']['Base'] = (string)$traslados[$j]->attributes()->Base;
                    $data['Conceptos'][$i]['Impuestos']['Traslados']['Impuesto'] = (string)$traslados[$j]->attributes()->Impuesto;
                    $data['Conceptos'][$i]['Impuestos']['Traslados']['TipoFactor'] = (string)$traslados[$j]->attributes()->TipoFactor;
                    $data['Conceptos'][$i]['Impuestos']['Traslados']['TasaOCuota'] = (string)substr($traslados[$j]->attributes()->TasaOCuota, 0, -4);
                    $data['Conceptos'][$i]['Impuestos']['Traslados']['Importe'] = (string)$traslados[$j]->attributes()->Importe;
                }
            }
            

        }

        //impuestos totales del ultimo impusto
        foreach(end($impuestos_totales)->attributes() as $key => $value){
            $data['Impuestos'][$key] = (string)$value;
        }
        //impuestos totales del ultimo impuesto (traslados)
        $traslados = end($impuestos_totales)->xpath('cfdi:Traslados//cfdi:Traslado');
        for($i=0; $i<count($traslados); $i++){
            $data['Impuestos']['Traslados']['Impuesto'] = (string)$traslados[$i]->attributes()->Impuesto;
            $data['Impuestos']['Traslados']['TipoFactor'] = (string)$traslados[$i]->attributes()->TipoFactor;
            $data['Impuestos']['Traslados']['TasaOCuota'] = (string)substr($traslados[$i]->attributes()->TasaOCuota, 0, -4);
            $data['Impuestos']['Traslados']['Importe'] = (string)$traslados[$i]->attributes()->Importe;
        }

        //complemento
        foreach($timbre[0]->attributes() as $key => $value){
            $data['Complemento']['TimbreFiscalDigital'][$key] = (string)$value;
        }
        

        return $data;

    }



}