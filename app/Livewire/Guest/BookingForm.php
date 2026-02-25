<?php

namespace App\Livewire\Guest;

use App\Actions\Booking\CreateBookingAction;
use App\Models\Apartment;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Carbon\Carbon;

class BookingForm extends Component
{
    public Apartment $apartment;

    #[Validate('required|date|after:today')]
    public $startDate;

    #[Validate('required|date|after:startDate')]
    public $endDate;

    #[Validate('required|string|min:10|max:20')]
    public $phone;

    public function mount(Apartment $apartment)
    {
        $this->apartment = $apartment;
    }

    public function book(CreateBookingAction $action)
    {
        $this->validate();

        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $action->execute(Auth::user(), $this->apartment, $this->startDate, $this->endDate, $this->phone);

        session()->flash('message', 'Booking request sent successfully!');
        
        $this->reset(['startDate', 'endDate', 'phone']);
    }
    
    public function getTotalPriceProperty()
    {
        if (!$this->startDate || !$this->endDate) return 0;
        
        try {
            $days = Carbon::parse($this->startDate)->diffInDays(Carbon::parse($this->endDate)) ?: 1;
            return $this->apartment->price * $days;
        } catch (\Exception $e) {
            return 0;
        }
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('livewire.guest.booking-form');
    }
}
