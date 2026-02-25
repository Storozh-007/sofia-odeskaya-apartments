<?php

namespace App\Actions\Booking;

use App\Models\Apartment;
use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;

class CreateBookingAction
{
    public function execute(User $user, Apartment $apartment, string $startDate, string $endDate, string $phone): Booking
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        
        $days = $start->diffInDays($end) ?: 1;
        $totalPrice = $apartment->price * $days;

        return Booking::create([
            'user_id' => $user->id,
            'apartment_id' => $apartment->id,
            'start_date' => $start,
            'end_date' => $end,
            'total_price' => $totalPrice,
            'status' => 'pending',
            'phone' => $phone,
        ]);
    }
}
