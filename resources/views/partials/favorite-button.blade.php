@php
    $profile = $profile ?? null;
    $compact = (bool) ($compact ?? false);
    $variant = $variant ?? 'dark';
    $isFavorite = $profile && auth()->check() && auth()->user()->isFavorite($profile);
    $baseClasses = $compact
        ? 'inline-flex h-11 w-11 items-center justify-center rounded-2xl text-lg font-black shadow-lg transition hover:-translate-y-0.5'
        : 'inline-flex min-h-12 items-center justify-center gap-2 rounded-2xl px-5 py-3 text-sm font-black shadow-lg transition hover:-translate-y-0.5';
    $variantClasses = $variant === 'light'
        ? ($isFavorite ? 'bg-pink-50 text-pink-600 ring-1 ring-pink-200' : 'bg-white/85 text-slate-700 ring-1 ring-slate-200 hover:text-pink-600')
        : ($isFavorite ? 'bg-pink-400/15 text-pink-100 ring-1 ring-pink-300/25' : 'bg-white/10 text-white ring-1 ring-white/10 hover:bg-pink-400/15 hover:text-pink-100');
@endphp

@if($profile)
    @auth
        @if((int) auth()->id() !== (int) $profile->id)
            <form action="{{ $isFavorite ? route('favorites.destroy', $profile) : route('favorites.store', $profile) }}" method="POST" class="{{ $compact ? 'inline-flex' : 'w-full sm:w-auto' }}">
                @csrf
                @if($isFavorite)
                    @method('DELETE')
                @endif
                <button type="submit" class="{{ $baseClasses }} {{ $variantClasses }}" title="{{ $isFavorite ? 'Премахни от Любими' : 'Добави в Любими' }}" aria-label="{{ $isFavorite ? 'Премахни от Любими' : 'Добави в Любими' }}">
                    <span aria-hidden="true">{{ $isFavorite ? '♥' : '♡' }}</span>
                    @unless($compact)
                        <span>{{ $isFavorite ? 'В Любими' : 'Запази' }}</span>
                    @endunless
                </button>
            </form>
        @endif
    @else
        <a href="{{ route('login') }}" class="{{ $baseClasses }} {{ $variantClasses }}" title="Влез, за да запазиш" aria-label="Влез, за да запазиш">
            <span aria-hidden="true">♡</span>
            @unless($compact)
                <span>Запази</span>
            @endunless
        </a>
    @endauth
@endif
