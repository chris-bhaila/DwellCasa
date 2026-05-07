@extends('layouts.admin')

@section('title', 'Settings - DwellCasa Admin')
@section('header_title', 'Settings')

@section('content')

<div class="mb-8">
    <h1 class="text-3xl font-serif font-bold text-slate-900 italic lg:hidden">Settings</h1>
    <p class="text-slate-500 mt-1">Manage system configuration, content, and access controls.</p>
</div>

@php
$groups = [
    [
        'label' => 'Content',
        'items' => [
            auth()->user()->hasRole('super_admin') ? [
                'route' => 'admin.home-info',
                'icon'  => 'bi-house',
                'label' => 'Home Page',
                'desc'  => 'Edit the global landing page headings and brand promise text.',
            ] : null,
            auth()->user()->can('manage website info') ? [
                'route' => 'admin.info',
                'icon'  => 'bi-globe',
                'label' => 'Website Info',
                'desc'  => 'Manage headings, contact details, check-in times, and social links per location.',
            ] : null,
        ],
    ],
    [
        'label' => 'System',
        'items' => [
            auth()->user()->can('manage locations') ? [
                'route' => 'admin.locations',
                'icon'  => 'bi-geo-alt',
                'label' => 'Locations',
                'desc'  => 'Add, edit, or deactivate property locations.',
            ] : null,
            auth()->user()->can('manage logs') ? [
                'route' => 'admin.activity-log',
                'icon'  => 'bi-journal-text',
                'label' => 'Activity Logs',
                'desc'  => 'Review a full audit trail of admin actions.',
            ] : null,
            auth()->user()->can('manage users') ? [
                'route' => 'admin.users',
                'icon'  => 'bi-people',
                'label' => 'Users',
                'desc'  => 'Grant/Revoke permissions.',
            ] : null,
        ],
    ],
];
@endphp

<div class="space-y-10">
    @foreach($groups as $group)
    @php $items = collect($group['items'])->filter()->values(); @endphp
    @if($items->isNotEmpty())
    <div>
        <h2 class="text-sm font-bold uppercase tracking-widest text-slate-400 mb-4">{{ $group['label'] }}</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($items as $item)
            <a href="{{ route($item['route']) }}"
               class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 flex items-start gap-4 hover:border-primary hover:shadow-md transition-all group">
                <div class="w-11 h-11 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500 group-hover:bg-primary/10 group-hover:text-primary transition-colors flex-shrink-0">
                    <i class="bi {{ $item['icon'] }} text-xl"></i>
                </div>
                <div class="min-w-0 pt-0.5">
                    <p class="font-semibold text-slate-900 text-sm mb-1">{{ $item['label'] }}</p>
                    <p class="text-sm text-slate-400 leading-relaxed">{{ $item['desc'] }}</p>
                </div>
                <i class="bi bi-chevron-right text-slate-300 group-hover:text-primary transition-colors flex-shrink-0 mt-1 ml-auto"></i>
            </a>
            @endforeach
        </div>
    </div>
    @endif
    @endforeach
</div>

@endsection
