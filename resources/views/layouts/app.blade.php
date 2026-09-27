<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'LuxStay Hotels')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        gold: { 50:'#fdf8ee',100:'#f8ecd0',300:'#e9c877',500:'#c9a14a',600:'#a9822f',700:'#8a6825' },
                        ink: { 900:'#0f1720', 800:'#16202b', 700:'#1e2a37' },
                    },
                    fontFamily: { serif: ['Georgia', 'ui-serif', 'serif'] },
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.13.5/cdn.min.js"></script>
    @stack('head')
</head>
<body class="bg-slate-50 text-ink-900 antialiased">
    <nav class="bg-ink-900 text-white sticky top-0 z-40 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="{{ route('home') }}" class="flex items-center gap-2 text-xl font-serif tracking-wide">
                    <i class="fa-solid fa-hotel text-gold-400"></i>
                    <span>Lux<span class="text-gold-400">Stay</span></span>
                </a>

                <div class="hidden md:flex items-center gap-6 text-sm font-medium">
                    <a href="{{ route('rooms.index') }}" class="hover:text-gold-300 transition">Rooms</a>
                    @auth
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="hover:text-gold-300 transition">Admin</a>
                            <a href="{{ route('admin.rooms.index') }}" class="hover:text-gold-300 transition">Manage Rooms</a>
                            <a href="{{ route('admin.users.index') }}" class="hover:text-gold-300 transition">Users</a>
                        @endif
                        @if(auth()->user()->isStaff())
                            <a href="{{ route('staff.dashboard') }}" class="hover:text-gold-300 transition">Front Desk</a>
                        @endif
                        <a href="{{ route('customer.dashboard') }}" class="hover:text-gold-300 transition">My Bookings</a>
                    @endauth
                </div>

                <div class="flex items-center gap-3">
                    @auth
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center gap-2 bg-ink-800 hover:bg-ink-700 rounded-full px-3 py-1.5 transition">
                                <span class="w-6 h-6 rounded-full bg-gold-500 text-ink-900 flex items-center justify-center text-xs font-bold">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </span>
                                <span class="text-sm hidden sm:inline">{{ auth()->user()->name }}</span>
                                <i class="fa-solid fa-chevron-down text-xs"></i>
                            </button>
                            <div x-show="open" @click.outside="open = false" x-cloak
                                 class="absolute right-0 mt-2 w-48 bg-white text-ink-900 rounded-lg shadow-xl py-2 text-sm">
                                <span class="block px-4 py-1 text-xs uppercase tracking-wide text-slate-400">{{ ucfirst(auth()->user()->role) }}</span>
                                <a href="{{ route('customer.profile.edit') }}" class="block px-4 py-2 hover:bg-slate-50">My Profile</a>
                                <a href="{{ route('customer.dashboard') }}" class="block px-4 py-2 hover:bg-slate-50">My Bookings</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="w-full text-left px-4 py-2 hover:bg-slate-50 text-red-600">Log out</button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-sm hover:text-gold-300">Sign in</a>
                        <a href="{{ route('register') }}" class="bg-gold-500 hover:bg-gold-600 text-ink-900 font-semibold text-sm px-4 py-2 rounded-full transition">Sign up</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    @if (session('status'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg px-4 py-3 text-sm flex items-center gap-2">
                <i class="fa-solid fa-circle-check"></i> {{ session('status') }}
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <main>
        @yield('content')
    </main>

    <footer class="bg-ink-900 text-slate-300 mt-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 grid grid-cols-1 md:grid-cols-3 gap-8 text-sm">
            <div>
                <div class="text-lg font-serif text-white mb-2"><i class="fa-solid fa-hotel text-gold-400"></i> LuxStay Hotels</div>
                <p class="text-slate-400">Effortless luxury, wherever you land.</p>
            </div>
            <div>
                <h4 class="text-white font-semibold mb-2">Explore</h4>
                <a href="{{ route('rooms.index') }}" class="block hover:text-gold-300">Rooms & Suites</a>
            </div>
            <div>
                <h4 class="text-white font-semibold mb-2">Demo Accounts</h4>
                <p class="text-slate-400">admin@luxstay.test / staff@luxstay.test / guest@luxstay.test — password: <code>password</code></p>
            </div>
        </div>
        <div class="border-t border-ink-700 text-center text-xs text-slate-500 py-4">&copy; {{ date('Y') }} LuxStay Hotels. Demo project.</div>
    </footer>
</body>
</html>
