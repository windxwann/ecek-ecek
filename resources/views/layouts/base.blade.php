<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'RideGo' }} – Platform Rental Motor</title>

    <!-- Outfit Font (PRD v1 - tipografi geometris) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (via CDN + Vite fallback) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#EAFBE7',
                            100: '#dcf8d5',
                            500: '#86EF72',
                            600: '#6fdc5c',
                            700: '#4a9d4f',
                        },
                        secondary: {
                            800: '#1e293b',
                            900: '#0f172a',
                        }
                    },
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        [x-cloak] { display: none !important; }

        /* PRD v1 - Design tokens (Bagian 10.2) */
        :root {
            --bg: #FFFFFF;
            --ink: #0B0B0B;
            --ink-muted: #6B7280;
            --accent: #86EF72;
            --accent-soft: #EAFBE7;
            --border: #E5E7EB;
            --border-accent: #86EF72;
        }

        body {
            font-family: 'Outfit', ui-sans-serif, system-ui, sans-serif;
            background-color: var(--bg);
            color: var(--ink);
        }

        /* Aksen biru lama dipetakan ke hijau muda PRD v1. */
        .bg-blue-600, .bg-blue-700 { background-color: var(--accent) !important; color: var(--ink) !important; }
        .bg-blue-500 { background-color: #6fdc5c !important; color: var(--ink) !important; }
        .bg-blue-100, .bg-blue-50, .bg-blue-50\/40, .bg-blue-50\/60 { background-color: var(--accent-soft) !important; }
        .text-blue-600, .text-blue-700, .text-blue-800 { color: #2f7d32 !important; }
        .text-blue-500 { color: #4a9d4f !important; }
        .text-blue-400 { color: #5fae63 !important; }
        .border-blue-500, .border-blue-600, .border-blue-200, .border-blue-100 { border-color: var(--accent) !important; }
        .ring-blue-500, .focus\:ring-blue-500:focus { --tw-ring-color: var(--accent) !important; }
        .focus\:border-blue-500:focus { border-color: var(--accent) !important; }
        .hover\:bg-blue-700:hover { background-color: #6fdc5c !important; color: var(--ink) !important; }
        .shadow-blue-500\/20, .shadow-blue-500\/25 { --tw-shadow-color: rgba(134, 239, 114, .45) !important; }
        .from-blue-600, .from-blue-500 { --tw-gradient-from: var(--accent) !important; }
        .to-indigo-700, .to-indigo-600 { --tw-gradient-to: #6fdc5c !important; }
        [class*="from-blue-600"][class*="text-white"], [class*="from-blue-500"][class*="text-white"] { color: var(--ink) !important; }

        /* Sidebar putih ramping dengan item aktif hijau lembut (PRD v1 10.4). */
        aside.bg-slate-900 { background-color: #FFFFFF !important; color: #374151 !important; border-right: 1px solid var(--border); }
        aside.bg-slate-900 .bg-slate-950,
        aside.bg-slate-900 [class*="bg-slate-900"] { background-color: #FFFFFF !important; }
        aside.bg-slate-900 [class*="border-slate-8"] { border-color: var(--border) !important; }
        aside.bg-slate-900 .text-slate-300 { color: #374151 !important; }
        aside.bg-slate-900 .text-slate-400 { color: var(--ink-muted) !important; }
        aside.bg-slate-900 .text-white { color: var(--ink) !important; }
        aside.bg-slate-900 .hover\:bg-slate-800:hover { background-color: var(--accent-soft) !important; color: var(--ink) !important; }
        aside.bg-slate-900 .bg-blue-600,
        aside.bg-slate-900 .bg-emerald-600 { background-color: var(--accent) !important; color: var(--ink) !important; }
        aside.bg-slate-900 .text-blue-500,
        aside.bg-slate-900 .text-emerald-500 { color: #2f7d32 !important; }
        aside.bg-slate-900 .text-blue-400 { color: #2f7d32 !important; }
        aside.bg-slate-900 .bg-blue-500\/10 { background-color: var(--accent-soft) !important; border-color: var(--accent) !important; }

        /* Sembunyikan scrollbar bawaan tapi tetap bisa di-scroll */
        *::-webkit-scrollbar { width: 0; height: 0; }
        * { scrollbar-width: none; -ms-overflow-style: none; }
    </style>

    @stack('styles')
</head>
<body class="h-full text-slate-700 antialiased bg-slate-50 flex flex-col">

    <!-- Flash Messages / Notifications -->
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             class="fixed top-5 right-5 z-50 flex items-center p-4 text-sm text-emerald-800 rounded-xl bg-emerald-50 border border-emerald-200 shadow-lg transition-all">
            <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600 mr-3 flex-shrink-0"></i>
            <span class="font-medium mr-3">{{ session('success') }}</span>
            <button @click="show = false" class="text-emerald-500 hover:text-emerald-700 ml-auto">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)"
             class="fixed top-5 right-5 z-50 flex items-center p-4 text-sm text-red-800 rounded-xl bg-red-50 border border-red-200 shadow-lg transition-all">
            <i data-lucide="alert-circle" class="w-5 h-5 text-red-600 mr-3 flex-shrink-0"></i>
            <span class="font-medium mr-3">{{ session('error') }}</span>
            <button @click="show = false" class="text-red-500 hover:text-red-700 ml-auto">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
    @endif

    @if(session('info'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             class="fixed top-5 right-5 z-50 flex items-center p-4 text-sm text-blue-800 rounded-xl bg-blue-50 border border-blue-200 shadow-lg transition-all">
            <i data-lucide="info" class="w-5 h-5 text-blue-600 mr-3 flex-shrink-0"></i>
            <span class="font-medium mr-3">{{ session('info') }}</span>
            <button @click="show = false" class="text-blue-500 hover:text-blue-700 ml-auto">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
    @endif

    @yield('body')

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.lucide) {
                window.lucide.createIcons();
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
