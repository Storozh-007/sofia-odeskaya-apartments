<?php

namespace App\Livewire\Guest;

use App\Models\Apartment;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ApartmentReviews extends Component
{
    public Apartment $apartment;

    #[Validate('required|integer|min:1|max:5')]
    public $rating = 5;

    #[Validate('required|string|min:5|max:500')]
    public $comment = '';

    public function mount(Apartment $apartment)
    {
        $this->apartment = $apartment;
    }

    public function submit()
    {
        $this->validate();

        if (!Auth::check()) {
            return redirect()->route('login');
        }

        Review::create([
            'user_id' => Auth::id(),
            'apartment_id' => $this->apartment->id,
            'rating' => $this->rating,
            'comment' => $this->comment,
        ]);

        $this->reset(['rating', 'comment']);
        session()->flash('message', 'Review submitted successfully!');
    }

    public function render()
    {
        return view('livewire.guest.apartment-reviews', [
            'reviews' => $this->apartment->reviews()->latest()->get(),
        ]);
    }
}
