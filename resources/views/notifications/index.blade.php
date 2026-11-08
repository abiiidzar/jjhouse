@php
    $notificationUser = auth()->user();
    $layout = $notificationUser->hasRole('admin')
        ? 'layouts.admin'
        : ($notificationUser->hasRole('karyawan')
            ? 'layouts.karyawan'
            : ($notificationUser->hasRole('kasir') ? 'layouts.kasir' : 'layouts.app'));
    $hasUnreadNotifications = $notificationUser->unreadNotifications()->exists();
@endphp

@extends($layout)

@section('title', 'Notifikasi')
@section('header', 'Notifikasi')
@section('subheader', 'Pembaruan terbaru untuk akun Anda')
@section('page-title', 'Notifikasi')

@section('content')
    <div data-theme="light" class="mx-auto max-w-3xl pt-24 pb-8 md:pt-20 md:pb-12">
        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-4 border-b border-gray-100 px-5 py-4 sm:px-6">
                <div>
                    <h1 class="text-lg font-semibold text-gray-900">Semua Notifikasi</h1>
                    <p class="mt-1 text-sm text-gray-500">Lihat pembaruan pembayaran dan pengiriman pesanan Anda.</p>
                </div>

                @if ($hasUnreadNotifications)
                    <form action="{{ route('notifications.read-all') }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-400">
                            Tandai semua dibaca
                        </button>
                    </form>
                @endif
            </div>

            @forelse ($notifications as $notification)
                @php
                    $notificationData = is_array($notification->data) ? $notification->data : [];
                    $notificationTitle = data_get($notificationData, 'title', 'Pembaruan pesanan');
                    $notificationMessage = data_get($notificationData, 'message')
                        ?? data_get($notificationData, 'body')
                        ?? 'Ada pembaruan baru untuk Anda.';
                    $notificationUrl = data_get($notificationData, 'action_url')
                        ?? data_get($notificationData, 'url')
                        ?? data_get($notificationData, 'link');
                    $notificationIcon = data_get($notificationData, 'icon', 'bell');
                    $isUnread = is_null($notification->read_at);
                @endphp

                <article class="relative border-b border-gray-100 last:border-b-0 {{ $isUnread ? 'bg-blue-50/40' : 'bg-white' }}">
                    @if ($notificationUrl)
                        <a href="{{ $notificationUrl }}" class="block px-5 py-4 pr-14 transition hover:bg-gray-50 sm:px-6">
                    @else
                        <div class="px-5 py-4 pr-14 sm:px-6">
                    @endif
                        <div class="flex gap-4">
                            <div class="mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center rounded-full {{ in_array($notificationIcon, ['order', 'package', 'shipping'], true) ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600' }}">
                                @if (in_array($notificationIcon, ['order', 'package', 'shipping'], true))
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                @else
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 01-6 0v-1m6 0H9" />
                                    </svg>
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
                                    <h2 class="text-sm font-semibold text-gray-900">{{ $notificationTitle }}</h2>
                                    @if ($isUnread)
                                        <span class="rounded-full bg-blue-100 px-2 py-0.5 text-[11px] font-medium text-blue-700">Baru</span>
                                    @endif
                                </div>
                                <p class="mt-1 text-sm leading-6 text-gray-600">{{ $notificationMessage }}</p>
                                <p class="mt-2 text-xs text-gray-400">{{ optional($notification->created_at)->diffForHumans() }}</p>
                            </div>
                        </div>
                    @if ($notificationUrl)
                        </a>
                    @else
                        </div>
                    @endif

                    @if ($isUnread)
                        <form action="{{ route('notifications.read', ['notification' => $notification->id]) }}" method="POST" class="absolute right-4 top-4 sm:right-5">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="flex h-9 w-9 items-center justify-center rounded-full text-blue-600 transition hover:bg-blue-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500" aria-label="Tandai {{ $notificationTitle }} sudah dibaca" title="Tandai sudah dibaca">
                                <span class="h-2.5 w-2.5 rounded-full bg-current" aria-hidden="true"></span>
                            </button>
                        </form>
                    @endif
                </article>
            @empty
                <div class="px-6 py-14 text-center">
                    <svg class="mx-auto h-10 w-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 01-6 0v-1m6 0H9" />
                    </svg>
                    <h2 class="mt-4 text-sm font-semibold text-gray-800">Belum ada notifikasi</h2>
                    <p class="mt-1 text-sm text-gray-500">Pembaruan pesanan Anda akan tampil di halaman ini.</p>
                </div>
            @endforelse
        </div>

        @if ($notifications->hasPages())
            <div class="mt-6">
                {{ $notifications->onEachSide(1)->links() }}
            </div>
        @endif
    </div>
@endsection
