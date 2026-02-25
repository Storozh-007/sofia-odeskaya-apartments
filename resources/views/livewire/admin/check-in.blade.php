<div class="p-6 max-w-5xl mx-auto space-y-8">
    <div class="flex items-center justify-between">
        <h2 class="text-3xl font-bold text-[#0D1F3F] dark:text-white">Check-in Registration</h2>
        <div class="flex space-x-2">
            <span class="px-3 py-1 rounded-full text-xs font-bold {{ $step >= 1 ? 'bg-[#D4AF37] text-white' : 'bg-gray-200 text-gray-500' }}">1. Dates</span>
            <span class="px-3 py-1 rounded-full text-xs font-bold {{ $step >= 2 ? 'bg-[#D4AF37] text-white' : 'bg-gray-200 text-gray-500' }}">2. Room</span>
            <span class="px-3 py-1 rounded-full text-xs font-bold {{ $step >= 3 ? 'bg-[#D4AF37] text-white' : 'bg-gray-200 text-gray-500' }}">3. Guest</span>
            <span class="px-3 py-1 rounded-full text-xs font-bold {{ $step >= 4 ? 'bg-[#D4AF37] text-white' : 'bg-gray-200 text-gray-500' }}">4. Receipt</span>
        </div>
    </div>

    @if($step === 1)
        <!-- Step 1: Dates & Requirements -->
        <div class="bg-white dark:bg-[#1a2c4e] rounded-2xl shadow-lg border border-gray-100 dark:border-[#2a3c5e] p-8">
            <h3 class="text-xl font-bold text-[#0D1F3F] dark:text-white mb-6">Select Dates & Requirements</h3>
            <form wire:submit.prevent="searchRooms" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="text-sm font-semibold text-gray-600 dark:text-gray-300">Arrival Date</label>
                    <input type="date" wire:model.defer="start_date" class="w-full mt-2 rounded-xl border border-gray-200 dark:border-[#2a3c5e] px-4 py-3 bg-white dark:bg-[#0b1a36]">
                    @error('start_date') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="text-sm font-semibold text-gray-600 dark:text-gray-300">Departure Date</label>
                    <input type="date" wire:model.defer="end_date" class="w-full mt-2 rounded-xl border border-gray-200 dark:border-[#2a3c5e] px-4 py-3 bg-white dark:bg-[#0b1a36]">
                    @error('end_date') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="text-sm font-semibold text-gray-600 dark:text-gray-300">Number of Guests</label>
                    <input type="number" wire:model.defer="capacity" class="w-full mt-2 rounded-xl border border-gray-200 dark:border-[#2a3c5e] px-4 py-3 bg-white dark:bg-[#0b1a36]" min="1">
                    @error('capacity') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                </div>
                <div class="col-span-full flex justify-end">
                    <button type="submit" class="px-6 py-3 bg-[#0D1F3F] text-white font-bold rounded-xl hover:bg-[#1a3a6e] transition-all">Search Available Rooms</button>
                </div>
            </form>
        </div>
    @endif

    @if($step === 2)
        <!-- Step 2: Select Room -->
        <div class="bg-white dark:bg-[#1a2c4e] rounded-2xl shadow-lg border border-gray-100 dark:border-[#2a3c5e] p-8">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-[#0D1F3F] dark:text-white">Available Rooms ({{ count($availableRooms) }})</h3>
                <button wire:click="$set('step', 1)" class="text-sm text-gray-500 hover:text-black dark:text-gray-400 dark:hover:text-white underline">Back to Dates</button>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @forelse($availableRooms as $room)
                    <div class="border border-gray-100 dark:border-[#2a3c5e] rounded-xl p-4 flex gap-4 hover:border-[#D4AF37] cursor-pointer transition-colors" wire:click="selectRoom({{ $room->id }})">
                        <div class="w-24 h-24 rounded-lg bg-gray-200 overflow-hidden flex-shrink-0">
                            <img src="{{ $room->image_url ?? 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=300&q=80' }}" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-grow flex flex-col justify-between">
                            <div>
                                <h4 class="font-bold text-lg text-[#0D1F3F] dark:text-white">{{ $room->title }}</h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $room->room_class ?: 'Standard' }} • {{ $room->capacity }} Guests</p>
                            </div>
                            <div class="text-right">
                                <span class="font-bold text-[#D4AF37]">${{ number_format($room->price / 100, 2) }}</span><span class="text-xs text-gray-500">/night</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-12 text-center text-gray-500">
                        No rooms available for these dates and capacity.
                    </div>
                @endforelse
            </div>
        </div>
    @endif

    @if($step === 3)
        <!-- Step 3: Guest Details -->
        <div class="bg-white dark:bg-[#1a2c4e] rounded-2xl shadow-lg border border-gray-100 dark:border-[#2a3c5e] p-8">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-[#0D1F3F] dark:text-white">Guest Information</h3>
                <button wire:click="$set('step', 2)" class="text-sm text-gray-500 hover:text-black dark:text-gray-400 dark:hover:text-white underline">Back to Rooms</button>
            </div>

            <div class="flex mb-6 space-x-4">
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="radio" wire:model.live="guestType" value="new" class="text-[#D4AF37] focus:ring-[#D4AF37]">
                    <span class="text-gray-700 dark:text-gray-300">New Guest</span>
                </label>
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="radio" wire:model.live="guestType" value="existing" class="text-[#D4AF37] focus:ring-[#D4AF37]">
                    <span class="text-gray-700 dark:text-gray-300">Existing Guest</span>
                </label>
            </div>

            <form wire:submit.prevent="confirmCheckIn" class="space-y-6">
                @if($guestType === 'existing')
                    <div>
                        <label class="text-sm font-semibold text-gray-600 dark:text-gray-300">Select Guest</label>
                        <select wire:model.defer="existingUserId" class="w-full mt-2 rounded-xl border border-gray-200 dark:border-[#2a3c5e] px-4 py-3 bg-white dark:bg-[#0b1a36]">
                            <option value="">-- Choose a guest --</option>
                            @foreach($allUsers as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }}) {{ $user->passport_data ? '- Passport: ' . $user->passport_data : '' }}</option>
                            @endforeach
                        </select>
                        @error('existingUserId') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-sm font-semibold text-gray-600 dark:text-gray-300">Full Name</label>
                            <input type="text" wire:model.defer="guestForm.name" class="w-full mt-2 rounded-xl border border-gray-200 dark:border-[#2a3c5e] px-4 py-3 bg-white dark:bg-[#0b1a36]">
                            @error('guestForm.name') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="text-sm font-semibold text-gray-600 dark:text-gray-300">Email (for account)</label>
                            <input type="email" wire:model.defer="guestForm.email" class="w-full mt-2 rounded-xl border border-gray-200 dark:border-[#2a3c5e] px-4 py-3 bg-white dark:bg-[#0b1a36]">
                            @error('guestForm.email') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="text-sm font-semibold text-gray-600 dark:text-gray-300">Phone Number</label>
                        <input type="text" wire:model.defer="guestForm.phone" class="w-full mt-2 rounded-xl border border-gray-200 dark:border-[#2a3c5e] px-4 py-3 bg-white dark:bg-[#0b1a36]">
                        @error('guestForm.phone') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-600 dark:text-gray-300">Passport Data (Series, Number, Date)</label>
                        <input type="text" wire:model.defer="guestForm.passport_data" class="w-full mt-2 rounded-xl border border-gray-200 dark:border-[#2a3c5e] px-4 py-3 bg-white dark:bg-[#0b1a36]" placeholder="AA123456, 12.05.2010">
                        @error('guestForm.passport_data') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="flex justify-end pt-4 border-t border-gray-100 dark:border-[#2a3c5e]">
                    <button type="submit" class="px-8 py-3 bg-[#D4AF37] text-[#0D1F3F] font-bold rounded-xl hover:bg-[#b39025] transition-all">Complete Check-in</button>
                </div>
            </form>
        </div>
    @endif

    @if($step === 4)
        <!-- Step 4: Receipt -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden" id="printable-receipt">
            <div class="bg-[#0D1F3F] p-8 text-white flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold tracking-wider text-[#D4AF37]">SOBOOKING</h2>
                    <p class="text-sm opacity-80 mt-1">GUEST REGISTRATION RECEIPT</p>
                </div>
                <div class="text-right">
                    <p class="text-sm font-mono opacity-80">Receipt #{{ str_pad($receipt['booking_id'], 6, '0', STR_PAD_LEFT) }}</p>
                    <p class="text-sm opacity-80">{{ \Carbon\Carbon::parse($receipt['date_issued'])->format('M d, Y h:i A') }}</p>
                </div>
            </div>
            
            <div class="p-8 text-gray-800">
                <div class="grid grid-cols-2 gap-8 mb-8 pb-8 border-b border-gray-200">
                    <div>
                        <p class="text-sm text-gray-500 font-semibold mb-1">GUEST DETAILS</p>
                        <p class="font-bold text-lg">{{ $receipt['guest_name'] }}</p>
                        <p class="text-sm mt-1">Passport: {{ $receipt['passport'] ?: 'Not specified' }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-500 font-semibold mb-1">STAY DATES</p>
                        <p class="font-bold">{{ \Carbon\Carbon::parse($receipt['arrival'])->format('M d, Y') }} - {{ \Carbon\Carbon::parse($receipt['departure'])->format('M d, Y') }}</p>
                        <p class="text-sm mt-1">Duration: {{ $receipt['nights'] }} Nights</p>
                    </div>
                </div>
                
                <table class="w-full text-left mb-8">
                    <thead>
                        <tr class="border-b border-gray-200 text-sm text-gray-500">
                            <th class="py-2">DESCRIPTION</th>
                            <th class="py-2">CLASS</th>
                            <th class="py-2 text-right">PRICE/NIGHT</th>
                            <th class="py-2 text-right">TOTAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="py-4 font-bold">{{ $receipt['room_title'] }}</td>
                            <td class="py-4">{{ $receipt['room_class'] }}</td>
                            <td class="py-4 text-right">${{ number_format($receipt['price_per_night'], 2) }}</td>
                            <td class="py-4 text-right font-bold">${{ number_format($receipt['total'], 2) }}</td>
                        </tr>
                    </tbody>
                </table>
                
                <div class="flex justify-end">
                    <div class="w-1/3 bg-gray-50 p-4 rounded-xl border border-gray-200">
                        <div class="flex justify-between font-bold text-lg">
                            <span>Total Rendered</span>
                            <span class="text-[#D4AF37]">${{ number_format($receipt['total'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-50 p-6 flex justify-between items-center border-t border-gray-200">
                <button wire:click="resetForm" class="text-[#0D1F3F] font-bold hover:underline">Start New Check-in</button>
                <button onclick="window.print()" class="px-6 py-2 bg-[#0D1F3F] text-white font-bold rounded-lg hover:bg-[#1a3a6e] transition-colors flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Print Receipt
                </button>
            </div>
        </div>
        
        <style>
            @media print {
                body * { visibility: hidden; }
                #printable-receipt, #printable-receipt * { visibility: visible; }
                #printable-receipt { position: absolute; left: 0; top: 0; width: 100%; border: none; box-shadow: none; }
                button { display: none !important; }
            }
        </style>
    @endif
</div>
