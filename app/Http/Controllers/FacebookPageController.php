<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FacebookPage;
use Illuminate\Support\Facades\Validator;

class FacebookPageController extends Controller
{
    public function store(Request $request)
{
    // Validação dos dados recebidos
    $validator = Validator::make($request->all(), [
        'page_id' => 'required|unique:page_campaigns,page_id', // Adicione o nome da tabela e da coluna
    ]);

    // Se a validação falhar, redirecionar de volta com os erros
    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator)
            ->withInput();
    }

    $page = new FacebookPage;
    $page->page_id = $request->page_id;
    $page->save();
    
    return redirect()->route('welcome') // Substitua 'welcome' pela rota desejada
        ->with('success', 'Página do Facebook cadastrada com sucesso!');
}

}
