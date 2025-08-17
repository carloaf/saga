@extends('layouts.app')

@section('title', 'Debug Furriel')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-2xl font-bold">Debug Furriel Page</h1>
    
    <div class="mt-4">
        <p>Se você consegue ver esta mensagem, o problema está na view específica do arranchamento-cia.</p>
        
        @auth
        <p>Usuário: {{ auth()->user()->war_name }}</p>
        <p>Role: {{ auth()->user()->role }}</p>
        @endauth
    </div>
</div>
@endsection
