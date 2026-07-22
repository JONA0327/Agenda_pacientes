<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'AgendaCitas') }} - Iniciar Sesión</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css'])
</head>
<body class="auth-bg min-h-screen flex items-center justify-center p-3 sm:p-4">
    <div class="auth-card p-5 sm:p-8">
        <div class="text-center mb-6 sm:mb-8">
            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-cyan-500 to-teal-500 rounded-2xl flex items-center justify-center mx-auto mb-3 sm:mb-4 shadow-lg shadow-cyan-500/25">
                <svg class="w-6 h-6 sm:w-8 sm:h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-800">Agenda de Citas</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Ingresa tus credenciales para acceder</p>
        </div>

        @if (session('status'))
            <div class="mb-3 sm:mb-4 p-2.5 sm:p-3 rounded-lg bg-emerald-50 border border-emerald-200 text-xs sm:text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-3 sm:mb-4">
                <label for="email" class="auth-label text-xs sm:text-sm">Correo electrónico</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                       class="auth-input @error('email') border-red-300 focus:border-red-500 focus:ring-red-500/20 @enderror"
                       placeholder="tu@correo.com">
                @error('email')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-3 sm:mb-4">
                <label for="password" class="auth-label text-xs sm:text-sm">Contraseña</label>
                <input id="password" type="password" name="password" required autocomplete="current-password"
                       class="auth-input @error('password') border-red-300 focus:border-red-500 focus:ring-red-500/20 @enderror"
                       placeholder="••••••••">
                @error('password')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 sm:gap-0 mb-5 sm:mb-6">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="remember" class="rounded border-slate-300 text-cyan-600 focus:ring-cyan-500/20">
                    <span class="text-sm text-slate-600">Recordarme</span>
                </label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-sm text-cyan-600 hover:text-cyan-700 font-medium">
                        ¿Olvidaste tu contraseña?
                    </a>
                @endif
            </div>

            <button type="submit" class="auth-btn">
                Iniciar Sesión
            </button>
        </form>

        <div class="mt-5 sm:mt-6 text-center">
            <p class="text-[10px] sm:text-xs text-slate-400">
                Sistema de Gestión de Citas &mdash; Acceso exclusivo para personal autorizado
            </p>
        </div>
    </div>
</body>
</html>
