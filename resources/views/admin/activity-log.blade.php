@extends('layouts.admin')

@section('title', 'Activity Log - DwellCasa Admin')

@section('content')

<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
    <div>
        <h1 class="text-3xl font-serif font-bold text-slate-900 italic">Activity Log</h1>
        <p class="text-slate-500 mt-1">
            @if(auth()->user()->hasRole('super_admin'))
                All admin actions across every location.
            @else
                Admin actions for your location.
            @endif
        </p>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50 text-slate-500 text-sm border-b border-slate-100">
                    <th class="p-4 font-medium">Time</th>
                    <th class="p-4 font-medium">User</th>
                    @if(auth()->user()->hasRole('super_admin'))
                    <th class="p-4 font-medium">Location</th>
                    @endif
                    <th class="p-4 font-medium">Action</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-slate-100">
                @forelse($logs as $log)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="p-4 text-slate-500 whitespace-nowrap">
                        {{ $log->created_at->format('M j, Y — g:i A') }}
                    </td>
                    <td class="p-4">
                        @if($log->causer)
                        <div class="font-semibold text-slate-900">{{ $log->causer->name }}</div>
                        <div class="text-xs text-slate-400">{{ $log->causer->email }}</div>
                        @else
                        <span class="text-slate-400 italic text-xs">System</span>
                        @endif
                    </td>
                    @if(auth()->user()->hasRole('super_admin'))
                    <td class="p-4">
                        @if($log->location)
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-slate-50 text-slate-700 border border-slate-200">
                            {{ $log->location->name }}
                        </span>
                        @else
                        <span class="text-slate-400 italic text-xs">—</span>
                        @endif
                    </td>
                    @endif
                    <td class="p-4 text-slate-700">{{ $log->description }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="{{ auth()->user()->hasRole('super_admin') ? 4 : 3 }}" class="p-8 text-center text-slate-400 italic">
                        No activity recorded yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($logs->hasPages())
    <div class="p-4 border-t border-slate-100">
        {{ $logs->links() }}
    </div>
    @endif
</div>

@endsection
