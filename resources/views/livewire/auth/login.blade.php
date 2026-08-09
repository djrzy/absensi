<div class="min-h-screen h-dvh bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8 items-center px-4">
    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
        <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Login</h2>
        <p class="mt-1 text-xs text-gray-500">Silakan masuk menggunakan akun Anda</p>
    </div>

    <div class="mt-6 w-full sm:mx-auto sm:max-w-md">
        <div class="bg-white py-8 px-6 shadow-xs border border-gray-100 rounded-2xl sm:px-10">
            <form wire:submit.prevent="login" class="space-y-4">
                <!-- Field Email -->
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Alamat Email</label>
                    <input type="email" wire:model="email" placeholder="nama@sekolah.sch.id"
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm focus:bg-white focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all">
                    @error('email')
                        <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Field Password dengan Alpine Toggle View -->
                <div x-data="{ showPassword: false }">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Password</label>
                    <div class="relative">
                        <input :type="showPassword ? 'text' : 'password'" wire:model="password" placeholder="••••••••"
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl pl-3 pr-10 py-2 text-sm focus:bg-white focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all">
                        <button type="button" @click="showPassword = !showPassword"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-xs text-gray-400 hover:text-gray-600 cursor-pointer select-none">
                            <span x-text="showPassword ? '🙈' : '👁️'"></span>
                        </button>
                    </div>
                    @error('password')
                        <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Tombol Submit Login -->
                <button type="submit" wire:loading.attr="disabled"
                    class="w-full mt-2 py-2.5 bg-gray-900 hover:bg-gray-800 disabled:opacity-50 text-white text-xs font-bold rounded-xl transition-all cursor-pointer shadow-xs flex items-center justify-center gap-2">
                    <span wire:loading.remove wire:target="login">Masuk ke Sistem</span>
                    <span wire:loading wire:target="login" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        Memverifikasi...
                    </span>
                </button>
            </form>
        </div>
    </div>
</div>
