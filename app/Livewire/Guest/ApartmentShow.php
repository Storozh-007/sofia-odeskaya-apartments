<?php

namespace App\Livewire\Guest;

use App\Models\Apartment;
use Livewire\Attributes\Layout;
use Livewire\Component;

class ApartmentShow extends Component
{
    public Apartment $apartment;

    public function mount(Apartment $apartment)
    {
        $this->apartment = $apartment->load('images');
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('livewire.guest.apartment-show');
    }
}
