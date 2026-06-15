@extends('layouts.super_admin')

@section('title', 'Document Activity Log')

@section('content')
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">
                <div class="px-6 py-6">
                    <div class="flex items-center justify-between">
                        <h2 class="text-2xl font-semibold mb-6">Document Activity Log</h2>
                        <a href="javascript:history.back()"
                            class="inline-block rounded-md bg-primary-600 px-4 py-2 text-white">Go
                            Back</a>
                    </div>
                </div>
                <div class="rounded-lg border border-neutral-200 dark:border-neutral-700 overflow-hidden">
                    <table class="min-w-full text-sm">
                        <thead class="bg-neutral-100 dark:bg-neutral-800">
                            <tr>
                                <th class="px-4 py-3 text-left">Time</th>
                                <th class="px-4 py-3 text-left">Event</th>
                                <th class="px-4 py-3 text-left">WhoDoneIt</th>
                                <th class="px-4 py-3 text-left">Changes</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                            @forelse($rows as $r)
                                <tr>
                                    <td class="px-4 py-3">
                                        {{ \Carbon\Carbon::parse($r['time'])->format('d M, Y \a\t h:i A') }}</td>
                                    <td class="px-4 py-3">{{ $r['event'] }}</td>
                                    <td class="px-4 py-3">{{ $r['who'] }}</td>
                                    <td class="px-4 py-3">{!! e($r['changes']) !!}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="px-4 py-6 text-center" colspan="4">No activity yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>


            </div>
        </div>
    </div>
@endsection
