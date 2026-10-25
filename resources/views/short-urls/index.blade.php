<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Links Management') }}
            </h2>
            <a href="{{ route('short-urls.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700 transition">
                + New Short Link
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

            <!-- Search & Filters -->
            <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                <form method="GET" action="{{ route('short-urls.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                    <div class="sm:col-span-2">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by title, slug, or destination..." class="w-full text-sm rounded-lg border-gray-300">
                    </div>
                    <div>
                        <select name="status" class="w-full text-sm rounded-lg border-gray-300">
                            <option value="">All Statuses</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active Only</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Paused</option>
                            <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="w-full px-4 py-2 bg-slate-800 text-white rounded-lg text-sm font-semibold hover:bg-slate-700">Filter</button>
                        @if(request()->hasAny(['search', 'status']))
                            <a href="{{ route('short-urls.index') }}" class="px-3 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm font-semibold hover:bg-gray-200">Reset</a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Links Table -->
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-3">Short URL / Title</th>
                                <th class="px-6 py-3">Original Destination</th>
                                <th class="px-6 py-3">Clicks</th>
                                <th class="px-6 py-3">Status / Expiry</th>
                                <th class="px-6 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100" x-data="{ copiedSlug: null }">
                            @forelse($shortUrls as $link)
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-900">{{ $link->title ?: 'Untitled Link' }}</div>
                                        <div class="flex items-center gap-2 mt-1">
                                            <a href="{{ $link->short_url }}" target="_blank" class="font-mono text-xs font-semibold text-indigo-600 hover:underline">
                                                {{ $link->short_url }}
                                            </a>
                                            <button 
                                                @click="navigator.clipboard.writeText('{{ $link->short_url }}'); copiedSlug = '{{ $link->slug }}'; setTimeout(() => copiedSlug = null, 2000)" 
                                                type="button" 
                                                class="px-2 py-0.5 rounded text-[10px] font-semibold border transition"
                                                :class="copiedSlug === '{{ $link->slug }}' ? 'bg-emerald-50 text-emerald-700 border-emerald-300' : 'bg-gray-50 text-gray-600 border-gray-200 hover:bg-gray-100'">
                                                <span x-text="copiedSlug === '{{ $link->slug }}' ? 'Copied!' : 'Copy'"></span>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-xs text-gray-500 max-w-sm truncate">
                                        <a href="{{ $link->original_url }}" target="_blank" class="hover:text-indigo-600 transition truncate block">
                                            {{ $link->original_url }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-indigo-50 text-indigo-700">
                                            {{ number_format($link->clicks_count) }} clicks
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-xs">
                                        @if($link->isUsable())
                                            <span class="px-2 py-0.5 text-xs font-semibold rounded bg-emerald-50 text-emerald-700">Active</span>
                                        @elseif($link->isExpired())
                                            <span class="px-2 py-0.5 text-xs font-semibold rounded bg-rose-50 text-rose-700">Expired</span>
                                        @else
                                            <span class="px-2 py-0.5 text-xs font-semibold rounded bg-gray-100 text-gray-600">Paused</span>
                                        @endif

                                        @if($link->expires_at)
                                            <div class="text-[11px] text-gray-400 mt-1">
                                                Exp: {{ $link->expires_at->format('M d, Y') }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                                        <a href="{{ route('short-urls.analytics', $link) }}" class="text-indigo-600 hover:text-indigo-900 font-semibold text-xs">Analytics</a>
                                        <a href="{{ route('short-urls.edit', $link) }}" class="text-slate-600 hover:text-slate-900 font-semibold text-xs">Edit</a>
                                        <form method="POST" action="{{ route('short-urls.destroy', $link) }}" class="inline" onsubmit="return confirm('Delete this short URL? All click stats will be erased.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-rose-500 hover:text-rose-700 font-semibold text-xs">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                        No short links found. Create your first link above!
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($shortUrls->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100">
                        {{ $shortUrls->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
