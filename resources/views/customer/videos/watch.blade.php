<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $video->title }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg space-y-4">
                <video id="player" controls controlsList="nodownload"
                       class="w-full rounded-lg bg-black"
                       src="{{ route('customer.videos.stream', $video) }}"></video>

                <p class="text-gray-600">{{ $video->description }}</p>

                <p class="text-sm text-gray-700">
                    Akses berlaku sampai <b>{{ $expiresAt->format('d M Y H:i') }}</b>.
                    Sisa waktu: <b id="countdown"></b>
                </p>

                <p id="expired-message" class="hidden p-4 rounded-lg bg-red-100 text-red-800">
                    Waktu akses Anda sudah habis.
                    <a href="{{ route('customer.videos.index') }}" class="underline">Kembali ke daftar video</a>
                    untuk request akses ulang.
                </p>

                <a href="{{ route('customer.videos.index') }}" class="text-indigo-600 underline text-sm">&larr; Kembali ke daftar video</a>
            </div>
        </div>
    </div>

    <script>
        let remaining = {{ $remaining }};
        const player = document.getElementById('player');
        const countdown = document.getElementById('countdown');
        const expiredMessage = document.getElementById('expired-message');

        function format(seconds) {
            const h = Math.floor(seconds / 3600);
            const m = Math.floor((seconds % 3600) / 60);
            const s = seconds % 60;
            return [h, m, s].map(n => String(n).padStart(2, '0')).join(':');
        }

        function tick() {
            countdown.textContent = format(remaining);

            if (remaining <= 0) {
                player.pause();
                player.removeAttribute('src');
                player.load();
                expiredMessage.classList.remove('hidden');
                clearInterval(timer);
                return;
            }

            remaining--;
        }

        tick();
        const timer = setInterval(tick, 1000);
    </script>
</x-app-layout>