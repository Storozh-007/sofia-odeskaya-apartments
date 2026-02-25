<?php

namespace App\Livewire\Admin;

use App\Models\Apartment;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Component;

class CheckIn extends Component
{
    public $start_date;
    public $end_date;
    public $capacity = 1;
    
    // Step state
    public int $step = 1;
    
    // Available rooms
    public $availableRooms = [];
    public $selectedRoomId = null;

    // Guest Info
    public $guestType = 'new'; // 'new' or 'existing'
    public $existingUserId = null;
    public $guestForm = [
        'name' => '',
        'email' => '',
        'phone' => '',
        'passport_data' => '',
    ];

    // Receipt
    public $receipt = null;

    public function mount()
    {
        $this->start_date = Carbon::today()->format('Y-m-d');
        $this->end_date = Carbon::tomorrow()->format('Y-m-d');
    }

    public function searchRooms()
    {
        $this->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'capacity' => 'required|integer|min:1',
        ]);

        // Find rooms that do not have overlapping ACTIVE/PENDING bookings
        $this->availableRooms = Apartment::where('capacity', '>=', $this->capacity)
            ->whereDoesntHave('bookings', function($query) {
                $query->whereIn('status', ['active', 'pending'])
                      ->where(function($q) {
                          $q->whereBetween('start_date', [$this->start_date, $this->end_date])
                            ->orWhereBetween('end_date', [$this->start_date, $this->end_date])
                            ->orWhere(function($q2) {
                                $q2->where('start_date', '<=', $this->start_date)
                                   ->where('end_date', '>=', $this->end_date);
                            });
                      });
            })->get();

        $this->step = 2;
    }

    public function selectRoom($id)
    {
        $this->selectedRoomId = $id;
        $this->step = 3;
    }

    public function confirmCheckIn()
    {
        if ($this->guestType === 'new') {
            $this->validate([
                'guestForm.name' => 'required|string|max:255',
                'guestForm.email' => 'required|email|unique:users,email',
                'guestForm.phone' => 'required|string|max:50',
                'guestForm.passport_data' => 'required|string|max:255',
            ]);

            $user = User::create([
                'name' => $this->guestForm['name'],
                'email' => $this->guestForm['email'],
                'password' => Hash::make('password123'), // default password for admin created
                'role' => 'user',
                'passport_data' => $this->guestForm['passport_data'],
            ]);
        } else {
            $this->validate([
                'existingUserId' => 'required|exists:users,id',
                'guestForm.phone' => 'required|string|max:50',
            ]);
            $user = User::find($this->existingUserId);
            // Optionally update passport if empty
            if (empty($user->passport_data) && !empty($this->guestForm['passport_data'])) {
                $user->update(['passport_data' => $this->guestForm['passport_data']]);
            }
        }

        $room = Apartment::findOrFail($this->selectedRoomId);
        
        $days = Carbon::parse($this->start_date)->diffInDays(Carbon::parse($this->end_date));
        $totalPrice = $room->price * $days;

        $booking = Booking::create([
            'user_id' => $user->id,
            'apartment_id' => $room->id,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'total_price' => $totalPrice,
            'status' => 'active', // Checked in
            'phone' => $this->guestForm['phone'],
        ]);

        // Mark room as taken
        $room->update(['status' => 'taken']);

        // Generate receipt data
        $this->receipt = [
            'booking_id' => $booking->id,
            'guest_name' => $user->name,
            'passport' => $user->passport_data,
            'room_title' => $room->title,
            'room_class' => $room->room_class ?: 'Standard',
            'arrival' => $this->start_date,
            'departure' => $this->end_date,
            'nights' => $days,
            'price_per_night' => $room->price / 100,
            'total' => $totalPrice / 100,
            'date_issued' => now()->format('Y-m-d H:i:s'),
        ];

        $this->step = 4;
    }

    public function resetForm()
    {
        $this->reset(['start_date', 'end_date', 'capacity', 'step', 'availableRooms', 'selectedRoomId', 'guestType', 'existingUserId', 'guestForm', 'receipt']);
        $this->mount();
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('livewire.admin.check-in', [
            'allUsers' => User::where('role', 'user')->get()
        ]);
    }
}
