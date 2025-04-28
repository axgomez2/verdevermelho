<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Newsletter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NewsletterController extends Controller
{
    /**
     * Salvar um email na newsletter
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255'
        ], [
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'Por favor, insira um e-mail válido.',
            'email.max' => 'O e-mail não pode ter mais de 255 caracteres.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first('email')
            ]);
        }

        $email = $request->input('email');

        // Verificar se o email já está cadastrado
        if (Newsletter::isEmailRegistered($email)) {
            return response()->json([
                'success' => false,
                'message' => 'Este e-mail já está cadastrado em nossa newsletter.'
            ]);
        }

        // Salvar o email na newsletter
        try {
            Newsletter::create([
                'email' => $email,
                'is_active' => true
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Obrigado por se inscrever em nossa newsletter!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocorreu um erro ao processar sua inscrição. Por favor, tente novamente.'
            ]);
        }
    }
}
