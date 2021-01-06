<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

use Illuminate\Support\Facades\DB;
class UserTest extends TestCase
{
    #use DatabaseTransactions;
    /** @test
     */
    public function criaUser()
    {
       $data = [
        'name' => 'brendonX',
       'email' => 'brendonX@gmail.com',
        'email_verified_at' => now(),
        'password' => bcrypt('12345'),
       'remember_token' => '12345',
       ];
       DB::table('users')->insert( $data);
        $this->assertDatabaseHas('users', $data);

    }
}
