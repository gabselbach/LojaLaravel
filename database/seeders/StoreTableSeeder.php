<?php

use Illuminate\Database\Seeder;

class StoreTableSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        $stores = \App\Store::all();

        foreach($stores as $store)
        {
            $store->products()->save(factory(\App\Product::class)->make());
        }
    }
}
