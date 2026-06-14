@extends('layouts.admin')

@section('title', 'Audit лог')
@section('page-title', 'Audit лог')
@section('eyebrow', 'Admin activity')

@section('content')
    <section class="grid gap-3">
        @forelse($activityLogs as $log)
            <article class="rounded-[1.35rem] border border-white/10 bg-white/[.08] p-4 shadow-xl shadow-black/15 backdrop-blur-xl">
                <div class="grid gap-3 lg:grid-cols-[1fr_auto]">
                    <div>
                        <p class="font-black">{{ $log->action }}</p>
                        <p class="mt-1 text-sm text-white/50">{{ $log->admin?->name ?: 'Admin' }} · {{ $log->created_at?->format('d.m.Y H:i:s') }}</p>
                        <p class="mt-1 text-xs text-white/35">{{ class_basename($log->subject_type) }} #{{ $log->subject_id }}</p>
                    </div>
                    <details class="rounded-2xl border border-white/10 bg-slate-950/40 p-3 text-xs text-white/60">
                        <summary class="cursor-pointer font-black text-white">Детайли</summary>
                        <pre class="mt-3 max-h-60 overflow-auto whitespace-pre-wrap">{{ json_encode(['old' => $log->old_values, 'new' => $log->new_values, 'meta' => $log->metadata], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    </details>
                </div>
            </article>
        @empty
            <p class="rounded-2xl border border-white/10 bg-white/5 p-5 text-white/55">Все още няма записани admin действия.</p>
        @endforelse
    </section>

    @if(method_exists($activityLogs, 'links'))
        <div class="mt-4">{{ $activityLogs->links() }}</div>
    @endif
@endsection
