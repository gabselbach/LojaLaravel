<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductsTest extends TestCase
{
    /** @test */
    public function ProdutosTest()
    {
        $response = $this->get('/admin/products')
        ->assertRedirect('/login');
    }
}
