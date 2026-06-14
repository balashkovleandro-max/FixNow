@extends('layouts.admin')

@section('title', 'Редакция на бизнес')
@section('page-title', 'Редакция на бизнес профил')
@section('eyebrow', 'Business edit')

@section('content')
    <form action="{{ route('admin.businesses.profile.update', $business) }}" method="POST" class="rounded-[1.75rem] border border-white/10 bg-white/[.08] p-4 shadow-2xl shadow-black/20 backdrop-blur-2xl sm:p-6">
        @csrf
        @method('PUT')

        <div class="grid gap-4 md:grid-cols-2">
            <label class="grid gap-2 text-sm font-bold text-white/70">
                Име на бизнеса
                <input name="business_name" value="{{ old('business_name', $business->business_name) }}" class="min-h-12 rounded-2xl border border-white/10 bg-slate-950/60 px-4 text-white outline-none focus:border-blue-300">
            </label>
            <label class="grid gap-2 text-sm font-bold text-white/70">
                Собственик
                <input name="name" value="{{ old('name', $business->name) }}" class="min-h-12 rounded-2xl border border-white/10 bg-slate-950/60 px-4 text-white outline-none focus:border-blue-300">
            </label>
            <label class="grid gap-2 text-sm font-bold text-white/70">
                Имейл
                <input name="email" type="email" value="{{ old('email', $business->email) }}" class="min-h-12 rounded-2xl border border-white/10 bg-slate-950/60 px-4 text-white outline-none focus:border-blue-300">
            </label>
            <label class="grid gap-2 text-sm font-bold text-white/70">
                Телефон
                <input name="phone" value="{{ old('phone', $business->phone) }}" class="min-h-12 rounded-2xl border border-white/10 bg-slate-950/60 px-4 text-white outline-none focus:border-blue-300">
            </label>
            <label class="grid gap-2 text-sm font-bold text-white/70">
                Град
                <input name="city" value="{{ old('city', $business->city) }}" class="min-h-12 rounded-2xl border border-white/10 bg-slate-950/60 px-4 text-white outline-none focus:border-blue-300">
            </label>
            <label class="grid gap-2 text-sm font-bold text-white/70">
                Категория
                <input name="business_category" value="{{ old('business_category', $business->business_category) }}" class="min-h-12 rounded-2xl border border-white/10 bg-slate-950/60 px-4 text-white outline-none focus:border-blue-300">
            </label>
            <label class="grid gap-2 text-sm font-bold text-white/70">
                Website
                <input name="website" value="{{ old('website', $business->website) }}" class="min-h-12 rounded-2xl border border-white/10 bg-slate-950/60 px-4 text-white outline-none focus:border-blue-300">
            </label>
            <label class="grid gap-2 text-sm font-bold text-white/70">
                Работно време
                <input name="working_hours" value="{{ old('working_hours', $business->working_hours) }}" class="min-h-12 rounded-2xl border border-white/10 bg-slate-950/60 px-4 text-white outline-none focus:border-blue-300">
            </label>
        </div>

        <div class="mt-4 grid gap-4">
            <label class="grid gap-2 text-sm font-bold text-white/70">
                Кратко описание
                <textarea name="short_description" rows="3" class="rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white outline-none focus:border-blue-300">{{ old('short_description', $business->short_description) }}</textarea>
            </label>
            <label class="grid gap-2 text-sm font-bold text-white/70">
                Пълно описание
                <textarea name="description" rows="7" class="rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white outline-none focus:border-blue-300">{{ old('description', $business->description) }}</textarea>
            </label>
        </div>

        <div class="mt-6 flex flex-col gap-3 sm:flex-row">
            <button class="min-h-12 rounded-2xl bg-white px-6 text-sm font-black text-[#070B1F]">Запази профила</button>
            <a href="{{ route('admin.businesses.index', ['search' => $business->email]) }}" class="inline-flex min-h-12 items-center justify-center rounded-2xl border border-white/10 bg-white/10 px-6 text-sm font-black text-white">Назад</a>
        </div>
    </form>
@endsection
