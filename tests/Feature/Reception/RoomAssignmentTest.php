<?php

namespace Tests\Feature\Reception;

use App\Models\Order;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoomAssignmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::create(['name' => 'recepcion', 'guard_name' => 'web']);
    }

    public function test_can_assign_room_to_order_with_advance_payment()
    {
        $receptionist = User::factory()->create();
        $receptionist->assignRole('recepcion');

        $roomType = RoomType::create(['name' => 'Suite']);

        $room = Room::create([
            'room_type_id' => $roomType->id,
            'total_room' => 201,
            'no_beds' => 2,
            'status' => Room::STATUS_DISPONIBLE,
            'price' => 200000,
            'desc' => 'Suite presidencial'
        ]);

        $order = Order::create([
            'user_id' => $receptionist->id,
            'room_type_id' => $roomType->id,
            'status' => Order::STATUS_ANTICIPO_PAGADO,
            'check_in' => now(),
            'check_out' => now()->addDays(2),
            'nombre_cliente' => 'Cliente de Prueba'
        ]);

        $response = $this->actingAs($receptionist)
            ->post(route('reception.asignacion.confirm', ['reserva' => $order->id, 'room' => $room->id]));

        $response->assertRedirect(route('reception.asignacion.index'));

        $this->assertEquals(Room::STATUS_DISPONIBLE, $room->fresh()->status);
        $this->assertEquals(Order::STATUS_RESERVA_PREVIA, $order->fresh()->status);
        $this->assertEquals($room->id, $order->fresh()->room_id);
        $this->assertNotNull($order->fresh()->room_number);
    }
}
