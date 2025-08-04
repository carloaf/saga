<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CardapioController extends Controller
{
    /**
     * Display the weekly menu page
     */
    public function index()
    {
        // Verificação de acesso - apenas superusers podem acessar
        if (!Auth::user() || !Auth::user()->isSuperuser()) {
            abort(403, 'Acesso negado. Apenas superusuários podem acessar o cardápio da semana.');
        }

        // Dados do cardápio da semana (exemplo)
        $cardapio = [
            'segunda' => [
                'cafe' => 'Café, Pão Francês, Manteiga, Leite',
                'almoco' => 'Arroz, Feijão, Carne Assada, Salada Verde'
            ],
            'terca' => [
                'cafe' => 'Café, Pão de Forma, Requeijão, Leite',
                'almoco' => 'Arroz, Feijão, Frango Grelhado, Legumes'
            ],
            'quarta' => [
                'cafe' => 'Café, Pão Francês, Presunto e Queijo, Leite',
                'almoco' => 'Arroz, Feijão, Peixe Assado, Salada Mista'
            ],
            'quinta' => [
                'cafe' => 'Café, Pão de Forma, Manteiga, Leite',
                'almoco' => 'Arroz, Feijão, Carne de Porco, Purê de Batata'
            ],
            'sexta' => [
                'cafe' => 'Café, Pão Francês, Geleia, Leite'
                // Sexta não tem almoço
            ]
        ];

        return view('cardapio.index', compact('cardapio'));
    }
}
