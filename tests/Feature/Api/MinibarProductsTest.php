<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\BebidaType;
use App\Models\MinibarProduct;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MinibarProductsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate('minibar', 'web');
    }

    private function actingAsMinibarUser(): User
    {
        $user = User::factory()->create();
        $user->assignRole('minibar');

        Sanctum::actingAs($user, ['minibar:write']);

        return $user;
    }

    /** @test */
    public function puede_listar_las_bebidas_publicamente()
    {
        MinibarProduct::factory()->count(5)->create();

        $response = $this->getJson('/api/minibar-products');

        $response->assertOk()
                 ->assertJsonStructure(['current_page', 'data', 'total']);
    }

    /** @test */
    public function requiere_token_para_crear_un_producto()
    {
        $type = BebidaType::factory()->create();

        $this->postJson('/api/minibar-products', [
            'nombre' => 'Agua 500ml',
            'precio' => 2500,
            'bebida_type_id' => $type->id,
            'stock' => 10,
        ])->assertStatus(401);
    }

    /** @test */
    public function puede_crear_producto_con_token()
    {
        $this->actingAsMinibarUser();
        $type = BebidaType::factory()->create();

        $response = $this->postJson('/api/minibar-products', [
            'nombre' => 'Agua 500ml',
            'precio' => 2500,
            'bebida_type_id' => $type->id,
            'stock' => 10,
        ]);

        $response->assertCreated()
                 ->assertJsonPath('data.nombre', 'Agua 500ml');
    }

    /** @test */
    public function puede_actualizar_producto_con_token()
    {
        $this->actingAsMinibarUser();
        $producto = MinibarProduct::factory()->create(['precio' => 2000]);

        $response = $this->putJson("/api/minibar-products/{$producto->id}", [
            'precio' => 3000
        ]);

        $response->assertOk()
                 ->assertJsonPath('data.precio', 3000);
    }

    /** @test */
    public function puede_eliminar_producto_con_token()
    {
        $this->actingAsMinibarUser();
        $producto = MinibarProduct::factory()->create();

        $this->deleteJson("/api/minibar-products/{$producto->id}")
             ->assertNoContent();
    }
}
