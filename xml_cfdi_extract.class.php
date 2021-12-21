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
        $relacionados = $xml->xpath('//cfdi:Comprobante//cfdi:CfdiRelacionados');



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
            }
            

        }

        //cfdi relacionados
        if(count($relacionados)>0){
            $data['Relacionados']['TipoRelacion'] = (string)$relacionados[0]->attributes()->TipoRelacion;
            $relacionado = $relacionados[0]->xpath('cfdi:CfdiRelacionado');
            $data['Relacionados']['CfdiRelacionado'] = (string)$relacionado[0]->attributes()->UUID;
        }

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

        //complemento
        foreach($timbre[0]->attributes() as $key => $value){
            $data['Complemento']['TimbreFiscalDigital'][$key] = (string)$value;
        }
        

        return $data;

    }



}