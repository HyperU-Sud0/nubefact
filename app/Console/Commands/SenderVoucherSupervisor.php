<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SenderVoucherSupervisor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sendervoucher:daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tarea para anulación perriodica de boletas de venta';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        \Log::info("Proceso para envio de boletas de venta diario inicializado");
        $getInvoice = \App\Models\ScheduleVoucher::where('status',0)->get();
        if (count($getInvoice)>0){
            foreach ($getInvoice as $invoice) {
                $responseInvoice = $this->sendToNubefact($invoice->serie, $invoice->number, $invoice->docnumber);
                if ($responseInvoice->status)
                {
                    $invoice->status = 1;
                    $invoice->save();
                    $responseOSI = $this->sendToOSI($invoice->serie, $invoice->number,$responseInvoice->ticket, $responseInvoice->description, $invoice->docnumber);
                    if ($responseOSI){
                        \Log::info("Boleta de venta: $invoice->serie-$invoice->number enviada correctamente a OSI");
                    } else {
                        \Log::error("Error al enviar Boleta de venta $invoice->serie-$invoice->number");
                    }
                }
            }
        }
        return Command::SUCCESS;
    }
    public function sendToNubefact($serie, $number, $ruc){
        try {
            $arrayRequest = array(
                "operacion" => "generar_anulacion",
                "tipo_de_comprobante" => 2,
                "serie" => $serie,
                "numero" => $number,
                "motivo"=> "CANCELADO",
                "codigo_unico"=> "" 
            );  
            $url = NUBEFACT_ARRAY_URL[$ruc] ?? NUBEFACT_URL;
            $token = NUBEFACT_ARRAY_TOKEN[$ruc] ?? NUBEFACT_TOKEN;
                $response = \Http::withToken($token)->post($url, $arrayRequest );
                $responseStatus = new \stdClass;
                $responseStatus->status = false;
                $responseStatus->ticket= "";
                $responseStatus->description = "";
                if ($response->ok()){
                    \Log::info("Boleta $serie-$number ha sido anulada correctamente");
                    $responseJson = json_decode($response);
                    $responseStatus->status = true;
                    $responseStatus->ticket= (string)$responseJson->sunat_ticket_numero;
                    $responseStatus->description = $responseJson->sunat_description;
                } else {
                    \Log::info("Boleta $serie-$number no fue anulada");
                    $responseStatus->status = false;
                }
            } catch (\Throwable $th) {
            \Log::error($th);
            $responseStatus->status = false;
            return $responseStatus;
            }
        return $responseStatus;
    }
    public function sendToOSI($serie, $number, $ticket, $description, $ruc){
        $responseStatus=false;
        try {
            $arrayRequest = array(
                "serie" => $serie,
                "number" => $number,
                "ticket" => $ticket,
                "comment" => $description,
                "ruc" => $ruc
            );  
                $url = OSI_URL;
                $response = \Http::post($url, $arrayRequest);
            if ($response->ok()){
                $responseJson = json_decode($response);
                if ($responseJson->accept){
                    $responseStatus=true;
                } else {
                    \Log::info($responseStatus->message);
                }
            }
            } catch (\Throwable $th) {
            \Log::error($th);
            }
        return $responseStatus;
    }
}
