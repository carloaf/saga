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
        // Verificação de acesso - apenas usuários Aprov podem acessar
        $user = Auth::user();
        if (!$user || $user->role !== 'aprov') {
            abort(403, 'Acesso negado. Apenas usuários Aprov podem acessar o cardápio da semana.');
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
    public function edit(Request $request)
    {
        // Verificação de acesso - apenas usuários Aprov podem acessar
        $user = Auth::user();
        if (!$user || $user->role !== 'aprov') {
            abort(403, 'Acesso negado. Apenas usuários Aprov podem editar o cardápio da semana.');
        }

        // Se uma semana específica foi selecionada, usar essa; senão usar lógica padrão
        $selectedWeekStart = $request->get('week_start');
        
        if ($selectedWeekStart) {
            $weekStart = Carbon::parse($selectedWeekStart)->startOfWeek(Carbon::MONDAY);
        } else {
            // Lógica padrão: semana atual ou próxima
            $now = Carbon::now();
            $weekStart = $now->copy()->startOfWeek(Carbon::MONDAY);
            
            // Se for sexta-feira, sábado ou domingo, edita a próxima semana
            if ($now->dayOfWeek >= Carbon::FRIDAY) {
                $weekStart->addWeek();
            }
        }

        // Buscar cardápio da semana selecionada
        $currentWeekMenu = WeeklyMenu::getWeekMenu($weekStart->toDateString());
        $cardapio = $currentWeekMenu ? $currentWeekMenu->menu_data : WeeklyMenu::getDefaultMenuStructure();
        
        // Buscar cardápio da semana anterior como sugestão
        $previousWeekStart = $weekStart->copy()->subWeek();
        $previousWeekMenu = WeeklyMenu::getWeekMenu($previousWeekStart->toDateString());
        $cardapioAnterior = $previousWeekMenu ? $previousWeekMenu->menu_data : null;

        // Calcular as datas específicas de cada dia da semana
        $weekDates = [
            'segunda' => $weekStart->copy(),
            'terca' => $weekStart->copy()->addDay(),
            'quarta' => $weekStart->copy()->addDays(2),
            'quinta' => $weekStart->copy()->addDays(3),
            'sexta' => $weekStart->copy()->addDays(4)
        ];

        // Gerar lista de semanas disponíveis para seleção (4 semanas para trás, 8 para frente)
        $availableWeeks = [];
        for ($i = -4; $i <= 8; $i++) {
            $week = Carbon::now()->startOfWeek(Carbon::MONDAY)->addWeeks($i);
            $availableWeeks[] = [
                'value' => $week->toDateString(),
                'label' => $week->format('d/m/Y') . ' - ' . $week->copy()->endOfWeek(Carbon::FRIDAY)->format('d/m/Y'),
                'is_current' => $week->toDateString() === $weekStart->toDateString()
            ];
        }

        return view('cardapio.edit', compact(
            'cardapio', 
            'cardapioAnterior',
            'weekStart', 
            'currentWeekMenu', 
            'weekDates',
            'availableWeeks',
            'previousWeekStart'
        ));
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

    /**
     * Get previous week menu suggestions (AJAX)
     */
    public function getPreviousWeekSuggestions(Request $request)
    {
        // Verificação de acesso - apenas superusers podem acessar
        $user = Auth::user();
        if (!$user || $user->role !== 'superuser') {
            return response()->json(['error' => 'Acesso negado'], 403);
        }

        $weekStart = Carbon::parse($request->week_start)->startOfWeek(Carbon::MONDAY);
        $previousWeekStart = $weekStart->copy()->subWeek();
        $previousWeekMenu = WeeklyMenu::getWeekMenu($previousWeekStart->toDateString());
        
        $cardapioAnterior = $previousWeekMenu ? $previousWeekMenu->menu_data : null;

        return response()->json([
            'cardapio_anterior' => $cardapioAnterior,
            'previous_week_start' => $previousWeekStart->toDateString(),
            'previous_week_display' => $previousWeekStart->format('d/m/Y') . ' - ' . $previousWeekStart->copy()->endOfWeek(Carbon::FRIDAY)->format('d/m/Y')
        ]);
    }
}
