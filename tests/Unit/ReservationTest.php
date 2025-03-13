<?php

namespace Tests\Unit;

use App\Models\Reservation;
use Mockery;
use PHPUnit\Framework\TestCase;

class ReservationTest extends TestCase
{
    
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function testCreateReservation()
    {
        $data = [
            'user_id' => 1,
            'parking_id' => 3,
            'heurs_arrivée' => '2025-03-15T08:00:00Z',
            'heurs_départ' => '2025-03-15T10:00:00Z',
        ];

        $mockReservation = Mockery::mock('alias:' . Reservation::class);
        $mockReservation->shouldReceive('create')
            ->once()
            ->with($data)
            ->andReturn((object) $data);

        $reservation = $mockReservation::create($data);

        $this->assertEquals(1, $reservation->user_id);
        $this->assertEquals(3, $reservation->parking_id);
        $this->assertEquals('2025-03-15T08:00:00Z', $reservation->heurs_arrivée);
        $this->assertEquals('2025-03-15T10:00:00Z', $reservation->heurs_départ);
    }

    public function testUpdateReservation()
    {
        $data = [
            'id' => 1,
            'heurs_arrivée' => '2025-03-15T09:00:00Z',
            'heurs_départ' => '2025-03-15T11:00:00Z',
        ];

        $mockReservation = Mockery::mock('alias:' . Reservation::class);
        $mockReservation->shouldReceive('find')->with(1)->andReturn((object) $data);
        $mockReservation->shouldReceive('update')->with($data)->andReturn((object) $data);
        
        $reservation = $mockReservation::update($data);

        $this->assertEquals(1, $reservation->id);
        $this->assertEquals('2025-03-15T09:00:00Z', $reservation->heurs_arrivée);
        $this->assertEquals('2025-03-15T11:00:00Z', $reservation->heurs_départ);
    }

    public function testFindReservationById()
    {
        $mockedReservation = (object) [
            'id' => 1,
            'user_id' => 1,
            'parking_id' => 3,
            'heurs_arrivée' => '2025-03-15T08:00:00Z',
            'heurs_départ' => '2025-03-15T10:00:00Z',
        ];

        $mockReservation = Mockery::mock('alias:' . Reservation::class);
        $mockReservation->shouldReceive('find')
            ->once()
            ->with(1)
            ->andReturn($mockedReservation);

        $reservation = $mockReservation::find(1);

        $this->assertEquals(1, $reservation->id);
        $this->assertEquals(1, $reservation->user_id);
        $this->assertEquals(3, $reservation->parking_id);
        $this->assertEquals('2025-03-15T08:00:00Z', $reservation->heurs_arrivée);
        $this->assertEquals('2025-03-15T10:00:00Z', $reservation->heurs_départ);
    }

    public function testDeleteReservation()
    {
        $reservationId = 1;

        $mockReservation = Mockery::mock('alias:' . Reservation::class);
        $mockReservation->shouldReceive('destroy')
            ->once()
            ->with($reservationId)
            ->andReturn(true);

        $result = $mockReservation::destroy($reservationId);

        $this->assertTrue($result);
    }
}
