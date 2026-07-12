<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        @if(isset($email_verified) && $email_verified)
            {{ __('Silakan masukkan password baru Anda dan konfirmasikan di bawah ini.') }}
        @else
            {{ __('Lupa password? Masukkan alamat email Anda untuk melanjutkan ke penggantian password baru.') }}
        @endif
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        @if(isset($email_verified) && $email_verified)
            <!-- Email Address (Read-only) -->
            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full bg-gray-100 cursor-not-allowed text-gray-500" type="email" name="email" :value="$email" readonly required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-input-label for="password" :value="__('Password Baru')" />
                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" autofocus />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div class="mt-4">
                <x-input-label for="password_confirmation" :value="__('Konfirmasi Password Baru')" />
                <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-primary-button>
                    {{ __('Perbarui Password') }}
                </x-primary-button>
            </div>
        @else
            <!-- Email Address -->
            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 me-4" href="{{ route('login') }}">
                    {{ __('Kembali ke Login') }}
                </a>
                <x-primary-button>
                    {{ __('Cek Email') }}
                </x-primary-button>
            </div>
        @endif
    </form>
</x-guest-layout>
