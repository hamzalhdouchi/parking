<?php

namespace Tests\Unit;

use App\Models\Parking;
use Mockery;
use PHPUnit\Framework\TestCase;

class ParkingTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function testStoreParking()
    {
        $data = [
            'name' => 'Parking center ville',
            'location' => 'Marrakech',
            'total_spaces' => 100
        ];

        $mockParking = Mockery::mock('alias:' . Parking::class);
        $mockParking->shouldReceive('create')
            ->once()
            ->with($data)
            ->andReturn((object) $data);

        $parking = $mockParking::create($data);

        $this->assertEquals('Parking center ville', $parking->name);
        $this->assertEquals('Marrakech', $parking->location);
        $this->assertEquals(100, $parking->total_spaces);

        $mockParking->shouldHaveReceived('create')->once();
    }


    public function testUpdateParking()
    {
        $data=[
            'id'=> 1,
            'name' => 'hna am3lem',
            'location' => 'sidi youssef',
            'total_spaces' => 50
        ];
        $mokeyUpdate = \Mockery::mock('alias'.Parking::class);
        $mokeyUpdate->shouldReceive('update')->with($data)->andReturn((object) $data);
        $parking = $mokeyUpdate::update($data);
        $this->assertEquals(1,$parking->id);
        $this->assertEquals('hna am3lem',$parking->name);
        $this->assertEquals('sidi youssef',$parking->location);
        $this->assertEquals(50,$parking->total_spaces);
        $mokeyUpdate->shouldHaveReceived('update')->once();


    }
    public function testFindParkingById()
    {
        $mockedParking = (object)[
            'id' => 1,
            'name' => 'Parking Central',
            'location' => 'marrakech',
            'total_spaces' => 100
        ];

        $mockParking = Mockery::mock('alias:' . Parking::class);
        $mockParking->shouldReceive('find')
            ->once()
            ->with(1)
            ->andReturn($mockedParking);

        $parking = $mockParking::find(1);

        $this->assertEquals(1, $parking->id);
        $this->assertEquals('Parking Central', $parking->name);
        $this->assertEquals('marrakech', $parking->location);
        $this->assertEquals(100, $parking->total_spaces);

        $mockParking->shouldHaveReceived('find')->once();
    }

    public function testDeleteParking()
    {
        $parkingId = 1;
    
        $mockParking = Mockery::mock('alias:' . Parking::class);
        
        $mockParking->shouldReceive('destroy')
            ->once()
            ->with($parkingId)
            ->andReturn(true);

            $result = $mockParking::destroy($parkingId);

            $this->assertTrue($result);
    }
    
}
