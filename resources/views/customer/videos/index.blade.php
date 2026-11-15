<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Daftar Video</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if (session('success'))
                <div class="p-4 rounded-lg bg-green-100 text-green-800">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="p-4 rounded-lg bg-red-100 text-red-800">{{ session('error') }}</div>
            @endif

            @forelse ($items as $item)
                <div class="bg-white p-6 shadow-sm sm:rounded-lg flex items-center justify-between gap-4">
                    <div>
                        <h3 class="font-semibold text-lg text-gray-800">{{ $item['video']->title }}</h3>
                        <p class="text-sm text-gray-600">{{ $item['video']->description }}</p>

                        @if ($item['status'] === 'active')
                            <p class="text-sm text-green-700 mt-1">
                                Akses berlaku sampai {{ $item['expires_at']->format('d M Y H:i') }}
                            </p>
                        @elseif ($item['status'] === 'expired')
                            <p class="text-sm text-gray-500 mt-1">
                                Akses berakhir pada {{ $item['expires_at']?->format('d M Y H:i') }}
                            </p>
                        @endif
                    </div>

                    <div class="flex items-center gap-3">
                        @switch($item['status'])
                            @case('pending')
                                <span class="px-3 py-1 rounded-full text-sm bg-yellow-100 text-yellow-800">Menunggu</span>
                                @break
                            @case('rejected')
                                <span class="px-3 py-1 rounded-full text-sm bg-red-100 text-red-800">Ditolak</span>
                                @break
                            @case('active')
                                <span class="px-3 py-1 rounded-full text-sm bg-green-100 text-green-800">Disetujui</span>
                                @break
                            @case('expired')
                                <span class="px-3 py-1 rounded-full text-sm bg-gray-200 text-gray-700">Kedaluwarsa</span>
                                @break
                            @default
                                <span class="px-3 py-1 rounded-full text-sm bg-gray-100 text-gray-600">Belum diminta</span>
                        @endswitch

                                                @if ($item['status'] === 'active')
                            <a href="{{ route('customer.videos.watch', $item['video']) }}"
                               class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">
                                Tonton
                            </a>
                        @endif
                        @if (in_array($item['status'], ['none', 'rejected', 'expired']))
                            <form method="POST" action="{{ route('customer.videos.request', $item['video']) }}">
                                @csrf
                                <x-primary-button>
                                    {{ $item['status'] === 'none' ? 'Request akses' : 'Request ulang' }}
                                </x-primary-button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-white p-6 shadow-sm sm:rounded-lg text-gray-600">
                    Belum ada video tersedia.
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>