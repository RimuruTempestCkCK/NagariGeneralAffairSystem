@extends('layouts.adminator')

@section('title', 'Notifikasi')

@section('content')
<div class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 lg:mt-1.5 dark:bg-gray-800 dark:border-gray-700">
    <div class="w-full mb-1">
        <div class="mb-4">
            <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">Semua Notifikasi</h1>
        </div>
        <div class="flex justify-between items-center mb-4">
            <p class="text-gray-500 text-sm dark:text-gray-400">Anda memiliki {{ Auth::user()->unreadNotifications->count() }} notifikasi belum dibaca.</p>
            @if(Auth::user()->unreadNotifications->count() > 0)
                <form action="{{ route('notifications.markAllAsRead') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-white bg-primary-600 hover:bg-primary-700 focus:ring-4 focus:ring-primary-200 font-medium rounded-lg text-sm px-4 py-2 dark:bg-primary-500 dark:hover:bg-primary-600 focus:outline-none">Tandai Semua Dibaca</button>
                </form>
            @endif
        </div>
        
        <div class="flex flex-col">
            <div class="overflow-x-auto">
                <div class="inline-block min-w-full align-middle">
                    <div class="overflow-hidden shadow">
                        <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($notifications as $notif)
                            <li class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700 {{ is_null($notif->read_at) ? 'bg-blue-50 dark:bg-gray-600' : 'bg-white dark:bg-gray-800' }}">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-shrink-0">
                                            @if(is_null($notif->read_at))
                                                <span class="inline-block w-3 h-3 bg-blue-600 rounded-full"></span>
                                            @else
                                                <span class="inline-block w-3 h-3 bg-gray-300 rounded-full"></span>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate dark:text-white">
                                                {{ $notif->data['title'] ?? 'Notifikasi' }}
                                            </p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $notif->data['message'] ?? '' }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="inline-flex items-center text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ $notif->created_at->diffForHumans() }}
                                    </div>
                                </div>
                                <div class="mt-2 flex space-x-2 ml-7">
                                    @if(!empty($notif->data['url']))
                                        <a href="{{ route('notifications.markAndRedirect', $notif->id) }}" class="text-sm text-primary-600 hover:underline dark:text-primary-500">Lihat Detail</a>
                                    @endif
                                    @if(is_null($notif->read_at))
                                        <form action="{{ route('notifications.markAsRead', $notif->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-sm text-gray-600 hover:underline dark:text-gray-300 ml-3">Tandai Dibaca</button>
                                        </form>
                                    @endif
                                </div>
                            </li>
                            @empty
                            <li class="p-4 text-center text-gray-500 dark:text-gray-400">Tidak ada notifikasi.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4">
            {{ $notifications->links() }}
        </div>
    </div>
</div>
@endsection
