<?php

use App\Actions\Booking\CreateBookingAction;
use App\Models\Apartment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guest can create a booking', function () {
    $user = User::factory()->create(['role' => 'guest']);
    $apartment = Apartment::factory()->create(['price' => 10000]); // $100.00
    
    $action = new CreateBookingAction();
    
    $startDate = '2024-06-01';
    $endDate = '2024-06-03'; // 2 days
    
    $booking = $action->execute($user, $apartment, $startDate, $endDate);
    
    expect($booking)
        ->user_id->toBe($user->id)
        ->apartment_id->toBe($apartment->id)
        ->total_price->toBe(20000) // $200.00
        ->status->toBe('pending');
        
    $this->assertDatabaseHas('bookings', [
        'id' => $booking->id,
        'total_price' => 20000,
    ]);
});
