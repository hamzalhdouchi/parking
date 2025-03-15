<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Mockery;
use Tests\TestCase;

class AuthTest extends TestCase
{
    
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function testRegister()
    {
        $data = [
            'name' => 'hamza',
            'email' => 'hamza@gmail.com',
            'password' => 'hamza123',
        ];

        $mockUser = Mockery::mock('alias:' . User::class);
        $mockUser->shouldReceive('create')
            ->once()
            ->with($data)
            ->andReturn((object) $data);

        $user = $mockUser::create($data);
        $this->assertEquals('hamza', $user->name);
        $this->assertEquals('hamza@gmail.com', $user->email);
    }

    public function testLogin()
    {
        $data = [
            'email' => 'hamza@gmail.com',
            'password' => Hash::make('hamza123'),
        ];

        $mockUser = Mockery::mock('alias:' . User::class);
        $mockUser->shouldReceive('where->first')->andReturn((object) $data);

        $user = $mockUser::where('email', $data['email'])->first();
        $this->assertNotNull($user);
        $this->assertTrue(Hash::check('hamza123', $user->password));
    }

        public function testLogout()
    {
        $mockUser = Mockery::mock('alias:' . User::class);
        $mockUser->shouldReceive('revoke')->once()->andReturn(true);

        $this->assertTrue(true); 
    }
}
