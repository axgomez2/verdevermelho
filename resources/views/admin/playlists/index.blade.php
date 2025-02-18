@extends('layouts.admin')

@section('content')
    <div class="p-4">
        <div class="mb-4 flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-gray-900 sm:text-3xl dark:text-white">{{ __('Playlists') }}</h1>
            <a href="{{ route('admin.playlists.create') }}" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white rounded-lg bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path>
                </svg>
                {{ __('Add Playlist') }}
            </a>
        </div>

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            {{ __('Image') }}
                        </th>
                        <th scope="col" class="px-6 py-3">
                            {{ __('Name') }}
                        </th>
                        <th scope="col" class="px-6 py-3">
                            {{ __('Tracks') }}
                        </th>
                        <th scope="col" class="px-6 py-3">
                            {{ __('Status') }}
                        </th>
                        <th scope="col" class="px-6 py-3">
                            {{ __('Actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($playlists as $playlist)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="px-6 py-4">
                                <img class="w-10 h-10 rounded-full" src="{{ $playlist->image_url }}" alt="{{ $playlist->name }}">
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-base font-semibold text-gray-900 dark:text-white">{{ $playlist->name }}</div>
                                <div class="font-normal text-gray-500">{{ $playlist->slug }}</div>
                            </td>
                            <td class="px-6 py-4 font-medium">
                                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">
                                    {{ $playlist->tracks->count() }} / 10
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($playlist->is_active)
                                    <span class="inline-flex items-center bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full dark:bg-green-900 dark:text-green-300">
                                        <span class="w-2 h-2 mr-1 bg-green-500 rounded-full"></span>
                                        {{ __('Active') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full dark:bg-red-900 dark:text-red-300">
                                        <span class="w-2 h-2 mr-1 bg-red-500 rounded-full"></span>
                                        {{ __('Inactive') }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('admin.playlists.edit', $playlist) }}"
                                       class="font-medium text-primary-600 dark:text-primary-500 hover:underline">
                                        {{ __('Edit') }}
                                    </a>
                                    <form action="{{ route('admin.playlists.destroy', $playlist) }}"
                                          method="POST"
                                          class="inline-block"
                                          x-data
                                          @submit.prevent="if (confirm('{{ __('Are you sure you want to delete this playlist?') }}')) $el.submit()">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="font-medium text-red-600 dark:text-red-500 hover:underline">
                                            {{ __('Delete') }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr class="bg-white dark:bg-gray-800">
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                {{ __('No playlists found.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($playlists->hasPages())
            <div class="px-4 py-3">
                {{ $playlists->links() }}
            </div>
        @endif
    </div>
@endsection
