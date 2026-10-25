<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Analytics: /') }}{{ $shortUrl->slug }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('short-urls.edit', $shortUrl) }}" class="px-3 py-1.5 bg-slate-800 text-white rounded-lg text-xs font-semibold hover:bg-slate-700">Edit Link</a>
                <a href="{{ route('short-urls.index') }}" class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg text-xs font-semibold hover:bg-gray-200">&larr; Back to Links</a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Header Summary Card -->
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Short Link Target</span>
                        <h3 class="text-xl font-bold text-gray-900 mt-1">{{ $shortUrl->title ?: 'Untitled Short Link' }}</h3>
                        <p class="font-mono text-sm text-indigo-600 mt-1">
                            <a href="{{ $shortUrl->short_url }}" target="_blank" class="hover:underline">{{ $shortUrl->short_url }}</a>
                        </p>
                        <p class="text-xs text-gray-500 mt-1 truncate max-w-xl">
                            &rarr; {{ $shortUrl->original_url }}
                        </p>
                    </div>
                    <div class="flex items-center gap-6">
                        <div class="text-center">
                            <span class="text-xs text-gray-500 uppercase font-semibold">Total Clicks</span>
                            <p class="text-3xl font-extrabold text-indigo-600 mt-1">{{ number_format($shortUrl->clicks_count) }}</p>
                        </div>
                        <div class="text-center">
                            <span class="text-xs text-gray-500 uppercase font-semibold">Status</span>
                            <div class="mt-2">
                                @if($shortUrl->isUsable())
                                    <span class="px-3 py-1 text-xs font-bold rounded-full bg-emerald-50 text-emerald-700">Active</span>
                                @elseif($shortUrl->isExpired())
                                    <span class="px-3 py-1 text-xs font-bold rounded-full bg-rose-50 text-rose-700">Expired</span>
                                @else
                                    <span class="px-3 py-1 text-xs font-bold rounded-full bg-gray-100 text-gray-600">Paused</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Referrers Breakdown -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-4">
                    <h4 class="font-bold text-sm text-gray-900 uppercase tracking-wider">Top Referrer Channels</h4>
                    @if($referrers->isEmpty())
                        <p class="text-xs text-gray-400 italic">No referrer data available.</p>
                    @else
                        <div class="space-y-2">
                            @foreach($referrers as $ref)
                                <div class="flex justify-between items-center p-2 rounded bg-gray-50 border border-gray-100 text-xs">
                                    <span class="font-medium text-gray-800 truncate max-w-[180px]">{{ $ref->referrer_source }}</span>
                                    <span class="font-bold text-indigo-600">{{ $ref->total }} clicks</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Recent 50 Clicks Feed Table -->
                <div class="md:col-span-2 bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                    <h4 class="font-bold text-sm text-gray-900 uppercase tracking-wider mb-4">Latest Visitor Click Logs</h4>
                    @if($recentClicks->isEmpty())
                        <p class="text-xs text-gray-400 italic py-6 text-center">No visitors recorded yet.</p>
                    @else
                        <div class="overflow-x-auto max-h-96">
                            <table class="w-full text-xs text-left">
                                <thead class="text-gray-400 uppercase bg-gray-50 sticky top-0">
                                    <tr>
                                        <th class="px-4 py-2">Timestamp</th>
                                        <th class="px-4 py-2">Device & Browser</th>
                                        <th class="px-4 py-2">IP Address</th>
                                        <th class="px-4 py-2">Referer</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($recentClicks as $click)
                                        <tr class="hover:bg-gray-50/50">
                                            <td class="px-4 py-2 font-medium text-gray-900 whitespace-nowrap">
                                                {{ $click->created_at->format('M d, H:i') }}
                                                <span class="text-[10px] text-gray-400">({{ $click->created_at->diffForHumans() }})</span>
                                            </td>
                                            <td class="px-4 py-2 text-gray-700">
                                                {{ $click->device_type }} &bull; {{ $click->browser }}
                                            </td>
                                            <td class="px-4 py-2 font-mono text-gray-500">
                                                {{ $click->ip_address ?: 'Direct' }}
                                            </td>
                                            <td class="px-4 py-2 text-gray-500 truncate max-w-xs">
                                                {{ $click->referer ?: 'Direct' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
