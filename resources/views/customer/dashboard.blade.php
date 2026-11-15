<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard Customer</h2>
    </x-slot>

    <div class="bg-white p-6 shadow-sm sm:rounded-lg">
            Halo, {{ auth()->user()->name }}. Anda login sebagai <b>customer</b>.
            <div class="mt-4">
                <a href="{{ route('customer.videos.index') }}" class="text-indigo-600 underline">Lihat daftar video</a>
            </div>
        </div>
    </div>
</x-app-layout>