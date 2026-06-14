@extends('layouts.admin')

@section('title', 'Потребители')
@section('page-title', 'Управление на потребители')
@section('eyebrow', 'Users')

@section('content')
    <section class="rounded-[1.75rem] border border-white/10 bg-white/[.08] p-4 shadow-2xl shadow-black/20 backdrop-blur-2xl sm:p-5">
        <form method="GET" action="{{ route('admin.users.index') }}" class="grid gap-3 lg:grid-cols-[220px_220px_1fr_auto]">
            <select name="role" class="min-h-12 rounded-2xl border border-white/10 bg-slate-950/60 px-4 text-sm font-bold text-white outline-none">
                @foreach(['all' => 'Всички роли', 'admin' => 'Admin', 'business' => 'Business', 'customer' => 'Customer', 'client' => 'Client', 'freelancer' => 'Freelancer'] as $key => $label)
                    <option value="{{ $key }}" @selected(($filters['role'] ?? 'all') === $key)>{{ $label }}</option>
                @endforeach
            </select>
            <select name="type" class="min-h-12 rounded-2xl border border-white/10 bg-slate-950/60 px-4 text-sm font-bold text-white outline-none">
                @foreach(['all' => 'Всички типове', 'admin' => 'Admin', 'business' => 'Business', 'client' => 'Client', 'freelancer' => 'Freelancer'] as $key => $label)
                    <option value="{{ $key }}" @selected(($filters['type'] ?? 'all') === $key)>{{ $label }}</option>
                @endforeach
            </select>
            <input name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Търси по име, имейл, телефон, бизнес..." class="min-h-12 rounded-2xl border border-white/10 bg-slate-950/60 px-4 text-sm font-bold text-white outline-none placeholder:text-white/35">
            <button class="min-h-12 rounded-2xl bg-white px-5 text-sm font-black text-[#070B1F]">Филтрирай</button>
        </form>
    </section>

    <section class="mt-4 grid gap-3">
        @forelse($users as $user)
            <article class="rounded-[1.5rem] border border-white/10 bg-white/[.08] p-4 shadow-xl shadow-black/15 backdrop-blur-xl">
                <div class="grid gap-4 lg:grid-cols-[1.2fr_.8fr_1fr_auto] lg:items-center">
                    <div class="min-w-0">
                        <p class="truncate text-lg font-black">{{ $user->name }}</p>
                        <p class="mt-1 truncate text-sm text-white/50">{{ $user->email }} · {{ $user->phone ?: 'няма телефон' }}</p>
                        @if($user->business_name)
                            <p class="mt-1 truncate text-xs text-white/40">Бизнес: {{ $user->business_name }}</p>
                        @endif
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <span class="rounded-full bg-blue-400/10 px-3 py-1 text-xs font-black text-blue-100">{{ $user->role }}</span>
                        <span class="rounded-full bg-violet-400/10 px-3 py-1 text-xs font-black text-violet-100">Тип: {{ $user->accountType() }}</span>
                        @if($user->profile_type)
                            <span class="rounded-full bg-fuchsia-400/10 px-3 py-1 text-xs font-black text-fuchsia-100">Профил: {{ $user->profile_type }}</span>
                        @endif
                        <span class="rounded-full {{ ($user->is_suspended ?? false) ? 'bg-rose-400/10 text-rose-100' : 'bg-emerald-400/10 text-emerald-100' }} px-3 py-1 text-xs font-black">{{ ($user->is_suspended ?? false) ? 'Спрян' : 'Активен' }}</span>
                    </div>

                    <div class="grid grid-cols-3 gap-2 text-center text-xs text-white/55">
                        <div class="rounded-2xl bg-slate-950/35 p-3">
                            <p class="font-black text-white">{{ $user->customer_service_requests_count ?? 0 }}</p>
                            <p>заявки</p>
                        </div>
                        <div class="rounded-2xl bg-slate-950/35 p-3">
                            <p class="font-black text-white">{{ $user->service_request_offers_count ?? 0 }}</p>
                            <p>оферти</p>
                        </div>
                        <div class="rounded-2xl bg-slate-950/35 p-3">
                            <p class="font-black text-white">{{ $user->freelancer_job_applications_count ?? 0 }}</p>
                            <p>кандид.</p>
                        </div>
                    </div>

                    <div class="grid gap-2">
                        <form action="{{ route('admin.users.update', $user) }}" method="POST" class="flex gap-2">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="action" value="role">
                            <select name="role" class="min-h-10 min-w-0 flex-1 rounded-xl border border-white/10 bg-slate-950/70 px-3 text-xs font-bold text-white">
                                @foreach(['admin', 'business', 'customer', 'client', 'freelancer'] as $role)
                                    <option value="{{ $role }}" @selected($user->role === $role)>{{ $role }}</option>
                                @endforeach
                            </select>
                            <button class="rounded-xl bg-white px-3 text-xs font-black text-[#070B1F]">OK</button>
                        </form>
                        <div class="grid grid-cols-2 gap-2">
                            <form action="{{ route('admin.users.update', $user) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="action" value="{{ ($user->is_suspended ?? false) ? 'activate' : 'suspend' }}">
                                <button class="min-h-10 w-full rounded-xl border border-white/10 bg-white/10 px-3 text-xs font-black text-white">{{ ($user->is_suspended ?? false) ? 'Активирай' : 'Спри' }}</button>
                            </form>
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Сигурен ли си, че искаш да изтриеш този потребител?')">
                                @csrf
                                @method('DELETE')
                                <button class="min-h-10 w-full rounded-xl bg-rose-500/15 px-3 text-xs font-black text-rose-100">Изтрий</button>
                            </form>
                        </div>
                    </div>
                </div>
            </article>
        @empty
            <p class="rounded-2xl border border-white/10 bg-white/5 p-5 text-white/55">Няма потребители.</p>
        @endforelse
    </section>

    <div class="mt-4">{{ $users->links() }}</div>
@endsection
