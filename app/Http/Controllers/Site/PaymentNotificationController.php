<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Services\PagSeguroService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentNotificationController extends Controller
{
    protected $pagSeguroService;

    public function __construct(PagSeguroService $pagSeguroService)
    {
        $this->pagSeguroService = $pagSeguroService;
    }

    /**
     * Processa a notificação do PagSeguro
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function handlePagSeguroNotification(Request $request)
    {
        Log::info('Notificação PagSeguro recebida', $request->all());

        $result = $this->pagSeguroService->processNotification($request);

        if ($result) {
            return response('Notificação processada com sucesso', 200);
        }

        return response('Erro ao processar notificação', 400);
    }
}
