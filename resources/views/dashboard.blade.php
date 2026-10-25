<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Links Dashboard') }}
            </h2>
            <a href="{{ route('short-urls.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700 transition">
                + Shorten New URL
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-sm">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Short Links</p>
                    <p class="text-3xl font-extrabold text-gray-900 mt-2">{{ $totalLinks }}</p>
                    <p class="text-xs text-emerald-600 font-medium mt-1">{{ $activeLinks }} currently active</p>
                </div>
                <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Clicks Recorded</p>
                    <p class="text-3xl font-extrabold text-indigo-600 mt-2">{{ number_format($totalClicks) }}</p>
                    <p class="text-xs text-gray-500 font-medium mt-1">Aggregated link redirects</p>
                </div>
                <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Avg Clicks / Link</p>
                    <p class="text-3xl font-extrabold text-slate-800 mt-2">{{ $totalLinks > 0 ? round($totalClicks / $totalLinks, 1) : 0 }}</p>
                    <p class="text-xs text-gray-500 font-medium mt-1">Traffic efficiency metric</p>
                </div>
            </div>

            <!-- Quick Shorten Form -->
            <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm">
                <h3 class="text-base font-bold text-gray-900 mb-3">Quick Shorten</h3>
                <form method="POST" action="{{ route('short-urls.store') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                    @csrf
                    <div class="sm:col-span-2">
                        <input type="url" name="original_url" required placeholder="https://example.com/very/long/destination/url" class="w-full text-sm rounded-lg border-gray-300">
                    </div>
                    <div>
                        <input type="text" name="slug" placeholder="Optional custom slug" class="w-full text-sm rounded-lg border-gray-300">
                    </div>
                    <div>
                        <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700 transition">
                            Shorten Link
                        </button>
                    </div>
                </form>
            </div>

            <!-- Main Content Split -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Top Links -->
                <div class="lg:col-span-2 bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-base font-bold text-gray-900">Top Performing Links</h3>
                        <a href="{{ route('short-urls.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                            View All Links &rarr;
                        </a>
                    </div>

                    @if($topLinks->isEmpty())
                        <p class="text-sm text-gray-500 py-6 text-center">No short links created yet.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left">
                                <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b">
                                    <tr>
                                        <th class="px-4 py-3">Link / Title</th>
                                        <th class="px-4 py-3">Clicks</th>
                                        <th class="px-4 py-3">Status</th>
                                        <th class="px-4 py-3 text-right">Analytics</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($topLinks as $link)
                                        <tr>
                                            <td class="px-4 py-3">
                                                <div class="font-bold text-gray-900">{{ $link->title ?: $link->slug }}</div>
                                                <div class="font-mono text-xs text-indigo-600 flex items-center gap-1">
                                                    <span>/{{ $link->slug }}</span>
                                                    <span class="text-gray-400 font-sans truncate max-w-xs">&rarr; {{ $link->original_url }}</span>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 font-extrabold text-indigo-600">
                                                {{ number_format($link->clicks_count) }}
                                            </td>
                                            <td class="px-4 py-3">
                                                @if($link->isUsable())
                                                    <span class="px-2 py-0.5 text-xs font-semibold rounded bg-emerald-50 text-emerald-700">Active</span>
                                                @elseif($link->isExpired())
                                                    <span class="px-2 py-0.5 text-xs font-semibold rounded bg-rose-50 text-rose-700">Expired</span>
                                                @else
                                                    <span class="px-2 py-0.5 text-xs font-semibold rounded bg-gray-100 text-gray-600">Paused</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-right">
                                                <a href="{{ route('short-urls.analytics', $link) }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                                                    Insights &rarr;
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                <!-- Recent Click Stream -->
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-4">
                    <h3 class="text-base font-bold text-gray-900">Live Click Feed</h3>
                    <div class="space-y-3">
                        @forelse($recentClicks as $click)
                            <div class="p-3 rounded-lg bg-gray-50 border border-gray-100 text-xs space-y-1">
                                <div class="flex justify-between font-medium">
                                    <span class="text-indigo-600 font-mono font-bold">/{{ $click->shortUrl->slug }}</span>
                                    <span class="text-gray-400">{{ $click->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="text-gray-600 flex justify-between">
                                    <span>{{ $click->device_type }} &bull; {{ $click->browser }}</span>
                                    <span class="font-mono text-gray-400">{{ $click->ip_address ?: '127.0.0.1' }}</span>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-gray-400 italic py-4 text-center">No clicks logged yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
