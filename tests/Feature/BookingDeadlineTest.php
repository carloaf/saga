<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Booking;
use App\Models\Organization;
use App\Models\Rank;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BookingDeadlineTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test organization and rank
        $organization = Organization::create([
            'name' => 'Test Organization',
            'is_host' => true,
        ]);
        
        $rank = Rank::create([
            'name' => 'Test Rank',
            'hierarchy' => 1,
        ]);
        
        // Create test user
        $this->user = User::create([
            'full_name' => 'Test User',
            'war_name' => 'Test',
            'email' => 'test@example.com',
            'google_id' => 'test123',
            'organization_id' => $organization->id,
            'rank_id' => $rank->id,
            'role' => 'user',
        ]);
    }

    /** @test */
    public function cannot_cancel_booking_after_1pm_previous_day()
    {
        // Create a booking for tomorrow
        $tomorrow = Carbon::tomorrow();
        $booking = Booking::create([
            'user_id' => $this->user->id,
            'booking_date' => $tomorrow->format('Y-m-d'),
            'meal_type' => 'breakfast',
            'status' => 'confirmed',
        ]);

        // Mock current time to be after 1 PM today (past deadline)
        Carbon::setTestNow(Carbon::today()->setTime(14, 0, 0));

        $response = $this->actingAs($this->user)
            ->deleteJson("/bookings/{$booking->id}");

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'Não é possível cancelar reservas após às 13h do dia anterior. Prazo expirado em ' . Carbon::today()->setTime(13, 0, 0)->format('d/m/Y \à\s H:i') . '.'
            ]);

        // Booking should still exist
        $this->assertDatabaseHas('bookings', ['id' => $booking->id]);
    }

    /** @test */
    public function can_cancel_booking_before_1pm_previous_day()
    {
        // Create a booking for tomorrow
        $tomorrow = Carbon::tomorrow();
        $booking = Booking::create([
            'user_id' => $this->user->id,
            'booking_date' => $tomorrow->format('Y-m-d'),
            'meal_type' => 'breakfast',
            'status' => 'confirmed',
        ]);

        // Mock current time to be before 1 PM today (within deadline)
        Carbon::setTestNow(Carbon::today()->setTime(12, 0, 0));

        $response = $this->actingAs($this->user)
            ->deleteJson("/bookings/{$booking->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        // Booking should be deleted
        $this->assertDatabaseMissing('bookings', ['id' => $booking->id]);
    }

    /** @test */
    public function cannot_create_booking_after_1pm_previous_day()
    {
        // Mock current time to be after 1 PM today
        Carbon::setTestNow(Carbon::today()->setTime(14, 0, 0));

        $tomorrow = Carbon::tomorrow();
        
        $response = $this->actingAs($this->user)
            ->postJson('/bookings/reserve-single', [
                'date' => $tomorrow->format('Y-m-d'),
                'meal_type' => 'breakfast',
            ]);

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'Não é possível fazer reservas após às 13h do dia anterior. Prazo expirado em ' . Carbon::today()->setTime(13, 0, 0)->format('d/m/Y \à\s H:i') . '.'
            ]);

        // Booking should not be created
        $this->assertDatabaseMissing('bookings', [
            'user_id' => $this->user->id,
            'booking_date' => $tomorrow->format('Y-m-d'),
            'meal_type' => 'breakfast',
        ]);
    }

    /** @test */
    public function can_create_booking_before_1pm_previous_day()
    {
        // Mock current time to be before 1 PM today
        Carbon::setTestNow(Carbon::today()->setTime(12, 0, 0));

        $tomorrow = Carbon::tomorrow();
        
        $response = $this->actingAs($this->user)
            ->postJson('/bookings/reserve-single', [
                'date' => $tomorrow->format('Y-m-d'),
                'meal_type' => 'breakfast',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        // Booking should be created
        $this->assertDatabaseHas('bookings', [
            'user_id' => $this->user->id,
            'booking_date' => $tomorrow->format('Y-m-d'),
            'meal_type' => 'breakfast',
        ]);
    }

    /** @test */
    public function deadline_applies_to_weekend_bookings()
    {
        // Find next Monday
        $monday = Carbon::now()->next(Carbon::MONDAY);
        
        // Mock current time to be Saturday at 2 PM (after Friday 1 PM deadline)
        $friday = $monday->copy()->subDays(3); // Friday before Monday
        Carbon::setTestNow($friday->setTime(14, 0, 0));

        $response = $this->actingAs($this->user)
            ->postJson('/bookings/reserve-single', [
                'date' => $monday->format('Y-m-d'),
                'meal_type' => 'breakfast',
            ]);

        $response->assertStatus(400)
            ->assertJsonFragment(['success' => false]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow(); // Reset Carbon time
        parent::tearDown();
    }
}
