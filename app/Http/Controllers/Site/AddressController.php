<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AddressController extends Controller
{
    /**
     * Store a newly created address in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Verificar se o usuário está autenticado
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Você precisa estar logado para adicionar um endereço.'
            ], 401);
        }

        // Preparar os dados para validação
        $data = $request->all();
        
        // Converter o campo is_default para boolean
        if ($request->has('is_default')) {
            $data['is_default'] = filter_var($request->is_default, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ? true : false;
        } else {
            $data['is_default'] = false;
        }
        
        // Validar os dados do formulário
        $validator = Validator::make($data, [
            'type' => 'required|string|max:255',
            'zip_code' => 'required|string|max:9',
            'street' => 'required|string|max:255',
            'number' => 'required|string|max:20',
            'complement' => 'nullable|string|max:255',
            'neighborhood' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:2',
            'is_default' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Se o endereço for marcado como padrão, remover a marcação de outros endereços
            if ($data['is_default']) {
                Auth::user()->addresses()->update(['is_default' => false]);
            }

            // Criar o novo endereço
            $address = Auth::user()->addresses()->create([
                'type' => $data['type'],
                'zip_code' => preg_replace('/[^0-9]/', '', $data['zip_code']),
                'street' => $data['street'],
                'number' => $data['number'],
                'complement' => $data['complement'] ?? null,
                'neighborhood' => $data['neighborhood'],
                'city' => $data['city'],
                'state' => $data['state'],
                'is_default' => $data['is_default']
            ]);

            // Se não houver outros endereços, tornar este o padrão
            if (Auth::user()->addresses()->count() === 1) {
                $address->is_default = true;
                $address->save();
            }

            // Guardar o CEP na sessão para cálculo de frete
            session(['shipping_postal_code' => preg_replace('/[^0-9]/', '', $request->zip_code)]);

            return response()->json([
                'success' => true,
                'message' => 'Endereço adicionado com sucesso',
                'address' => $address
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao salvar endereço:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao salvar endereço: ' . $e->getMessage()
            ], 500);
        }
    }
}
