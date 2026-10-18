@props([
    'name',
    'value' => '',
    'label' => null,
    'placeholder' => '812-3456-7890',
    'required' => false,
    'helpText' => null,
])

@php
    $parsed = \App\Support\WhatsAppHelper::parsePhone($value);
    $initialDialCode = $parsed['dial_code'] ?? '+62';
    $initialNationalNumber = $parsed['national_number'] ?? '';
    $countries = \App\Support\WhatsAppHelper::COUNTRIES;
@endphp

<div x-data="{
    countries: {{ json_encode($countries) }},
    selectedDial: {{ json_encode($initialDialCode) }},
    nationalNumber: {{ json_encode($initialNationalNumber) }},
    search: '',
    isOpen: false,
    get selectedCountry() {
        return this.countries.find(c => c.dial_code === this.selectedDial) || this.countries[0];
    },
    get filteredCountries() {
        if (!this.search.trim()) return this.countries;
        const q = this.search.toLowerCase().trim();
        return this.countries.filter(c => 
            c.name.toLowerCase().includes(q) || 
            c.dial_code.includes(q) || 
            c.code.toLowerCase().includes(q)
        );
    },
    toggleDropdown() {
        this.isOpen = !this.isOpen;
        if (this.isOpen) {
            this.search = '';
            this.$nextTick(() => {
                if (this.$refs.searchInput) {
                    this.$refs.searchInput.focus();
                }
            });
        }
    },
    selectCountry(c) {
        this.selectedDial = c.dial_code;
        this.isOpen = false;
        this.search = '';
        this.$nextTick(() => {
            if (this.$refs.phoneInput) {
                this.$refs.phoneInput.focus();
            }
        });
    },
    onInput(val) {
        let clean = val ? val.toString().trim() : '';
        // If pasted with '+' e.g. +6591234567 or +60123456789
        if (clean.startsWith('+')) {
            const sorted = [...this.countries].sort((a, b) => b.dial_code.length - a.dial_code.length);
            for (let c of sorted) {
                if (clean.startsWith(c.dial_code)) {
                    this.selectedDial = c.dial_code;
                    clean = clean.substring(c.dial_code.length);
                    break;
                }
            }
        }
        // If leading zero e.g. 0812...
        if (clean.startsWith('0')) {
            clean = clean.substring(1);
        }
        this.nationalNumber = clean.replace(/[^0-9]/g, '');
    },
    get fullPhoneNumber() {
        if (!this.nationalNumber) return '';
        return this.selectedDial + this.nationalNumber;
    }
}" class="relative">
    @if($label)
        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
            {{ $label }}
        </label>
    @endif

    <!-- Hidden input submitting full international standard e.g. +6281234567890 -->
    <input type="hidden" name="{{ $name }}" :value="fullPhoneNumber">

    <div class="flex rounded-xl overflow-hidden border border-slate-300 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 focus-within:ring-2 focus-within:ring-indigo-500 focus-within:border-transparent transition-all shadow-xs">
        <!-- Country Selector Button -->
        <button type="button" @click="toggleDropdown()"
            class="inline-flex items-center gap-1.5 px-3 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-900 dark:hover:bg-slate-800 border-r border-slate-300 dark:border-slate-800 text-xs font-bold text-slate-800 dark:text-slate-200 transition-colors shrink-0 select-none">
            <span class="text-base leading-none" x-text="selectedCountry.flag"></span>
            <span x-text="selectedCountry.dial_code"></span>
            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        <!-- National Number Input -->
        <input type="text"
            x-ref="phoneInput"
            :value="nationalNumber"
            @input="onInput($event.target.value)"
            placeholder="{{ $placeholder }}"
            {{ $required ? 'required' : '' }}
            class="w-full px-3.5 py-2.5 bg-transparent text-sm text-slate-900 dark:text-white border-0 focus:outline-hidden focus:ring-0">
    </div>

    <!-- Country Dropdown Menu -->
    <div x-show="isOpen"
        x-cloak
        @click.outside="isOpen = false"
        @keydown.escape.window="isOpen = false"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute left-0 mt-1 w-80 max-h-72 overflow-hidden rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-2xl z-50 flex flex-col">
        
        <!-- Search input header -->
        <div class="p-2.5 border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/60">
            <div class="relative">
                <input type="text"
                    x-ref="searchInput"
                    x-model="search"
                    placeholder="Ketik nama negara / kode (+62, +65)..."
                    class="w-full pl-8 pr-3 py-1.5 rounded-lg bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                <span class="absolute left-2.5 top-2 text-slate-400 text-xs">🔍</span>
            </div>
        </div>

        <!-- Country list -->
        <div class="overflow-y-auto max-h-56 py-1 divide-y divide-slate-100 dark:divide-slate-800/40">
            <template x-for="c in filteredCountries" :key="c.code + c.dial_code">
                <button type="button" @click="selectCountry(c)"
                    :class="{'bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 font-bold': c.dial_code === selectedDial}"
                    class="w-full px-3.5 py-2 text-left text-xs flex items-center justify-between hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                    <span class="flex items-center gap-2.5 truncate">
                        <span class="text-base" x-text="c.flag"></span>
                        <span class="truncate text-slate-800 dark:text-slate-200" x-text="c.name"></span>
                        <span class="text-[10px] text-slate-400 uppercase font-mono" x-text="'(' + c.code + ')'"></span>
                    </span>
                    <span class="font-mono text-slate-600 dark:text-slate-300 text-xs shrink-0 ml-2" x-text="c.dial_code"></span>
                </button>
            </template>
            <div x-show="filteredCountries.length === 0" class="px-4 py-6 text-center text-xs text-slate-400">
                Negara tidak ditemukan
            </div>
        </div>
    </div>

    @if($helpText)
        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">{{ $helpText }}</p>
    @endif
    @error($name) <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
</div>

