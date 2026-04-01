<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Organization;
use App\Models\Rank;
use App\Models\User;
use Carbon\Carbon;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminReportsTest extends TestCase
{
    #[Test]
    public function weekly_summary_excel_report_downloads_successfully()
    {
        $organization = Organization::create([
            'name' => 'Organizacao Teste',
            'is_host' => true,
        ]);

        $rank = Rank::create([
            'name' => 'Tenente Teste',
            'hierarchy' => 1,
        ]);

        $manager = User::create([
            'full_name' => 'Gestor de Teste',
            'war_name' => 'GESTOR',
            'email' => 'gestor.relatorios@example.com',
            'google_id' => 'reports_test_manager',
            'organization_id' => $organization->id,
            'rank_id' => $rank->id,
            'role' => 'manager',
            'armed_force' => 'EB',
            'gender' => 'M',
            'ready_at_om_date' => now()->format('Y-m-d'),
            'idt' => 'TSTREL001',
            'is_active' => true,
        ]);

        $startDate = Carbon::create(2026, 4, 1);
        $endDate = Carbon::create(2026, 4, 7);

        Booking::create([
            'user_id' => $manager->id,
            'booking_date' => $startDate->copy(),
            'meal_type' => 'breakfast',
            'status' => 'confirmed',
        ]);

        Booking::create([
            'user_id' => $manager->id,
            'booking_date' => $startDate->copy()->addDay(),
            'meal_type' => 'lunch',
            'status' => 'confirmed',
        ]);

        $response = $this->actingAs($manager)->get(route('admin.reports.generate', [
            'report_type' => 'weekly_summary',
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'format' => 'excel',
        ]));

        $response->assertOk();
        $response->assertHeader('content-disposition');
        $this->assertStringContainsString(
            'resumo_semanal_' . $startDate->format('Y-m-d') . '_' . $endDate->format('Y-m-d') . '.xlsx',
            $response->headers->get('content-disposition', '')
        );
    }
}