<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class ApiInvoice extends Controller
{
    public function index(Request $request){
<<<<<<< HEAD
        try {
            $bodyResponseContent = json_decode(base64_decode($request->fileContent));
=======
    $bodyResponseContent = json_decode(base64_decode($request->fileContent));
>>>>>>> 731a64b5a8f3ced84c0b3d2751605c77bca948c7
    $typeDocument='';
        switch ($bodyResponseContent->factura->IDE->codTipoDocumento) {
            case '01':
                $typeDocument = 1;
            break;
            case '03':
                $typeDocument = 2;
            break;
            case '07':
                $typeDocument = 3;
            break;
            case '08':
                $typeDocument = 4;
            break;
        }
    $serie_Number=explode('-', $bodyResponseContent->factura->IDE->numeracion);
    $dateEmition = \Carbon\Carbon::parse($bodyResponseContent->factura->IDE->fechaEmision)->format('d-m-Y');
    $docType=(int)$bodyResponseContent->factura->EMI->tipoDocId;
    $docNumber=$bodyResponseContent->factura->EMI->numeroDocId;
    $customerName= $bodyResponseContent->factura->EMI->razonSocial;
    $customerAddress= $bodyResponseContent->factura->EMI->direccion;
    $gravada = $bodyResponseContent->factura->CAB->gravadas->totalVentas;
    $totalIgv =$bodyResponseContent->factura->CAB->totalImpuestos[0]->montoImpuesto;
    $total =$bodyResponseContent->factura->CAB->importeTotal;
    $items = array();
    foreach ($bodyResponseContent->factura->DET as $item) {
        $itemDescription = new \stdClass;
        $itemDescription->unidad_de_medida = $item->unidad;
        $itemDescription->codigo = "";
        $itemDescription->codigo_producto_sunat = $item->codProductoSunat;
        $itemDescription->descripcion = $item->descripcionProducto;
        $itemDescription->cantidad = $item->cantidadItems;
        $itemDescription->valor_unitario = $item->valorUnitario;
        $itemDescription->precio_unitario = $item->precioVentaUnitario;
        $itemDescription->descuento = "";
        $itemDescription->subtotal = $item->valorVenta;
        $itemDescription->tipo_de_igv = 1;
        $itemDescription->igv = $item->montoTotalImpuestos;
        $itemDescription->total = $item->precioVentaUnitario * $item->cantidadItems;
        $itemDescription->anticipo_regularizacion = false;
        $itemDescription->anticipo_documento_serie = "";
        $itemDescription->anticipo_documento_numero = "";
        $items[] =$itemDescription;
<<<<<<< HEAD


=======
<<<<<<< HEAD
=======

        
>>>>>>> 662304dc8113aa1bbc13bcb5185d64fa9ebba0fe
>>>>>>> 731a64b5a8f3ced84c0b3d2751605c77bca948c7
    }
    $arrayRequest = array(
        "operacion" => "generar_comprobante",
        "tipo_de_comprobante" => $typeDocument,
        "serie" => $serie_Number[0],
        "numero" => (int)$serie_Number[1],
        "sunat_transaction" => 1,
        "cliente_tipo_de_documento" => $docType,
        "cliente_numero_de_documento" => $docNumber,
        "cliente_denominacion" => $customerName,
        "cliente_direccion" => $customerAddress,
        "cliente_email" => "",
        "cliente_email_1" => "",
        "cliente_email_2" => "",
        // "fecha_de_emision" => $dateEmition,
        "fecha_de_emision" => "29/12/2023",
        "fecha_de_vencimiento" => "",
        "moneda" => 1,
        "tipo_de_cambio" => "",
        "porcentaje_de_igv"=> 18.00,
        "descuento_global"=> "",
        "total_descuento"=> "",
        "total_anticipo"=> "",
        "total_gravada"=> $gravada,
        "total_inafecta"=> "",
        "total_exonerada"=> "",
        "total_igv"=> $totalIgv,
        "total_gratuita"=> "",
        "total_otros_cargos"=> "",
        "total"=> $total,
        "percepcion_tipo"=> "",
        "percepcion_base_imponible"=> "",
        "total_percepcion"=> "",
        "total_incluido_percepcion"=> "",
        "retencion_tipo"=> "",
        "retencion_base_imponible"=> "",
        "total_retencion"=> "",
        "total_impuestos_bolsas"=> "",
        "detraccion"=> false,
        "observaciones"=> "",
        "documento_que_se_modifica_tipo"=> "",
        "documento_que_se_modifica_serie"=> "",
        "documento_que_se_modifica_numero"=> "",
        "tipo_de_nota_de_credito"=> "",
        "tipo_de_nota_de_debito"=> "",
        "enviar_automaticamente_a_la_sunat"=> true,
        "enviar_automaticamente_al_cliente"=> false,
        "condiciones_de_pago"=> "",
        "medio_de_pago"=> "",
        "placa_vehiculo"=> "",
        "orden_compra_servicio"=> "",  
        "formato_de_pdf"=> "",
        "generado_por_contingencia"=> "",
        "bienes_region_selva"=> "",
        "servicios_region_selva"=> "",
        "items" =>$items
    );
    $url = NUBEFACT_URL;
    $token = NUBEFACT_TOKEN;
    $responseParsed = $arrayRequest;
    $response = \Http::withToken($token)->post($url, $responseParsed);
    if ($response->ok()){
        $responseJson = json_decode($response);
        $result_response = array(
            "responseCode" => "0",
            "responseContent" => "Operacion exitosa",
            "pseRequests" => []
        );
    } else {
        $responseJson = json_decode($response);
        $result_response = array(
            "responseCode" => $responseJson->codigo,
            "responseContent" => $responseJson->errors,
            "pseRequests" => []
        );
    }
<<<<<<< HEAD
        } catch (\Throwable $th) {
            \Log::error($th);
            return response()->json(['success' => false, 'message' => 'Estructura JSON inválida, consule el log'],500);
            }
    return response()->json($result_response, 200);
    }
    public function CancelInvoice(Request $request){
        try {
            $bodyResponseContent = json_decode(base64_decode($request->fileContent));
            $serie_Number=explode('-', $bodyResponseContent->resumenComprobantes->DET[0]->numeracionItem);
            $typeDocument='';
            switch ($bodyResponseContent->resumenComprobantes->DET[0]->tipoComprobanteItem) {
                case '01':
                    $typeDocument = 1;
                break;
                case '03':
                    $typeDocument = 2;
                break;
                case '07':
                    $typeDocument = 3;
                break;
                case '08':
                    $typeDocument = 4;
                break;
            }
        $serie_Number=explode('-', $bodyResponseContent->resumenComprobantes->DET[0]->numeracionItem);
            $arrayRequest = array(
                "operacion" => "generar_anulacion",
                "tipo_de_comprobante" => $typeDocument,
                "serie" => $serie_Number[0],
                "numero" => $serie_Number[1],
                "motivo"=> "ANULACION GENERICA",
                "codigo_unico"=> "" 
            );          
            $url = NUBEFACT_URL;
            $token = NUBEFACT_TOKEN;
            $responseParsed = $arrayRequest;
            $response = \Http::withToken($token)->post($url, $responseParsed );
            if ($response->ok()){
                $responseJson = json_decode($response);
                $result_response = array(
                    "responseCode" => "0",
                    "responseContent" => "Operacion exitosa",
                    "pseRequests" => []
                );
            } else {
                $responseJson = json_decode($response);
                $result_response = array(
                    "responseCode" => $responseJson->codigo,
                    "responseContent" => $responseJson->errors,
                    "pseRequests" => []
                );
            }
        } catch (\Throwable $th) {
        \Log::error($th);
          return response()->json(['success' => false, 'message' => 'Estructura JSON inválida, consule el log'],500);
        }
        return response()->json($result_response, 200);
    }
    // public function QueryInvoice(Request $Request){
    //     $arrayRequest = array(
    //         "operacion" => "consultar_comprobante",
    //         "tipo_de_comprobante" => 1,
    //         "serie" => "F050",
    //         "numero" => 1,
    //     );    
    //     $url = NUBEFACT_URL;
    //     $token = NUBEFACT_TOKEN;
    //     $responseParsed = $arrayRequest;
    //     $response = \Http::withToken($token)->post($url, $responseParsed );
    //     if ($response->ok()){
    //         $responseJson = json_decode($response);
    //     } else {
    //         $responseJson = json_decode($response);
    //     }
    //     return response()->json(['respuesta' => $responseJson], 200);
    // }
    public function QueryInvoiceXML(Request $Request){

    }
    public function QueryInvoiceQR(Request $Request){

=======
    return response()->json($result_response, 200);
    }
    public function QueryInvoice(Request $Request){
        $arrayRequest = array(
            "operacion" => "consultar_comprobante",
            "tipo_de_comprobante" => 1,
            "serie" => "F050",
            "numero" => 1,
        );    
        $url = NUBEFACT_URL;
        $token = NUBEFACT_TOKEN;
        $responseParsed = $arrayRequest;
        $response = \Http::withToken($token)->post($url, $responseParsed );
        if ($response->ok()){
            $responseJson = json_decode($response);
        } else {
            $responseJson = json_decode($response);
        }
        return response()->json(['respuesta' => $responseJson], 200);
    }
    public function CancelInvoice(Request $Request){
        $arrayRequest = array(
            "operacion" => "generar_anulacion",
            "tipo_de_comprobante" => 1,
            "serie" => "F050",
            "numero" => 1,
            "motivo"=> "ERROR DEL SISTEMA",
            "codigo_unico"=> "" 
        );          
        $url = NUBEFACT_URL;
        $token = NUBEFACT_TOKEN;
        $responseParsed = $arrayRequest;
        $response = \Http::withToken($token)->post($url, $responseParsed );
        if ($response->ok()){
            $responseJson = json_decode($response);
        } else {
            $responseJson = json_decode($response);
        }
        return response()->json(['respuesta' => $responseJson], 200);
    }
    public function QueryInvoiceCanceled(Request $Request){
        $arrayRequest = array(
            "operacion" => "consultar_anulacion",
            "tipo_de_comprobante" => 1,
            "serie" => "F050",
            "numero" => 1,
        );    
        $url = NUBEFACT_URL;
        $token = NUBEFACT_TOKEN;
        $responseParsed = $arrayRequest;
        $response = \Http::withToken($token)->post($url, $responseParsed );
        if ($response->ok()){
            $responseJson = json_decode($response);
        } else {
            $responseJson = json_decode($response);
        }
        return response()->json(['respuesta' => $responseJson], 200);
>>>>>>> 731a64b5a8f3ced84c0b3d2751605c77bca948c7
    }
}
