<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">  {{-- TAMBAHKAN INI --}}
    <title>Login Admin - Kas RT</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-md p-8 max-w-md w-full">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-green-600">🏘️ Kas RT</h1>
                <h2 class="text-xl font-semibold text-gray-700 mt-2">Login Admin</h2>
                <p class="text-gray-500 text-sm mt-1">Panel pengelolaan iuran warga</p>
            </div>
            
            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ $errors->first() }}
                </div>
            @endif
            
            <form method="POST" action="{{ route('admin.login.post') }}">
                @csrf
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Email Admin</label>
                    <input type="email" name="email" value="{{ old('email') }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-green-500"
                           required autofocus>
                </div>
                
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                    <input type="password" name="password" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-green-500"
                           required>
                </div>
                
                <div class="flex items-center justify-between mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" class="mr-2">
                        <span class="text-sm text-gray-600">Ingat saya</span>
                    </label>
                </div>
                
                <button type="submit" 
                        class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition">
                    Login
                </button>
            </form>
            
            <div class="text-center mt-6 text-sm text-gray-500">
                <p>Demo: admin@kasrt.com / password123</p>
            </div>
        </div>
    </div>
</body>
</html>