<?php

namespace Tests\Feature;

use App\Mail\ReservationPendingMail;
use App\Models\Order;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OrderStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_pending_payment_reservation_and_sends_the_payment_email()
    {
        Mail::fake();

        $roomType = RoomType::create(['name' => 'Doble']);
        $room = Room::create([
            'room_type_id' => $roomType->id,
            'total_room' => 101,
            'no_beds' => 2,
            'status' => Room::STATUS_DISPONIBLE,
            'price' => 150000,
            'desc' => 'Habitación doble para pruebas',
        ]);

        $response = $this->post(route('orders.store'), [
            'email' => 'cliente@example.com',
            'check_in' => now()->addDay()->toDateString(),
            'check_out' => now()->addDays(3)->toDateString(),
            'room_id' => $room->id,
        ]);

        $response->assertRedirect(route('orders.index'));

        $order = Order::query()->latest('id')->firstOrFail();

        $this->assertSame('pendiente_pago', $order->status);
        $this->assertFalse((bool) $order->is_paid);
        $this->assertNotNull($order->payment_token);
        $this->assertSame($roomType->id, $order->room_type_id);

        Mail::assertSent(ReservationPendingMail::class, function ($mail) use ($order) {
            return $mail->hasTo('cliente@example.com')
                && $mail->order->is($order)
                && str_contains($mail->paymentUrl, $order->payment_token);
        });
    }

    public function test_it_updates_to_pre_reservation_only_after_payment_confirmation()
    {
        Mail::fake();

        $roomType = RoomType::create(['name' => 'Doble']);
        $user = \App\Models\User::factory()->create();
        $order = Order::create([
            'check_in' => now()->addDay(),
            'check_out' => now()->addDays(3),
            'room_type_id' => $roomType->id,
            'user_id' => $user->id,
            'status' => 'pendiente_pago',
            'down_payment_amount' => 50000,
            'is_paid' => false,
        ]);

        $response = $this->post(route('orders.confirm_payment', ['token' => $order->payment_token]));

        $response->assertRedirect(route('orders.index'));

        $order->refresh();

        $this->assertTrue((bool) $order->is_paid);
        $this->assertSame(Order::STATUS_RESERVA_PREVIA, $order->status);
    }
}
