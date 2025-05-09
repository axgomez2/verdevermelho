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
     * Exibe a lista de endereços do usuário.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $addresses = Auth::user()->addresses;
        return view('site.profile.addresses.index', compact('addresses'));
    }

    /**
     * Exibe o formulário para criar um novo endereço.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('site.profile.addresses.create');
    }
    
    /**
     * Salva um novo endereço a partir do formulário.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function createAddress(Request $request)
    {
        // Validar os dados do formulário
        $validated = $request->validate([
            'type' => 'required|string|max:255',
            'zip_code' => 'required|string|max:9',
            'street' => 'required|string|max:255',
            'number' => 'required|string|max:20',
            'complement' => 'nullable|string|max:255',
            'neighborhood' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:2',
            'is_default' => 'boolean',
        ]);
        
        try {
            // Se o endereço for marcado como padrão, remover a marcação de outros endereços
            if ($request->has('is_default')) {
                Auth::user()->addresses()->update(['is_default' => false]);
            }

            // Criar o novo endereço
            $address = Auth::user()->addresses()->create([
                'type' => $validated['type'],
                'zip_code' => preg_replace('/[^0-9]/', '', $validated['zip_code']),
                'street' => $validated['street'],
                'number' => $validated['number'],
                'complement' => $validated['complement'] ?? null,
                'neighborhood' => $validated['neighborhood'],
                'city' => $validated['city'],
                'state' => $validated['state'],
                'is_default' => $request->has('is_default') ? true : false,
            ]);

            // Se for o primeiro endereço, torná-lo padrão
            if (Auth::user()->addresses()->count() === 1) {
                $address->is_default = true;
                $address->save();
            }

            // Guardar o CEP na sessão para cálculo de frete
            session(['shipping_postal_code' => preg_replace('/[^0-9]/', '', $validated['zip_code'])]);

            return redirect()->route('site.profile.addresses.index')
                ->with('success', 'Endereço adicionado com sucesso.');
        } catch (\Exception $e) {
            Log::error('Erro ao salvar endereço:', [
                'message' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return back()->withInput()->with('error', 'Ocorreu um erro ao salvar o endereço. Por favor, tente novamente.');
        }
    }
    
    /**
     * Exibe o formulário para editar um endereço existente.
     *
     * @param  \App\Models\Address  $address
     * @return \Illuminate\Http\Response
     */
    public function edit(Address $address)
    {
        // Verificar se o endereço pertence ao usuário logado
        if ($address->user_id !== Auth::id()) {
            return redirect()->route('site.profile.addresses.index')
                ->with('error', 'Você não tem permissão para editar este endereço.');
        }
        
        return view('site.profile.addresses.edit', compact('address'));
    }
    
    /**
     * Atualiza um endereço existente.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Address  $address
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Address $address)
    {
        // Verificar se o endereço pertence ao usuário logado
        if ($address->user_id !== Auth::id()) {
            return redirect()->route('site.profile.addresses.index')
                ->with('error', 'Você não tem permissão para editar este endereço.');
        }
        
        // Validar os dados do formulário
        $validated = $request->validate([
            'type' => 'required|string|max:255',
            'zip_code' => 'required|string|max:9',
            'street' => 'required|string|max:255',
            'number' => 'required|string|max:20',
            'complement' => 'nullable|string|max:255',
            'neighborhood' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:2',
        ]);
        
        try {
            // Se o endereço for marcado como padrão, remover a marcação de outros endereços
            if ($request->has('is_default')) {
                Auth::user()->addresses()->where('id', '!=', $address->id)
                    ->update(['is_default' => false]);
                $address->is_default = true;
            }

            // Atualizar o endereço
            $address->update([
                'type' => $validated['type'],
                'zip_code' => preg_replace('/[^0-9]/', '', $validated['zip_code']),
                'street' => $validated['street'],
                'number' => $validated['number'],
                'complement' => $validated['complement'] ?? $address->complement,
                'neighborhood' => $validated['neighborhood'],
                'city' => $validated['city'],
                'state' => $validated['state'],
                'is_default' => $request->has('is_default') ? true : $address->is_default,
            ]);

            return redirect()->route('site.profile.addresses.index')
                ->with('success', 'Endereço atualizado com sucesso.');
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar endereço:', [
                'message' => $e->getMessage(),
                'user_id' => Auth::id(),
                'address_id' => $address->id
            ]);

            return back()->withInput()->with('error', 'Ocorreu um erro ao atualizar o endereço. Por favor, tente novamente.');
        }
    }
    
    /**
     * Remove um endereço existente.
     *
     * @param  \App\Models\Address  $address
     * @return \Illuminate\Http\Response
     */
    public function destroy(Address $address)
    {
        // Verificar se o endereço pertence ao usuário logado
        if ($address->user_id !== Auth::id()) {
            return redirect()->route('site.profile.addresses.index')
                ->with('error', 'Você não tem permissão para excluir este endereço.');
        }
        
        try {
            // Se este for o endereço padrão e existirem outros endereços, definir outro como padrão
            if ($address->is_default) {
                $anotherAddress = Auth::user()->addresses()->where('id', '!=', $address->id)->first();
                if ($anotherAddress) {
                    $anotherAddress->is_default = true;
                    $anotherAddress->save();
                }
            }
            
            $address->delete();
            
            return redirect()->route('site.profile.addresses.index')
                ->with('success', 'Endereço excluído com sucesso.');
        } catch (\Exception $e) {
            Log::error('Erro ao excluir endereço:', [
                'message' => $e->getMessage(),
                'user_id' => Auth::id(),
                'address_id' => $address->id
            ]);
            
            return back()->with('error', 'Ocorreu um erro ao excluir o endereço. Por favor, tente novamente.');
        }
    }

    /**
     * API: Retorna a lista de endereços do usuário em formato JSON
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiIndex(Request $request)
    {
        $addresses = $request->user()->addresses()->get();
        return response()->json([
            'success' => true,
            'addresses' => $addresses
        ]);
    }

    /**
     * API: Salva um novo endereço via AJAX
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiStore(Request $request)
    {
        // Validar os dados do formulário
        $validated = $request->validate([
            'type' => 'required|string|max:255',
            'zip_code' => 'required|string|max:9',
            'street' => 'required|string|max:255',
            'number' => 'required|string|max:20',
            'complement' => 'nullable|string|max:255',
            'neighborhood' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:2',
            'is_default' => 'boolean',
        ]);
        
        try {
            // Se o endereço for marcado como padrão, remover a marcação de outros endereços
            if ($request->has('is_default') && $request->is_default) {
                Auth::user()->addresses()->update(['is_default' => false]);
            }

            // Criar o novo endereço
            $address = Auth::user()->addresses()->create([
                'type' => $validated['type'],
                'zip_code' => preg_replace('/[^0-9]/', '', $validated['zip_code']),
                'street' => $validated['street'],
                'number' => $validated['number'],
                'complement' => $validated['complement'] ?? null,
                'neighborhood' => $validated['neighborhood'],
                'city' => $validated['city'],
                'state' => $validated['state'],
                'is_default' => $request->has('is_default') && $request->is_default ? true : false,
            ]);

            // Se for o primeiro endereço, torná-lo padrão
            if (Auth::user()->addresses()->count() === 1) {
                $address->is_default = true;
                $address->save();
            }

            // Guardar o CEP na sessão para cálculo de frete
            session(['shipping_postal_code' => preg_replace('/[^0-9]/', '', $validated['zip_code'])]);

            return response()->json([
                'success' => true,
                'message' => 'Endereço adicionado com sucesso.',
                'address' => $address
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao salvar endereço via API:', [
                'message' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocorreu um erro ao salvar o endereço. Por favor, tente novamente.'
            ], 500);
        }
    }

    /**
     * API: Define um endereço como padrão
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Address  $address
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiSetDefault(Request $request, Address $address)
    {
        // Verificar se o endereço pertence ao usuário logado
        if ($address->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Você não tem permissão para modificar este endereço.'
            ], 403);
        }
        
        try {
            // Remover a marcação de padrão de todos os endereços do usuário
            Auth::user()->addresses()->where('id', '!=', $address->id)
                ->update(['is_default' => false]);
            
            // Definir este endereço como padrão
            $address->is_default = true;
            $address->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Endereço definido como padrão com sucesso.'
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao definir endereço como padrão via API:', [
                'message' => $e->getMessage(),
                'user_id' => Auth::id(),
                'address_id' => $address->id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Ocorreu um erro ao definir o endereço como padrão. Por favor, tente novamente.'
            ], 500);
        }
    }

    /**
     * API: Remove um endereço existente
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Address  $address
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiDestroy(Request $request, Address $address)
    {
        // Verificar se o endereço pertence ao usuário logado
        if ($address->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Você não tem permissão para excluir este endereço.'
            ], 403);
        }
        
        try {
            $wasDefault = $address->is_default;
            
            // Excluir o endereço
            $address->delete();
            
            // Se o endereço era padrão, definir outro como padrão, se houver
            if ($wasDefault) {
                $anotherAddress = Auth::user()->addresses()->first();
                if ($anotherAddress) {
                    $anotherAddress->is_default = true;
                    $anotherAddress->save();
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Endereço excluído com sucesso.'
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao excluir endereço via API:', [
                'message' => $e->getMessage(),
                'user_id' => Auth::id(),
                'address_id' => $address->id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Ocorreu um erro ao excluir o endereço. Por favor, tente novamente.'
            ], 500);
        }
    }
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
