<div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8 items-center">
    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
        <h2 class="text-2xl font-bold text-gray-900 tracking-tight">EduAttend Login</h2>
        <p class="mt-1 text-xs text-gray-500">Silahkan masuk menggunakan akun sekolah Anda</p>
    </div>

    <div class="mt-6 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow-xs border border-gray-100 sm:rounded-2xl sm:px-10">
            <form wire:submit.prevent="login" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Alamat Email</label>
                    <input type="email" wire:model="email" placeholder="nama@school.id"
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm focus:bg-white focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all">
                    @error('email')
                        <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Password</label>
                    <input type="password" wire:model="password" placeholder="••••••••"
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm focus:bg-white focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all">
                </div>

                <button type="submit"
                    class="w-full mt-2 py-2.5 bg-gray-900 hover:bg-gray-800 text-white text-xs font-bold rounded-xl transition-all cursor-pointer shadow-xs text-center">
                    Masuk ke Sistem
                </button>
            </form>
        </div>
    </div>
</div>
