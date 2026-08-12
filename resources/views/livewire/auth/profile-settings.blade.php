<div class="p-4 sm:p-6 max-w-3xl mx-auto space-y-6">

    <!-- HEADER TITLE -->
    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-xs flex justify-between items-center">
        <div>
            <h1 class="text-lg sm:text-xl font-bold text-gray-900">Pengaturan Akun & Profil</h1>
            <p class="text-xs text-gray-500 mt-0.5">Perbarui nama, username, email, dan kata sandi login Anda.</p>
        </div>
        <a href="/"
            class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-xl transition-all">
            ← Kembali ke Dashboard
        </a>
    </div>

    <!-- FLASH SUCCESS NOTIFICATION -->
    @if (session()->has('success'))
        <div
            class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-medium flex items-center justify-between">
            <span>✅ {{ session('success') }}</span>
        </div>
    @endif

    <!-- FORM PENGATURAN PROFIL DENGAN VALIDASI INSTANT ALPINE.JS -->
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-xs space-y-5" x-data="{
        pass: @entangle('password'),
        passConfirm: @entangle('password_confirmation'),
        showPass: false,
        showPassConfirm: false,
        get isPasswordMismatch() {
            return this.pass.length > 0 && this.pass !== this.passConfirm;
        }
    }">

        <form wire:submit.prevent="updateProfile" class="space-y-4">

            <!-- NAMA LENGKAP -->
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Nama Lengkap</label>
                <input type="text" wire:model="name" placeholder="Nama Lengkap Anda"
                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs focus:bg-white focus:ring-2 focus:ring-indigo-100 focus:border-indigo-400 outline-none transition-all">
                @error('name')
                    <span class="text-[11px] text-rose-600 block mt-1">⚠️ {{ $message }}</span>
                @enderror
            </div>

            <!-- USERNAME -->
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">
                    Username Login
                    <span class="text-gray-400 font-normal lowercase">(tanpa spasi, misal: budi_santoso)</span>
                </label>
                <input type="text" wire:model="username" placeholder="username_anda"
                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs font-mono focus:bg-white focus:ring-2 focus:ring-indigo-100 focus:border-indigo-400 outline-none transition-all">
                @error('username')
                    <span class="text-[11px] text-rose-600 block mt-1">⚠️ {{ $message }}</span>
                @enderror
            </div>

            <!-- EMAIL -->
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Alamat Email</label>
                <input type="email" wire:model="email" placeholder="email@sekolah.sch.id"
                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs focus:bg-white focus:ring-2 focus:ring-indigo-100 focus:border-indigo-400 outline-none transition-all">
                @error('email')
                    <span class="text-[11px] text-rose-600 block mt-1">⚠️ {{ $message }}</span>
                @enderror
            </div>

            <!-- GRID FIELD PASSWORD BARU & KONFIRMASI -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-gray-100">

                <!-- FIELD PASSWORD BARU -->
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">
                        Password Baru
                    </label>
                    <div class="relative">
                        <input :type="showPass ? 'text' : 'password'" wire:model.live="password" placeholder="••••••••"
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl pl-3.5 pr-10 py-2.5 text-xs focus:bg-white focus:ring-2 focus:ring-indigo-100 focus:border-indigo-400 outline-none transition-all">
                        <button type="button" @click="showPass = !showPass"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-xs text-gray-400 hover:text-gray-600 cursor-pointer select-none">
                            <span x-text="showPass ? '🙈' : '👁️'"></span>
                        </button>
                    </div>
                    @error('password')
                        <span class="text-[11px] text-rose-600 block mt-1">⚠️ {{ $message }}</span>
                    @enderror
                </div>

                <!-- FIELD KONFIRMASI PASSWORD BARU -->
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">
                        Ulangi Password Baru
                    </label>
                    <div class="relative">
                        <input :type="showPassConfirm ? 'text' : 'password'" wire:model.live="password_confirmation"
                            placeholder="••••••••" :disabled="!pass || pass.length === 0"
                            :class="{
                                'opacity-50 cursor-not-allowed bg-gray-100': !pass || pass.length ===
                                    0,
                                'border-rose-300 focus:ring-rose-100 focus:border-rose-400': isPasswordMismatch
                            }"
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl pl-3.5 pr-10 py-2.5 text-xs focus:bg-white focus:ring-2 focus:ring-indigo-100 focus:border-indigo-400 outline-none transition-all">
                        <button type="button" @click="showPassConfirm = !showPassConfirm"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-xs text-gray-400 hover:text-gray-600 cursor-pointer select-none">
                            <span x-text="showPassConfirm ? '🙈' : '👁️'"></span>
                        </button>
                    </div>

                    <!-- PESAN WARNING MATCHING SEKETIKA -->
                    <template x-if="isPasswordMismatch">
                        <span class="text-[11px] text-rose-600 font-semibold block mt-1">
                            ⚠️ Konfirmasi password tidak cocok!
                        </span>
                    </template>
                    <template x-if="pass && pass.length > 0 && pass === passConfirm">
                        <span class="text-[11px] text-emerald-600 font-semibold block mt-1">
                            ✅ Password cocok.
                        </span>
                    </template>
                </div>

            </div>

            <!-- SUBMIT BUTTON -->
            <div class="pt-2 flex justify-between items-center">
                <p class="text-xs italic">*kosongkan field password jika tidak ingin mengubah password</p>
                <button type="submit" wire:loading.attr="disabled" :disabled="isPasswordMismatch"
                    :class="{
                        'opacity-50 cursor-not-allowed bg-gray-400': isPasswordMismatch,
                        'bg-gray-900 hover:bg-gray-800 cursor-pointer':
                            !isPasswordMismatch
                    }"
                    class="w-full sm:w-auto px-6 py-2.5 text-white text-xs font-bold rounded-xl transition-all shadow-xs inline-flex items-center justify-center gap-2">
                    <span wire:loading.remove wire:target="updateProfile">💾 Simpan Perubahan</span>
                    <span wire:loading.flex wire:target="updateProfile" class="items-center gap-2">
                        <svg class="animate-spin h-3.5 w-3.5 text-white shrink-0" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <span>Menyimpan...</span>
                    </span>
                </button>
            </div>

        </form>
    </div>

</div>
