<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\WeeklyMenu;
use App\Models\User;
use Carbon\Carbon;

class CardapioController extends Controller
{
    /**
     * Display the weekly menu page
     */
    public function index()
    {
        // Verificação de acesso - apenas superusers podem acessar
        $user = Auth::user();
        if (!$user || $user->role !== 'superuser') {
            abort(403, 'Acesso negado. Apenas superusuários podem acessar o cardápio da semana.');
        }

        $currentWeekMenu = WeeklyMenu::getCurrentWeekMenu();
        $cardapio = $currentWeekMenu ? $currentWeekMenu->menu_data : WeeklyMenu::getDefaultMenuStructure();
        
        // Calcular a semana que está sendo editada
        $now = Carbon::now();
        $weekStart = $now->copy()->startOfWeek(Carbon::MONDAY);
        
        // Se for sexta-feira, sábado ou domingo, edita a próxima semana
        if ($now->dayOfWeek >= Carbon::FRIDAY) {
            $weekStart->addWeek();
        }

        // Calcular as datas específicas de cada dia da semana
        $weekDates = [
            'segunda' => $weekStart->copy(),
            'terca' => $weekStart->copy()->addDay(),
            'quarta' => $weekStart->copy()->addDays(2),
            'quinta' => $weekStart->copy()->addDays(3),
            'sexta' => $weekStart->copy()->addDays(4)
        ];

        return view('cardapio.index', compact('cardapio', 'weekStart', 'currentWeekMenu', 'weekDates'));
    }

    /**
     * Show the form for editing the weekly menu
     */
    public function edit()
    {
        // Verificação de acesso - apenas superusers podem acessar
        $user = Auth::user();
        if (!$user || $user->role !== 'superuser') {
            abort(403, 'Acesso negado. Apenas superusuários podem editar o cardápio da semana.');
        }

        $currentWeekMenu = WeeklyMenu::getCurrentWeekMenu();
        $cardapio = $currentWeekMenu ? $currentWeekMenu->menu_data : WeeklyMenu::getDefaultMenuStructure();
        
        // Calcular a semana que está sendo editada
        $now = Carbon::now();
        $weekStart = $now->copy()->startOfWeek(Carbon::MONDAY);
        
        // Se for sexta-feira, sábado ou domingo, edita a próxima semana
        if ($now->dayOfWeek >= Carbon::FRIDAY) {
            $weekStart->addWeek();
        }

        // Calcular as datas específicas de cada dia da semana
        $weekDates = [
            'segunda' => $weekStart->copy(),
            'terca' => $weekStart->copy()->addDay(),
            'quarta' => $weekStart->copy()->addDays(2),
            'quinta' => $weekStart->copy()->addDays(3),
            'sexta' => $weekStart->copy()->addDays(4)
        ];

        return view('cardapio.edit', compact('cardapio', 'weekStart', 'currentWeekMenu', 'weekDates'));
    }

    /**
     * Update the weekly menu
     */
    public function update(Request $request)
    {
        // Verificação de acesso - apenas superusers podem acessar
        $user = Auth::user();
        if (!$user || $user->role !== 'superuser') {
            abort(403, 'Acesso negado. Apenas superusuários podem editar o cardápio da semana.');
        }

        $request->validate([
            'week_start' => 'required|date',
            'menu.segunda.cafe' => 'required|string|max:500',
            'menu.segunda.almoco' => 'required|string|max:500',
            'menu.terca.cafe' => 'required|string|max:500',
            'menu.terca.almoco' => 'required|string|max:500',
            'menu.quarta.cafe' => 'required|string|max:500',
            'menu.quarta.almoco' => 'required|string|max:500',
            'menu.quinta.cafe' => 'required|string|max:500',
            'menu.quinta.almoco' => 'required|string|max:500',
            'menu.sexta.cafe' => 'required|string|max:500',
        ]);

        $weekStart = Carbon::parse($request->week_start)->startOfWeek(Carbon::MONDAY);
        $menuData = $request->input('menu');

        WeeklyMenu::createOrUpdateWeekMenu(
            $weekStart->toDateString(),
            $menuData,
            Auth::id()
        );

        return redirect()->route('cardapio.index')
                         ->with('success', 'Cardápio da semana atualizado com sucesso!');
    }

    /**
     * Get menu for a specific week (AJAX)
     */
    public function getWeekMenu(Request $request)
    {
        // Verificação de acesso - apenas superusers podem acessar
        $user = Auth::user();
        if (!$user || $user->role !== 'superuser') {
            return response()->json(['error' => 'Acesso negado'], 403);
        }

        $weekStart = Carbon::parse($request->week_start)->startOfWeek(Carbon::MONDAY);
        $weekMenu = WeeklyMenu::getWeekMenu($weekStart->toDateString());
        
        $cardapio = $weekMenu ? $weekMenu->menu_data : WeeklyMenu::getDefaultMenuStructure();

        return response()->json([
            'cardapio' => $cardapio,
            'week_start' => $weekStart->toDateString(),
            'week_display' => $weekStart->format('d/m/Y') . ' - ' . $weekStart->copy()->endOfWeek(Carbon::FRIDAY)->format('d/m/Y')
        ]);
    }
}
