<?php

namespace Tests\Unit;

use App\Models\Bebida;
use App\Models\MinibarProduct;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MinibarImageAccessorsTest extends TestCase
{
    public function test_minibar_product_image_url_uses_s3_path_when_present(): void
    {
        Storage::fake('s3');
        Storage::disk('s3')->put('minibar/test.jpg', 'content');

        $product = new MinibarProduct([
            'imagen' => 'minibar/test.jpg',
        ]);

        $this->assertStringContainsString('minibar/test.jpg', $product->image_url);
    }

    public function test_bebida_image_url_falls_back_when_missing(): void
    {
        $bebida = new Bebida([
            'imagen' => null,
        ]);

        $this->assertStringContainsString('/images/no-image.png', $bebida->image_url);
    }
}
