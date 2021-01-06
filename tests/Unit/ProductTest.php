<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

use Illuminate\Support\Facades\DB;
class ProductTest extends TestCase
{
   #use DatabaseTransactions;
    /** @test
     */
    public function criaProduto()
    {
       
      $p =  DB::table('products')->insert(
            [
                'store_id' => 9,
                'name' =>  'coca-cola',
                'description' => 'refri',
                'body' => 'hhahahah ahahaha',
                'price' => 4.34,
                'slug' => 'coca',
            ]
        );
        $novo = action('/admin/products');
        foreach ($novo as $pg)
            $this->actingAs($p)->visit($pg)->seePageIs($pg);	
    }
}
