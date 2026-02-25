<?php

namespace App\Livewire\Admin;

use App\Models\Apartment;
use App\Models\ApartmentImage;
use Livewire\Attributes\Layout;
use Livewire\Component;

class ApartmentGrid extends Component
{
    public array $form = [
        'title' => '',
        'description' => '',
        'image_url' => '',
        'price' => null,
        'capacity' => 2,
        'status' => 'free',
        'extra_images' => '',
    ];

    protected array $rules = [
        'form.title' => 'required|string|min:3|max:150',
        'form.description' => 'required|string|min:10|max:500',
        'form.image_url' => 'nullable|url',
        'form.price' => 'required|numeric|min:1',
        'form.capacity' => 'required|integer|min:1|max:20',
        'form.status' => 'required|in:free,taken,cleaning',
        'form.extra_images' => 'nullable|string',
    ];

    public function createApartment()
    {
        $this->validate();

        $apartment = Apartment::create([
            'title' => $this->form['title'],
            'description' => $this->form['description'],
            'image_url' => $this->form['image_url'] ?: null,
            'price' => (int) round($this->form['price'] * 100),
            'capacity' => $this->form['capacity'],
            'status' => $this->form['status'],
        ]);

        // Attach optional extra images (comma separated)
        if (!empty($this->form['extra_images'])) {
            $urls = array_filter(array_map('trim', explode(',', $this->form['extra_images'])));
            foreach ($urls as $index => $url) {
                if (filter_var($url, FILTER_VALIDATE_URL)) {
                    ApartmentImage::create([
                        'apartment_id' => $apartment->id,
                        'image_url' => $url,
                        'sort_order' => $index + 2,
                    ]);
                }
            }
        }

        session()->flash('created', 'Hotel created and added to catalog.');
        $this->reset('form');
        $this->form['status'] = 'free';
        $this->form['capacity'] = 2;
    }

    public function toggleStatus(Apartment $apartment)
    {
        $newStatus = match($apartment->status) {
            'free' => 'taken',
            'taken' => 'cleaning',
            'cleaning' => 'free',
            default => 'free',
        };

        $apartment->update(['status' => $newStatus]);
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('livewire.admin.apartment-grid', [
            'apartments' => Apartment::all(),
        ]);
    }
}
