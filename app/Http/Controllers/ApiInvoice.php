<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ApiInvoice extends Controller
{
    public function index(Request $request){
        try {
            $bodyResponseContent = json_decode(base64_decode($request->fileContent));
            $typeNoteCredit = "";
            $typeNoteDebit = "";
            $docModifyId = "";
            $docModifySerie = "";
            $docModifyNumber = "";
            $ruc = null;
            /** EMISION DE FACTURA */
           if (isset($bodyResponseContent->factura)){
            $requestCodTypeDocument = "01";
            $requestSerie = $bodyResponseContent->factura->IDE->numeracion ?? "";
            $requestDateEmition = $bodyResponseContent->factura->IDE->fechaEmision ?? "";
            $requestDocType = $bodyResponseContent->factura->REC->tipoDocId ?? "";
            $requestDocNumber = $bodyResponseContent->factura->REC->numeroDocId ?? "";
            $requestCustomerName = $bodyResponseContent->factura->REC->razonSocial ?? "";
            $requestCustomerAddress= $bodyResponseContent->factura->REC->direccion ?? "";
            $requestGravada = $bodyResponseContent->factura->CAB->gravadas->totalVentas ?? "";
            $requestTotalIgv =$bodyResponseContent->factura->CAB->totalImpuestos[0]->montoImpuesto ?? "";
            $requestTotal =$bodyResponseContent->factura->CAB->importeTotal ?? "";
            $requestItemsCount = $bodyResponseContent->factura->DET ?? "";
            $ruc = $bodyResponseContent->factura->EMI->numeroDocId ?? null;
           }
           /** EMISION DE BOLETA */
           if (isset($bodyResponseContent->boleta)){
            $requestCodTypeDocument = "03";
            $requestSerie = $bodyResponseContent->boleta->IDE->numeracion ?? "";
            $requestDateEmition = $bodyResponseContent->boleta->IDE->fechaEmision ?? "";
            $requestDocType = $bodyResponseContent->boleta->REC->tipoDocId ?? "";
            $requestDocNumber = $bodyResponseContent->boleta->REC->numeroDocId ?? "";
            $requestCustomerName = $bodyResponseContent->boleta->REC->razonSocial ?? "";
            $requestCustomerAddress= $bodyResponseContent->boleta->REC->direccion ?? "";
            $requestGravada = $bodyResponseContent->boleta->CAB->gravadas->totalVentas ?? "";
            $requestTotalIgv =$bodyResponseContent->boleta->CAB->totalImpuestos[0]->montoImpuesto ?? "";
            $requestTotal =$bodyResponseContent->boleta->CAB->importeTotal ?? "";
            $requestItemsCount = $bodyResponseContent->boleta->DET ?? "";
            $ruc = $bodyResponseContent->boleta->EMI->numeroDocId ?? null;
           }
           /** EMISION DE NOTA DE CREDITO */
           if (isset($bodyResponseContent->notaCredito)){
            $requestCodTypeDocument = "07";
            $requestSerie = $bodyResponseContent->notaCredito->IDE->numeracion ?? "";
            $requestDateEmition = $bodyResponseContent->notaCredito->IDE->fechaEmision ?? "";
            $requestDocType = $bodyResponseContent->notaCredito->REC->tipoDocId ?? "";
            $requestDocNumber = $bodyResponseContent->notaCredito->REC->numeroDocId ?? "";
            $requestCustomerName = $bodyResponseContent->notaCredito->REC->razonSocial ?? "";
            $requestCustomerAddress= $bodyResponseContent->notaCredito->REC->direccion ?? "";
            $requestGravada = $bodyResponseContent->notaCredito->CAB->gravadas->totalVentas ?? "";
            $requestTotalIgv =$bodyResponseContent->notaCredito->CAB->totalImpuestos[0]->montoImpuesto ?? "";
            $requestTotal =$bodyResponseContent->notaCredito->CAB->importeTotal ?? "";
            $requestItemsCount = $bodyResponseContent->notaCredito->DET ?? "";
            $typeNoteCredit = (int)$bodyResponseContent->notaCredito->DRF[0]->codigoMotivo ?? "";
            $ruc = $bodyResponseContent->notaCredito->EMI->numeroDocId ?? null;
            if (isset($bodyResponseContent->notaCredito->DRF[0]->tipoDocRelacionado)){
                switch ($bodyResponseContent->notaCredito->DRF[0]->tipoDocRelacionado) {
                case '01':
                    $docModifyId = 1;
                break;
                case '03':
                    $docModifyId = 2;
                break;
                }
                if (isset($bodyResponseContent->notaCredito->DRF[0]->numeroDocRelacionado)){
                    $docModifyExplode = explode('-', $bodyResponseContent->notaCredito->DRF[0]->numeroDocRelacionado);
                    $docModifySerie = $docModifyExplode[0];
                    $docModifyNumber = (int)$docModifyExplode[1];
                }
            }
           } 
            /** EMISION DE NOTA DE DEBITO */
            if (isset($bodyResponseContent->notaDebito)){
                $requestCodTypeDocument = "08";
                $requestSerie = $bodyResponseContent->notaDebito->IDE->numeracion ?? "";
                $requestDateEmition = $bodyResponseContent->notaDebito->IDE->fechaEmision ?? "";
                $requestDocType = $bodyResponseContent->notaDebito->REC->tipoDocId ?? "";
                $requestDocNumber = $bodyResponseContent->notaDebito->REC->numeroDocId ?? "";
                $requestCustomerName = $bodyResponseContent->notaDebito->REC->razonSocial ?? "";
                $requestCustomerAddress= $bodyResponseContent->notaDebito->REC->direccion ?? "";
                $requestGravada = $bodyResponseContent->notaDebito->CAB->gravadas->totalVentas ?? "";
                $requestTotalIgv =$bodyResponseContent->notaDebito->CAB->totalImpuestos[0]->montoImpuesto ?? "";
                $requestTotal =$bodyResponseContent->notaDebito->CAB->importeTotal ?? "";
                $requestItemsCount = $bodyResponseContent->notaDebito->DET ?? "";
                $typeNoteDebit = (int)$bodyResponseContent->notaDebito->DRF[0]->codigoMotivo ?? "";
                $ruc = $bodyResponseContent->notaDebito->EMI->numeroDocId ?? null;
                if (isset($bodyResponseContent->notaDebito->DRF[0]->tipoDocRelacionado)){
                    switch ($bodyResponseContent->notaDebito->DRF[0]->tipoDocRelacionado) {
                    case '01':
                        $docModifyId = 1;
                    break;
                    case '03':
                        $docModifyId = 2;
                    break;
                    }
                    if (isset($bodyResponseContent->notaDebito->DRF[0]->numeroDocRelacionado)){
                        $docModifyExplode = explode('-', $bodyResponseContent->notaDebito->DRF[0]->numeroDocRelacionado);
                        $docModifySerie = $docModifyExplode[0];
                        $docModifyNumber = (int)$docModifyExplode[1];
                    }
                }
            } 
    $typeDocument='';
        switch ($requestCodTypeDocument) {
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
    $serie_Number=explode('-', $requestSerie);
    $dateEmition = \Carbon\Carbon::parse($requestDateEmition)->format('d-m-Y');
    $docType=(int)$requestDocType;
    $docNumber=$requestDocNumber;
    $customerName= $requestCustomerName;
    $customerAddress= $requestCustomerAddress;
    $gravada = $requestGravada;
    $totalIgv =$requestTotalIgv;
    $total =$requestTotal;
    $items = array();
    foreach ($requestItemsCount as $item) {
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
        "fecha_de_emision" => $dateEmition,
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
        "documento_que_se_modifica_tipo"=>  $docModifyId,
        "documento_que_se_modifica_serie"=> $docModifySerie,
        "documento_que_se_modifica_numero"=> $docModifyNumber,
        "tipo_de_nota_de_credito"=> $typeNoteCredit,
        "tipo_de_nota_de_debito"=> $typeNoteDebit,
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
    if (is_null($ruc)){
        $ruc = "20601285101";
    }
    $url = NUBEFACT_ARRAY_URL[$ruc] ?? NUBEFACT_URL;
    $token = NUBEFACT_ARRAY_TOKEN[$ruc] ?? NUBEFACT_TOKEN;
    $responseParsed = $arrayRequest;
    $response = \Http::withToken($token)->post($url, $responseParsed);
    if ($response->ok()){
        $responseJson = json_decode($response);
        $responseTypeDocument="";
        $responseCode="";
        $responseNumber="";
        $result_response = array(
            "responseCode" => "0",
            "responseContent" => $responseJson->sunat_description,
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
            $result_response = array(
                "responseCode" => 99,
                "responseContent" => "Estructura JSON inválida, consule el log",
                "pseRequests" => []
            );
            return response()->json($result_response,200);
            }
    return response()->json($result_response, 200);
    }
    public function CancelInvoice(Request $request){
        try {
            $bodyResponseContent = json_decode(base64_decode($request->fileContent));
            $typeDocument='';
            $ruc = null;
            /** ANULAR FACTURA, NOTA DE CREDITO, NOTA DE DEBITO */
            if (isset($bodyResponseContent->comunicacionBaja)){
                switch ($bodyResponseContent->comunicacionBaja->DBR[0]->tipoComprobanteItem) {
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
            $serie = $bodyResponseContent->comunicacionBaja->DBR[0]->serieItem ?? "";
            $serieNumber = $bodyResponseContent->comunicacionBaja->DBR[0]->correlativoItem ?? "";
            $observation = $bodyResponseContent->comunicacionBaja->DBR[0]->motivoBajaItem ?? "";
            $ruc = $bodyResponseContent->comunicacionBaja->EMI->numeroDocId ?? null;
            }
            /** ANULAR BOLETA DE VENTA */
            if (isset($bodyResponseContent->resumenComprobantes)){
            $typeDocument = 2;
            $serieExplode = explode('-', $bodyResponseContent->resumenComprobantes->DET[0]->numeracionItem); 
            $serie = $serieExplode[0];
            $serieNumber = (int)$serieExplode[1];
            $observation = "CANCELADO";
            $ruc = $bodyResponseContent->resumenComprobantes->EMI->numeroDocId ?? null;
            }
            $arrayRequest = array(
                "operacion" => "generar_anulacion",
                "tipo_de_comprobante" => $typeDocument,
                "serie" => $serie,
                "numero" => $serieNumber,
                "motivo"=> $observation,
                "codigo_unico"=> "" 
            );   
            if (is_null($ruc)){
                $ruc = "20601285101";
            }       
            $url = NUBEFACT_ARRAY_URL[$ruc] ?? NUBEFACT_URL;
            $token = NUBEFACT_ARRAY_TOKEN[$ruc] ?? NUBEFACT_TOKEN;
            $responseParsed = $arrayRequest;
            $response = \Http::withToken($token)->post($url, $responseParsed );
            if ($response->ok()){
                $responseJson = json_decode($response);
                $result_response = array(
                    "responseCode" => "98",
                    "responseContent" => "EN PROCESO",
                    "ticket" => (string)$responseJson->sunat_ticket_numero,
                    "pseRequests" => []
                );
            } else {
                $responseJson = json_decode($response);
                if (isset($bodyResponseContent->resumenComprobantes) && $responseJson->codigo == 21){
                    $setInvoice = new \App\Models\ScheduleVoucher;
                    $setInvoice->updateOrCreate(
                        ['serie' => $serie, 'number' => $serieNumber, 'docnumber' => $ruc],
                        ['serie' => $serie, 'number' => $serieNumber, 'docnumber' => $ruc]
                    );
                    $result_response = array(
                        "responseCode" => $responseJson->codigo,
                        "responseContent" => "EN PROCESO",
                        "ticket" => "",
                        "pseRequests" => []
                    );
                } else {
                    $result_response = array(
                        "responseCode" => $responseJson->codigo,
                        "responseContent" => $responseJson->errors,
                        "ticket" => "",
                        "pseRequests" => []
                    );
                }
                \Log::info(print_r($responseJson, false));
            }
        } catch (\Throwable $th) {
        \Log::error($th);
        $result_response = array(
            "responseCode" => 99,
            "responseContent" => "Estructura JSON inválida, consule el log",
            "pseRequests" => []
        );
        return response()->json($result_response,200);
        }
        return response()->json($result_response, 200);
    }
    public function QueryInvoice(Request $request){
        $ruc = null;
        $typeDocument=$request->codCPE;
        try {
            $serie= $request->numSerieCPE;
            $serieNumber= (int)$request->numCPE;
            $ruc=$request->numDoc ?? null;
            $arrayRequest = array(
                "operacion" => "consultar_anulacion",
                "tipo_de_comprobante" => $typeDocument,
                "serie" => $serie,
                "numero" => $serieNumber,
            );    
            if (is_null($ruc)){
                $ruc = "20601285101";
            }
            $url = NUBEFACT_ARRAY_URL[$ruc] ?? NUBEFACT_URL;
            $token = NUBEFACT_ARRAY_TOKEN[$ruc] ?? NUBEFACT_TOKEN;
                $responseParsed = $arrayRequest;
                $response = \Http::withToken($token)->post($url, $responseParsed );
                if ($response->ok()){
                    $responseJson = json_decode($response);
                    $result_response = array(
                        "codigo" => "0",
                        "mensaje" => "OK",
                        "statusCode" => "0",
                        "responseCode" => "0",
                        "responseMessage" => $responseJson->sunat_description,
                        "contentFile" => base64_encode(file_get_contents($responseJson->enlace_del_xml))
                    );
                } else {
                    $responseJson = json_decode($response);
                     if ($typeDocument == 2 && $responseJson->codigo == 40){
                        $result_response = array(
                            "codigo" => -1,
                            "mensaje" => "EN PROCESO"
                        );
                     } else {
                        $result_response = array(
                            "codigo" => -1,
                            "mensaje" => $responseJson->errors
                        );
                     } 
                }
            } catch (\Throwable $th) {
            \Log::error($th);
            $result_response = array(
                "responseCode" => 99,
                "responseContent" => "Estructura JSON inválida, consule el log",
                "pseRequests" => []
            );
            return response()->json($result_response,200);
            }
            return response()->json($result_response, 200);  
    }
    public function QueryInvoiceXML(Request $request){
        $ruc = null;
        $typeDocument='';
            switch ($request->codCPE) {
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
        try {
            $serie= $request->numSerieCPE;
            $serieNumber= (int)$request->numCPE;
            $ruc=$request->numDoc ?? null;
            $arrayRequest = array(
                "operacion" => "consultar_comprobante",
                "tipo_de_comprobante" => $typeDocument,
                "serie" => $serie,
                "numero" => $serieNumber,
            );    
            if (is_null($ruc)){
                $ruc = "20601285101";
            }
            $url = NUBEFACT_ARRAY_URL[$ruc] ?? NUBEFACT_URL;
            $token = NUBEFACT_ARRAY_TOKEN[$ruc] ?? NUBEFACT_TOKEN;
                $responseParsed = $arrayRequest;
                $response = \Http::withToken($token)->post($url, $responseParsed );
                if ($response->ok()){
                    $responseJson = json_decode($response);
                    $result_response = array(
                        "codigo" => 0 ,
                        "mensaje" => "OK",
                        "xml" => base64_encode(file_get_contents($responseJson->enlace_del_xml))
                    );
                } else {
                    $responseJson = json_decode($response);
                    $result_response = array(
                        "codigo" => $responseJson->codigo,
                        "mensaje" => $responseJson->errors,
                        "xml" => ""
                    );
                }
            } catch (\Throwable $th) {
            \Log::error($th);
              return response()->json(['success' => false, 'message' => 'Estructura JSON inválida, consule el log'],500);
            }
            return response()->json($result_response, 200);  
    }
    public function QueryInvoiceQR(Request $request){
        $ruc = null;
        $typeDocument='';
        switch ($request->codCPE) {
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
    try {
        $serie= $request->numSerieCPE;
        $serieNumber= (int)$request->numCPE;
        $ruc=$request->numDoc ?? null;
        $arrayRequest = array(
            "operacion" => "consultar_comprobante",
            "tipo_de_comprobante" => $typeDocument,
            "serie" => $serie,
            "numero" => $serieNumber,
        );    
        if (is_null($ruc)){
            $ruc = "20601285101";
        }
        $url = NUBEFACT_ARRAY_URL[$ruc] ?? NUBEFACT_URL;
        $token = NUBEFACT_ARRAY_TOKEN[$ruc] ?? NUBEFACT_TOKEN;
            $responseParsed = $arrayRequest;
            $response = \Http::withToken($token)->post($url, $responseParsed );
            if ($response->ok()){
                $responseJson = json_decode($response);
                $codigoQR = QrCode::format('png')->size(300)->margin(6)->generate($responseJson->cadena_para_codigo_qr);
                $result_response = array(
                    "codigo" => 0,
                    "mensaje" => "OK",
                    "pdfQRCode" => base64_encode($codigoQR)
                );
            } else {
                $responseJson = json_decode($response);
                $result_response = array(
                    "codigo" => (string)$responseJson->codigo,
                    "mensaje" => $responseJson->errors,
                    "pdfQRCode" => ""
                );
            }
        } catch (\Throwable $th) {
        \Log::error($th);
        $result_response = array(
            "responseCode" => 99,
            "responseContent" => "Estructura JSON inválida, consule el log",
            "pseRequests" => []
        );
        return response()->json($result_response,200);
        }
        return response()->json($result_response, 200);
    }

    public function QueryInvoiceSandbox(Request $request){
        $ruc = null;
        $typeDocument='';
            switch ($request->codCPE) {
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
        try {
            $serie= $request->numSerieCPE;
            $serieNumber= (int)$request->numCPE;
            $arrayRequest = array(
                "operacion" => "consultar_comprobante",
                "tipo_de_comprobante" => $typeDocument,
                "serie" => $serie,
                "numero" => $serieNumber,
            );    
            if (is_null($ruc)){
                $ruc = "20601285101";
            }
            $url = NUBEFACT_ARRAY_URL[$ruc] ?? NUBEFACT_URL;
            $token = NUBEFACT_ARRAY_TOKEN[$ruc] ?? NUBEFACT_TOKEN;
            $responseParsed = $arrayRequest;
                $response = \Http::withToken($token)->post($url, $responseParsed );
                if ($response->ok()){
                    $responseJson = json_decode($response);
                    $result_response = array(
                        "mensaje" => "OK",
                        "statusCode" => 0,
                        "responseCode" => 0,
                        "responseMessage" => "",
                        "contentFile" => $responseJson
                    );
                } else {
                    $responseJson = json_decode($response);
                    $result_response = array(
                        "mensaje" => "ERROR",
                        "statusCode" => $responseJson->codigo,
                        "responseCode" => $responseJson->codigo,
                        "responseMessage" => $responseJson->errors,
                        "contentFile" => ""
                    );
                }
            } catch (\Throwable $th) {
            \Log::error($th);
            $result_response = array(
                "responseCode" => 99,
                "responseContent" => "Estructura JSON inválida, consule el log",
                "pseRequests" => []
            );
            return response()->json($result_response,200);
            }
            return response()->json($result_response, 200);  
    }
    public function CancelInvoiceQuerySandbox(Request $request){
        $ruc = null;
        $typeDocument='';
            switch ($request->codCPE) {
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
        try {
            $serie= $request->numSerieCPE;
            $serieNumber= (int)$request->numCPE;
            $arrayRequest = array(
                "operacion" => "consultar_anulacion",
                "tipo_de_comprobante" => $typeDocument,
                "serie" => $serie,
                "numero" => $serieNumber,
            );    
            if (is_null($ruc)){
                $ruc = "20601285101";
            }
            $url = NUBEFACT_ARRAY_URL[$ruc] ?? NUBEFACT_URL;
            $token = NUBEFACT_ARRAY_TOKEN[$ruc] ?? NUBEFACT_TOKEN;
                $responseParsed = $arrayRequest;
                $response = \Http::withToken($token)->post($url, $responseParsed );
                if ($response->ok()){
                    $responseJson = json_decode($response);
                    $result_response = array(
                        "mensaje" => "OK",
                        "statusCode" => 0,
                        "responseCode" => 0,
                        "responseMessage" => "",
                        "contentFile" => $responseJson
                    );
                } else {
                    $responseJson = json_decode($response);
                    $result_response = array(
                        "mensaje" => "ERROR",
                        "statusCode" => $responseJson->codigo,
                        "responseCode" => $responseJson->codigo,
                        "responseMessage" => $responseJson->errors,
                        "contentFile" => ""
                    );
                }
            } catch (\Throwable $th) {
            \Log::error($th);
            $result_response = array(
                "responseCode" => 99,
                "responseContent" => "Estructura JSON inválida, consule el log",
                "pseRequests" => []
            );
            return response()->json($result_response,200);
            }
            return response()->json($result_response, 200);  
    }
    public function CancelInvoiceSandbox(Request $request){
        try {
            $ruc = null;
            $typeDocument='';
                switch ($request->codCPE) {
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
            $serie = $request->numSerieCPE ?? "";
            $serieNumber = (int)$request->numCPE;
            $observation = $request->observation;
            
            $arrayRequest = array(
                "operacion" => "generar_anulacion",
                "tipo_de_comprobante" => $typeDocument,
                "serie" => $serie,
                "numero" => $serieNumber,
                "motivo"=> $observation,
                "codigo_unico"=> "" 
            );          
            if (is_null($ruc)){
                $ruc = "20601285101";
            }
            $url = NUBEFACT_ARRAY_URL[$ruc] ?? NUBEFACT_URL;
            $token = NUBEFACT_ARRAY_TOKEN[$ruc] ?? NUBEFACT_TOKEN;
            $responseParsed = $arrayRequest;
            $response = \Http::withToken($token)->post($url, $responseParsed );
            if ($response->ok()){
                $responseJson = json_decode($response);
                $result_response = array(
                    "responseCode" => "98",
                    "responseContent" => "EN PROCESO",
                    "ticket" => $responseJson,
                    "pseRequests" => []
                );
            } else {
                $responseJson = json_decode($response);
                $result_response = array(
                    "responseCode" => $responseJson->codigo,
                    "responseContent" => $responseJson->errors,
                    "ticket" => "",
                    "pseRequests" => []
                );
            }
        } catch (\Throwable $th) {
        \Log::error($th);
        $result_response = array(
            "responseCode" => 99,
            "responseContent" => "Estructura JSON inválida, consule el log",
            "pseRequests" => []
        );
        return response()->json($result_response,200);
        }
        return response()->json($result_response, 200);
    }
}
