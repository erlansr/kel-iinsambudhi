{{-- resources/views/layouts/admin.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Kas RT - @yield('title')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
</head>
<body class="bg-gray-100">
    <nav class="bg-green-700 text-white shadow-lg">
        <div class="container mx-auto px-4 py-3">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-6">
                    <a href="{{ route('admin.dashboard') }}" class="text-xl font-bold">🏘️ Kas RT Admin</a>
                    <a href="{{ route('admin.keluarga') }}" class="hover:text-green-200">👨‍👩‍👧‍👦 Keluarga</a>
                    <a href="{{ route('admin.tagihan') }}" class="hover:text-green-200">📋 Tagihan</a>
                    <a href="{{ route('admin.laporan') }}" class="hover:text-green-200">📊 Laporan</a>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm">{{ Auth::user()->name ?? 'Admin' }}</span>
                    <form method="POST" action="{{ route('admin.logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-red-600 hover:bg-red-700 px-3 py-1 rounded text-sm">
                            🚪 Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>
    
    <main class="container mx-auto px-4 py-8">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif
        
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif
        
        @yield('content')
    </main>
</body>
</html>