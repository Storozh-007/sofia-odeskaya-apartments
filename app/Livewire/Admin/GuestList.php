<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class GuestList extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        $query = User::whereHas('bookings')
            ->with(['bookings' => function($q) {
                $q->orderBy('start_date', 'desc')->with('apartment');
            }]);

        if ($this->search) {
            $searchTerm = '%' . $this->search . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                  ->orWhere('email', 'like', $searchTerm)
                  ->orWhere('passport_data', 'like', $searchTerm)
                  ->orWhereHas('bookings', function($b) use ($searchTerm) {
                      $b->where('phone', 'like', $searchTerm)
                        ->orWhereHas('apartment', function($a) use ($searchTerm) {
                            $a->where('title', 'like', $searchTerm);
                        });
                  });
            });
        }

        return view('livewire.admin.guest-list', [
            'guests' => $query->paginate(12)
        ]);
    }
}
