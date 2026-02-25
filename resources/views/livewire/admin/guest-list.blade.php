<div class="p-6 space-y-8">
    <div class="flex items-center justify-between">
        <h2 class="text-3xl font-bold text-[#0D1F3F] dark:text-white">Guest List</h2>
        <div class="w-1/3">
            <input 
                type="text" 
                wire:model.live.debounce.300ms="search" 
                placeholder="Search by name, email, phone, passport or room..." 
                class="w-full rounded-xl border border-gray-200 dark:border-[#2a3c5e] px-4 py-2 bg-white dark:bg-[#0b1a36]"
            >
        </div>
    </div>

    <div class="bg-white dark:bg-[#1a2c4e] rounded-2xl shadow-lg border border-gray-100 dark:border-[#2a3c5e] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 dark:bg-[#0b1a36] border-b border-gray-200 dark:border-[#2a3c5e]">
                        <th class="py-4 px-6 font-semibold text-gray-600 dark:text-gray-300">Guest Details</th>
                        <th class="py-4 px-6 font-semibold text-gray-600 dark:text-gray-300">Passport Data</th>
                        <th class="py-4 px-6 font-semibold text-gray-600 dark:text-gray-300">Latest Booking</th>
                        <th class="py-4 px-6 font-semibold text-gray-600 dark:text-gray-300">Arrival / Departure</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-[#2a3c5e]">
                    @forelse($guests as $guest)
                        @php
                            $latestBooking = $guest->bookings->first();
                        @endphp
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-[#0f2042] transition-colors">
                            <td class="py-4 px-6">
                                <div class="font-bold text-[#0D1F3F] dark:text-white">{{ $guest->name }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $guest->email }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400 font-mono">{{ $latestBooking?->phone ?? 'N/A' }}</div>
                            </td>
                            <td class="py-4 px-6 font-mono text-sm">
                                {{ $guest->passport_data ?: 'Not Provided' }}
                            </td>
                            <td class="py-4 px-6">
                                @if($latestBooking)
                                    <div class="font-semibold text-[#D4AF37]">{{ $latestBooking->apartment->title }}</div>
                                    <div class="text-xs text-gray-500 uppercase tracking-widest mt-1">{{ $latestBooking->status }}</div>
                                @else
                                    <span class="text-gray-400 italic">No bookings</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-sm">
                                @if($latestBooking)
                                    <div><span class="text-gray-500 mr-2">In:</span> {{ $latestBooking->start_date->format('M d, Y') }}</div>
                                    <div><span class="text-gray-500 mr-2">Out:</span> <span class="{{ $latestBooking->end_date->isPast() ? 'text-gray-400' : 'font-bold text-emerald-500' }}">{{ $latestBooking->end_date->format('M d, Y') }}</span></div>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center text-gray-500 dark:text-gray-400">
                                No guests found. <a href="{{ route('admin.checkin') }}" class="text-[#D4AF37] hover:underline">Register a guest</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 dark:border-[#2a3c5e]">
            {{ $guests->links() }}
        </div>
    </div>
</div>
