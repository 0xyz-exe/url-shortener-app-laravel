<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Short Link: ') }} /{{ $shortUrl->slug }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                <form method="POST" action="{{ route('short-urls.update', $shortUrl) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    @if ($errors->any())
                        <div class="p-4 bg-rose-50 text-rose-800 rounded-lg text-sm">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase">Destination URL *</label>
                        <input type="url" name="original_url" value="{{ old('original_url', $shortUrl->original_url) }}" required class="mt-1 w-full text-sm rounded-lg border-gray-300">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase">Title / Campaign Label</label>
                        <input type="text" name="title" value="{{ old('title', $shortUrl->title) }}" class="mt-1 w-full text-sm rounded-lg border-gray-300">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase">Custom Slug *</label>
                        <div class="mt-1 flex rounded-lg shadow-sm">
                            <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-xs font-mono">
                                {{ url('/') }}/
                            </span>
                            <input type="text" name="slug" value="{{ old('slug', $shortUrl->slug) }}" required class="flex-1 block w-full rounded-none rounded-r-lg text-sm border-gray-300 font-mono">
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $shortUrl->is_active) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                        <label for="is_active" class="text-sm font-medium text-gray-700">Link is Active (uncheck to temporarily pause redirects)</label>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase">Expiration Date & Time</label>
                        <input type="datetime-local" name="expires_at" value="{{ old('expires_at', $shortUrl->expires_at ? $shortUrl->expires_at->format('Y-m-d\TH:i') : '') }}" class="mt-1 w-full text-sm rounded-lg border-gray-300">
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t">
                        <a href="{{ route('short-urls.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-semibold hover:bg-gray-200">Cancel</a>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700">Update Link</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
