<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Rank;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Atualizar graduação de "Usuário Externo" para "Soldado EV"
        $rank = Rank::where('name', 'Usuário Externo')->first();
        
        if ($rank) {
            $rank->update([
                'name' => 'Soldado EV',
                'abbreviation' => 'Sd EV'
            ]);
        } else {
            // Se não encontrar "Usuário Externo", criar nova graduação na última posição
            $maxOrder = Rank::max('order') ?? 16;
            
            Rank::create([
                'name' => 'Soldado EV',
                'abbreviation' => 'Sd EV',
                'order' => $maxOrder + 1
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverter para "Usuário Externo"
        $rank = Rank::where('name', 'Soldado EV')->first();
        
        if ($rank) {
            $rank->update([
                'name' => 'Usuário Externo',
                'abbreviation' => 'Usr Ext'
            ]);
        }
    }
};
