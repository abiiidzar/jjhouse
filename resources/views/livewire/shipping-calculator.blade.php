<div class="mb-10 border-t pt-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-4">
        <h2 class="font-medium text-gray-800">Alamat & Pengiriman</h2>
    </div>

    <!-- Alamat Input -->
    <div class="grid grid-cols-2 gap-4 mb-6">
        <!-- Province -->
        <div class="relative" x-data="{ open: false }" @click.away="open = false">
            <label class="text-[11px] uppercase tracking-wider text-gray-500 block mb-1 font-semibold">Provinsi</label>
            <input type="text"
                   wire:model.live="province"
                   @focus="open = true"
                   placeholder="Cari provinsi..."
                   class="w-full border border-gray-300 rounded-lg p-3 text-sm transition-all focus:ring-2 focus:ring-gray-100"
                   autocomplete="off">

            @if(count($filteredProvinces) > 0)
                <div x-show="open" x-cloak class="absolute z-50 w-full mt-1 bg-white border rounded-lg shadow-lg max-h-48 overflow-y-auto">
                    @foreach($filteredProvinces as $p)
                        <div wire:click="selectProvince('{{ $p['province'] }}', '{{ $p['province_id'] }}')"
                             @click="open = false"
                             class="px-3 py-2 text-sm hover:bg-gray-100 cursor-pointer transition">
                            {{ $p['province'] }}
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- City -->
        <div class="relative" x-data="{ open: false }" @click.away="open = false">
            <label class="text-[11px] uppercase tracking-wider text-gray-500 block mb-1 font-semibold">Kota</label>
            <input type="text"
                   wire:model.live="city"
                   @focus="open = true"
                   placeholder="Cari kota..."
                   class="w-full border border-gray-300 rounded-lg p-3 text-sm transition-all focus:ring-2 focus:ring-gray-100"
                   autocomplete="off">

            @if(count($filteredCities) > 0)
                <div x-show="open" x-cloak class="absolute z-50 w-full mt-1 bg-white border rounded-lg shadow-lg max-h-48 overflow-y-auto">
                    @foreach($filteredCities as $c)
                        <div wire:click="selectCity('{{ $c['city_name'] }}', '{{ $c['city_id'] }}')"
                             @click="open = false"
                             class="px-3 py-2 text-sm hover:bg-gray-100 cursor-pointer transition flex justify-between items-center">
                            <span>{{ $c['city_name'] }}</span>
                            @if($c['postal_code'] ?? '')
                            <span class="text-xs text-gray-400">{{ $c['postal_code'] }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Postal Code -->
        <div class="col-span-2">
            <label class="text-[11px] uppercase tracking-wider text-gray-500 block mb-1 font-semibold">Kode Pos</label>
            <input type="text" wire:model="postal_code"
                   placeholder="e.g., 25123"
                   class="w-full border border-gray-300 rounded-lg p-3 text-sm transition-all focus:ring-2 focus:ring-gray-100">
        </div>
    </div>

    <!-- Tombol Hitung -->
    <button type="button"
            wire:click="calculateShipping"
            wire:loading.attr="disabled"
            class="w-full bg-blue-600 text-white py-3 rounded-lg text-sm font-medium hover:bg-blue-700 transition-all shadow-sm active:scale-[0.98] disabled:bg-gray-400 disabled:cursor-not-allowed">
        <span wire:loading.remove wire:target="calculateShipping"> Hitung Ongkir</span>
        <span wire:loading wire:target="calculateShipping" class="flex items-center justify-center gap-2">
            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Menghitung Ongkir...
        </span>
    </button>

    <!-- Loading Overlay saat menghitung ongkir (muncul otomatis via wire:loading) -->
    {{-- <div wire:loading wire:target="calculateShipping"
         class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-4 flex items-center gap-3">
        <div class="relative flex items-center justify-center">
            <div class="w-8 h-8 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin"></div>
        </div>
        <div class="flex-1">
            <p class="text-sm font-semibold text-blue-800">Menghitung ongkir...</p>
            <p class="text-xs text-blue-600">Mohon tunggu sebentar, kami sedang memproses tarif pengiriman Anda.</p>
        </div>
    </div> --}}

    <!-- Notice (informational, e.g. local estimate) -->
    @if($notice)
        <div class="mt-4 bg-blue-50 border border-blue-200 text-blue-700 p-3 rounded-lg text-xs flex items-center gap-2">
            <span></span> {{ $notice }}
        </div>
    @endif

    <!-- Error -->
    @if($error)
        <div class="mt-4 bg-red-50 border border-red-200 text-red-700 p-3 rounded-lg text-xs flex items-center gap-2">
            <span>⚠️</span> {{ $error }}
        </div>
    @endif

    <!-- Shipping Options -->
    @if(count($shippingOptions) > 0)
        <div class="mt-8">
            <h3 class="font-semibold text-sm mb-4 text-gray-700">Pilihan Pengiriman</h3>
            <div class="space-y-3">
                @foreach($shippingOptions as $option)
                    <label class="relative border rounded-xl p-4 cursor-pointer hover:border-blue-500 transition-all block
                        {{ $selectedService === $option['service'] && $selectedCourier === $option['courier'] ? 'bg-blue-50 border-blue-500 ring-1 ring-blue-500' : 'bg-white border-gray-200' }}">

                        <div class="flex items-start gap-3">
                            <input type="radio" name="shipping_method"
                                   wire:click="selectShipping('{{ $option['courier'] }}', '{{ $option['service'] }}', {{ $option['cost'] }}, '{{ $option['etd'] }}')"
                                   {{ $selectedService === $option['service'] && $selectedCourier === $option['courier'] ? 'checked' : '' }}
                                   class="mt-1 text-blue-600 focus:ring-blue-500">

                            <div class="flex-1 text-sm">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <span class="font-bold text-gray-900">{{ $option['courier'] }}</span>
                                        <span class="mx-1 text-gray-300">|</span>
                                        <span class="text-gray-600">{{ $option['service'] }}</span>
                                    </div>
                                    <span class="font-bold text-blue-700">Rp {{ number_format($option['cost'], 0, ',', '.') }}</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">{{ $option['description'] }}</p>
                                <div class="flex items-center gap-1 mt-2 text-[11px] text-gray-400 font-medium">
                                    <span>🕒 Estimasi: {{ $option['etd'] }} Hari</span>
                                </div>
                            </div>
                        </div>
                    </label>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Selected Summary -->
    {{-- @if($selectedCourier && $shippingCost > 0)
        <div class="mt-6 bg-green-50 border border-green-100 p-4 rounded-xl">
            <div class="flex items-center gap-2 mb-1">
                <span class="bg-green-500 text-white text-[10px] px-2 py-0.5 rounded-full font-bold">SELECTED</span>
                <span class="text-sm font-bold text-green-800">{{ $selectedCourier }} - {{ $selectedService }}</span>
            </div>
            <div class="text-xs text-green-700 space-y-0.5">
                <p>Biaya: <span class="font-semibold">Rp {{ number_format($shippingCost, 0, ',', '.') }}</span></p>
                <p>Estimasi Tiba: <span class="font-semibold">{{ $estimatedDays }} Hari</span></p>
            </div>
        </div>
    @endif --}}
</div>
