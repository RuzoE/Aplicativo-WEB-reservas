<?php

namespace Tests\Feature\Reception;

use App\Models\Folio;
use App\Models\Guest;
use App\Models\Order;
use App\Models\Room;
use App\Models\Stay;
use App\Models\User;
use App\Models\MinibarProduct;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StayLinkingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Crear roles necesarios
        Role::create(['name' => 'administrador', 'guard_name' => 'web']);
        Role::create(['name' => 'recepcion', 'guard_name' => 'web']);
        Role::create(['name' => 'cliente', 'guard_name' => 'web']);
    }

    public function test_receptionist_can_search_users()
    {
        $receptionist = User::factory()->create();
        $receptionist->assignRole('recepcion');

        $client = User::factory()->create([
            'name' => 'Juan',
            'last_name' => 'Perez',
            'email' => 'juan@example.com'
        ]);
        $client->assignRole('cliente');

        $response = $this->actingAs($receptionist)
            ->getJson(route('reception.users.search', ['query' => 'Juan']));

        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonFragment(['name' => 'Juan']);
    }

    public function test_receptionist_can_link_user_to_stay()
    {
        $receptionist = User::factory()->create();
        $receptionist->assignRole('recepcion');

        $client = User::factory()->create();
        $client->assignRole('cliente');

        $roomType = \App\Models\RoomType::create([
            'name' => 'Sencilla',
            'description' => 'Habitación sencilla',
            'base_price' => 100000
        ]);

        $room = Room::create([
            'room_type_id' => $roomType->id,
            'total_room' => 101,
            'no_beds' => 2,
            'status' => 1,
            'price' => 100000,
            'desc' => 'Habitación de prueba'
        ]);
        
        $guest = Guest::create([
            'first_name' => 'Test',
            'last_name' => 'Guest',
            'document_type' => 'CC',
            'document_number' => '123456',
            'email' => 'guest@example.com',
            'phone' => '123456789'
        ]);
        
        $stay = Stay::create([
            'guest_id' => $guest->id,
            'room_id' => $room->id,
            'status' => 'InHouse',
            'arrival_at' => now(),
            'departure_at' => now()->addDays(2),
            'daily_rate' => 100000
        ]);

        $response = $this->actingAs($receptionist)
            ->postJson(route('reception.stay.link_user', $stay->id), [
                'user_id' => $client->id
            ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertEquals($client->id, $stay->fresh()->user_id);
    }

    public function test_minibar_checkout_uses_linked_user()
    {
        $client = User::factory()->create();
        $client->assignRole('cliente');

        $roomType = \App\Models\RoomType::create([
            'name' => 'Doble',
            'description' => 'Habitación doble',
            'base_price' => 120000
        ]);

        $room = Room::create([
            'room_type_id' => $roomType->id,
            'total_room' => 102,
            'no_beds' => 2,
            'status' => 1,
            'price' => 120000,
            'desc' => 'Habitación de prueba 2'
        ]);
        
        $guest = Guest::create([
            'first_name' => 'Test2',
            'last_name' => 'Guest2',
            'document_type' => 'CC',
            'document_number' => '654321',
            'email' => 'guest2@example.com',
            'phone' => '987654321'
        ]);
        
        $stay = Stay::create([
            'guest_id' => $guest->id,
            'room_id' => $room->id,
            'status' => 'InHouse',
            'user_id' => $client->id,
            'arrival_at' => now(),
            'departure_at' => now()->addDays(1),
            'daily_rate' => 120000
        ]);

        $folio = Folio::create([
            'stay_id' => $stay->id,
            'number' => 'F-100',
            'status' => 'Open',
            'currency' => 'USD'
        ]);

        $product = MinibarProduct::create([
            'nombre' => 'Cola',
            'precio' => 5000,
            'stock' => 10,
            'bebida_type_id' => \App\Models\BebidaType::factory()->create()->id
        ]);

        // Mock session cart
        session(['cart' => [
            ['id' => $product->id, 'qty' => 1]
        ]]);

        $response = $this->actingAs($client)
            ->post(route('minibar.checkout.pay'), [
                'metodo_pago' => 'efectivo'
            ]);

        $response->assertRedirect();
        
        // El cargo debe estar en el folio de la estancia vinculada
        $this->assertEquals(1, $folio->charges()->count());
        $charge = $folio->charges()->first();
        $this->assertEquals(5000, $charge->amount);
    }
}
