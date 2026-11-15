<x-app-layout>
    <x-slot name="header">
        <h2>Daftar Video</h2>
        <p class="mt-2 text-sm text-[#9A93A8]">
            Pilih video, ajukan akses ke admin, lalu tonton setelah disetujui.
        </p>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="p-4 rounded-lg border border-emerald-400/30 bg-emerald-400/10 text-emerald-300">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="p-4 rounded-lg border border-rose-400/30 bg-rose-400/10 text-rose-300">
                    {{ session('error') }}
                </div>
            @endif

            @if ($items->isEmpty())
                <div class="rounded-xl border border-[#2A2436] bg-[#181421] p-8 text-[#9A93A8]">
                    Belum ada video tersedia. Admin akan menambahkannya segera.
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($items as $item)
                        @php
                            $video = $item['video'];
                            $status = $item['status'];

                            $label = match ($status) {
                                'pending' => 'Menunggu',
                                'rejected' => 'Ditolak',
                                'active' => 'Disetujui',
                                'expired' => 'Kedaluwarsa',
                                default => 'Belum diminta',
                            };

                            $badge = match ($status) {
                                'pending' => 'bg-amber-400/10 text-amber-300 ring-amber-400/30',
                                'rejected' => 'bg-rose-400/10 text-rose-300 ring-rose-400/30',
                                'active' => 'bg-emerald-400/10 text-emerald-300 ring-emerald-400/30',
                                'expired' => 'bg-white/5 text-[#9A93A8] ring-white/10',
                                default => 'bg-white/5 text-[#F2EBDD] ring-white/20',
                            };
                        @endphp

                        <div class="rounded-xl border border-[#2A2436] bg-[#181421] overflow-hidden flex flex-col">
                            {{-- Sampul sementara: huruf pertama judul --}}
                            <div class="relative aspect-video bg-gradient-to-br from-[#2A2436] to-[#181421] flex items-center justify-center">
                                <div class="absolute inset-3 rounded-lg border border-[#C9A961]/25"></div>

                                <span class="font-['Cormorant_Garamond'] text-7xl font-semibold text-[#C9A961]/80 select-none">
                                    {{ strtoupper(mb_substr($video->title, 0, 1)) }}
                                </span>

                                @if ($status === 'active')
                                    <a href="{{ route('customer.videos.watch', $video) }}"
                                       aria-label="Tonton {{ $video->title }}"
                                       class="absolute inset-0 flex items-center justify-center bg-black/30 hover:bg-black/10 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C9A961]">
                                        <span class="w-14 h-14 rounded-full bg-[#C9A961] flex items-center justify-center">
                                            <svg class="w-6 h-6 text-[#0F0D14] ms-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M6.3 2.8A1 1 0 004.8 3.7v12.6a1 1 0 001.5.9l10.5-6.3a1 1 0 000-1.8L6.3 2.8z" />
                                            </svg>
                                        </span>
                                    </a>
                                @endif

                                <span class="absolute top-3 left-3 rounded-full px-3 py-1 text-xs font-medium ring-1 {{ $badge }}">
                                    {{ $label }}
                                </span>
                            </div>

                            <div class="p-5 flex flex-col flex-1">
                                <h3 class="font-['Cormorant_Garamond'] text-2xl font-semibold text-[#F2EBDD] leading-tight">
                                    {{ $video->title }}
                                </h3>
                                <p class="mt-2 text-sm text-[#9A93A8]">
                                    {{ \Illuminate\Support\Str::limit($video->description, 90) }}
                                </p>

                                @if ($status === 'active')
                                    <p class="mt-3 text-xs text-emerald-300">
                                        Akses sampai {{ $item['expires_at']->format('d M Y H:i') }}
                                    </p>
                                @elseif ($status === 'expired')
                                    <p class="mt-3 text-xs text-[#9A93A8]">
                                        Akses berakhir {{ $item['expires_at']?->format('d M Y H:i') }}
                                    </p>
                                @endif

                                <div class="mt-auto pt-5">
                                    @if ($status === 'active')
                                        <a href="{{ route('customer.videos.watch', $video) }}"
                                           class="inline-flex items-center rounded-md bg-[#C9A961] px-4 py-2 text-sm font-semibold text-[#0F0D14] hover:bg-[#D8BA73] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C9A961] focus-visible:ring-offset-2 focus-visible:ring-offset-[#181421]">
                                            Tonton
                                        </a>
                                    @elseif (in_array($status, ['none', 'rejected', 'expired']))
                                        <form method="POST" action="{{ route('customer.videos.request', $video) }}">
                                            @csrf
                                            <button type="submit"
                                                    class="inline-flex items-center rounded-md border border-[#C9A961] px-4 py-2 text-sm font-semibold text-[#C9A961] hover:bg-[#C9A961] hover:text-[#0F0D14] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C9A961] focus-visible:ring-offset-2 focus-visible:ring-offset-[#181421]">
                                                {{ $status === 'none' ? 'Request akses' : 'Request ulang' }}
                                            </button>
                                        </form>
                                    @elseif ($status === 'pending')
                                        <span class="text-sm text-[#9A93A8]">Menunggu persetujuan admin</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>