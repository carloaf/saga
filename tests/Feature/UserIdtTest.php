<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Rank;
use App\Models\Organization;
use PHPUnit\Framework\Attributes\Test;
// CSRF será tratado via sessão + header helper

class UserIdtTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    private function csrfHeaders(): array
    {
        // Garante sessão iniciada com token consistente para cada requisição mutante
        $this->withSession(['_token' => 'testtoken']);
        return ['X-CSRF-TOKEN' => 'testtoken'];
    }
    #[Test]
    public function manager_can_create_user_with_unique_idt(): void
    {
        $manager = $this->createManager();

        $payload = [
            'idt' => 'IDT12345',
            'full_name' => 'Novo Usuário',
            'war_name' => 'NOVO',
            'email' => 'novo@example.com',
            'rank_id' => $manager->rank_id,
            'organization_id' => $manager->organization_id,
            'subunit' => null,
            'armed_force' => 'EB',
            'gender' => 'M',
            'ready_at_om_date' => now()->format('Y-m-d'),
            'role' => 'user',
            'is_active' => true,
        ];

        $resp = $this->actingAs($manager)
            ->withHeaders($this->csrfHeaders())
            ->postJson('/admin/users', $payload);
        $resp->assertStatus(200)->assertJson(['success' => true]);

        $this->assertDatabaseHas('users', [
            'email' => 'novo@example.com',
            'idt' => 'IDT12345',
        ]);
    }

    #[Test]
    public function cannot_create_two_users_with_same_idt(): void
    {
        $manager = $this->createManager();
        $existing = User::create([
            'google_id' => 'g1',
            'idt' => 'DUP001',
            'full_name' => 'User 1',
            'war_name' => 'U1',
            'email' => 'u1@example.com',
            'rank_id' => $manager->rank_id,
            'organization_id' => $manager->organization_id,
            'armed_force' => 'EB',
            'gender' => 'M',
            'ready_at_om_date' => now(),
            'role' => 'user',
            'is_active' => true,
        ]);

        $resp = $this->actingAs($manager)
            ->withHeaders($this->csrfHeaders())
            ->postJson('/admin/users', [
            'idt' => 'DUP001',
            'full_name' => 'User 2',
            'war_name' => 'U2',
            'email' => 'u2@example.com',
            'rank_id' => $manager->rank_id,
            'organization_id' => $manager->organization_id,
            'subunit' => null,
            'armed_force' => 'EB',
            'gender' => 'M',
            'ready_at_om_date' => now()->format('Y-m-d'),
            'role' => 'user',
            'is_active' => true,
        ]);

        $resp->assertStatus(422); // validation error
    }

    #[Test]
    public function idt_is_immutable_on_update(): void
    {
        $manager = $this->createManager();
        $user = User::create([
            'google_id' => 'g2',
            'idt' => 'LOCK123',
            'full_name' => 'User Lock',
            'war_name' => 'LOCK',
            'email' => 'lock@example.com',
            'rank_id' => $manager->rank_id,
            'organization_id' => $manager->organization_id,
            'armed_force' => 'EB',
            'gender' => 'M',
            'ready_at_om_date' => now(),
            'role' => 'user',
            'is_active' => true,
        ]);

        $resp = $this->actingAs($manager)
            ->withHeaders($this->csrfHeaders())
            ->patchJson('/admin/users/' . $user->id, [
            'idt' => 'CHANGED999', // tentativa de mudar
            'full_name' => 'User Lock Updated',
            'war_name' => 'LOCKU',
            'email' => 'lock@example.com',
            'rank_id' => $manager->rank_id,
            'organization_id' => $manager->organization_id,
            'subunit' => null,
            'armed_force' => 'EB',
            'gender' => 'M',
            'ready_at_om_date' => now()->format('Y-m-d'),
            'role' => 'user',
            'is_active' => true,
        ]);

        $resp->assertStatus(200)->assertJson(['success' => true]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'idt' => 'LOCK123', // permanece o original
            'full_name' => 'User Lock Updated',
        ]);
    }

    private function createManager(): User
    {
        $rank = Rank::first() ?: Rank::create(['name' => 'Capitão']);
        $org = Organization::first() ?: Organization::create(['name' => 'Comando Geral']);

        return User::create([
            'google_id' => 'manager_'.uniqid(),
            'idt' => 'MGR'.mt_rand(1000,9999),
            'full_name' => 'Manager Test',
            'war_name' => 'MGR',
            'email' => 'manager'.uniqid().'@example.com',
            'rank_id' => $rank->id,
            'organization_id' => $org->id,
            'armed_force' => 'EB',
            'gender' => 'M',
            'ready_at_om_date' => now(),
            'role' => 'manager',
            'is_active' => true,
        ]);
    }
}
