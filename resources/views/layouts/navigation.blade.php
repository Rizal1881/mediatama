<nav class="bg-[#0F0D14] border-b border-[#2A2436]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-wrap items-center justify-between gap-x-6 gap-y-3 py-4">
            <div class="flex items-center gap-8">
                <a href="{{ route('dashboard') }}"
                   class="font-['Cormorant_Garamond'] text-2xl font-semibold text-[#C9A961]">
                    Mediatama
                </a>

                @if (auth()->user()->role === 'customer')
                    <a href="{{ route('customer.videos.index') }}"
                       class="pb-1 text-sm border-b {{ request()->routeIs('customer.videos.*') ? 'text-[#F2EBDD] border-[#C9A961]' : 'text-[#9A93A8] border-transparent hover:text-[#F2EBDD]' }}">
                        Daftar Video
                    </a>
                @endif
            </div>

            <div class="flex items-center gap-5 text-sm">
                <span class="text-[#9A93A8]">{{ auth()->user()->name }}</span>

                <a href="{{ route('profile.edit') }}" class="text-[#9A93A8] hover:text-[#F2EBDD]">Profil</a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="rounded-md border border-[#C9A961]/50 px-3 py-1.5 text-[#C9A961] hover:bg-[#C9A961]/10 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C9A961]">
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>