<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class WeeklyMenu extends Model
{
    protected $fillable = [
        'week_start',
        'menu_data',
        'is_active',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'week_start' => 'date',
        'menu_data' => 'array',
        'is_active' => 'boolean'
    ];

    /**
     * Usuário que criou o cardápio
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Último usuário que editou o cardápio
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Obtém o cardápio da semana atual ou próxima
     */
    public static function getCurrentWeekMenu()
    {
        $now = Carbon::now();
        $startOfWeek = $now->copy()->startOfWeek(Carbon::MONDAY);
        
        // Se for sexta-feira, sábado ou domingo, pega a próxima semana
        if ($now->dayOfWeek >= Carbon::FRIDAY) {
            $startOfWeek->addWeek();
        }

        return self::where('week_start', $startOfWeek->toDateString())
                   ->where('is_active', true)
                   ->first();
    }

    /**
     * Obtém o cardápio da semana específica
     */
    public static function getWeekMenu($weekStart)
    {
        return self::where('week_start', $weekStart)
                   ->where('is_active', true)
                   ->first();
    }

    /**
     * Cria ou atualiza o cardápio da semana
     */
    public static function createOrUpdateWeekMenu($weekStart, $menuData, $userId)
    {
        return self::updateOrCreate(
            ['week_start' => $weekStart, 'is_active' => true],
            [
                'menu_data' => $menuData,
                'updated_by' => $userId,
                'created_by' => $userId
            ]
        );
    }

    /**
     * Estrutura padrão do cardápio
     */
    public static function getDefaultMenuStructure()
    {
        return [
            'segunda' => [
                'cafe' => '',
                'almoco' => ''
            ],
            'terca' => [
                'cafe' => '',
                'almoco' => ''
            ],
            'quarta' => [
                'cafe' => '',
                'almoco' => ''
            ],
            'quinta' => [
                'cafe' => '',
                'almoco' => ''
            ],
            'sexta' => [
                'cafe' => ''
                // Sexta não tem almoço
            ]
        ];
    }
}
