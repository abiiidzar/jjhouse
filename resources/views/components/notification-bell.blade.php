@props([
    'limit' => 6,
    'variant' => 'panel',
])

@auth
    @php
        $notificationUser = auth()->user();

        $recentNotifications = $notificationUser->notifications()
            ->latest()
            ->limit($limit)
            ->get();

        $unreadNotificationsCount = $notificationUser
            ->unreadNotifications()
            ->count();

        $buttonClasses = match ($variant) {
            'store' => '
                text-current
                hover:opacity-60
                focus-visible:ring-current
            ',

            'mobile-menu' => '
                w-full
                text-left
                text-black
            ',

            default => '
                text-gray-500
                hover:bg-gray-100
                hover:text-gray-900
                focus-visible:ring-gray-400
            ',
        };
    @endphp

    <div class="relative shrink-0" data-notification-bell data-notification-poll-url="{{ route('notifications.unread-count') }}">
        @if ($variant === 'mobile-menu')

            <button
                type="button"
                data-notification-trigger
                class="
                    flex
                    w-full
                    items-center
                    justify-between

                    px-5
                    py-4

                    border-b
                    border-gray-100

                    text-xs
                    uppercase
                    tracking-[0.15em]

                    hover:bg-gray-50
                    transition
                "
                aria-expanded="false"
                aria-haspopup="dialog"
            >

                <div class="flex items-center gap-3">

                    {{-- BELL ICON --}}
                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        aria-hidden="true"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.8"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 01-6 0v-1m6 0H9"
                        />
                    </svg>

                    <span>
                        Notifications
                    </span>

                </div>


                {{-- BADGE --}}
                <span
                    data-notification-badge
                    aria-live="polite"
                    class="
                        flex
                        min-w-5
                        h-5
                        items-center
                        justify-center

                        rounded-full
                        bg-red-600

                        px-1

                        text-[10px]
                        font-bold
                        leading-none
                        text-white

                        {{ $unreadNotificationsCount > 0 ? '' : 'hidden' }}
                    "
                >
                    {{ $unreadNotificationsCount > 99
                        ? '99+'
                        : $unreadNotificationsCount
                    }}
                </span>

            </button>

        @else

            <button
                type="button"
                data-notification-trigger
                class="
                    relative
                    inline-flex
                    h-10
                    w-10
                    items-center
                    justify-center
                    rounded-full
                    transition
                    focus:outline-none
                    focus-visible:ring-2
                    focus-visible:ring-offset-2

                    {{ $buttonClasses }}
                "
                aria-label="
                    Buka notifikasi
                    {{ $unreadNotificationsCount
                        ? ', ' . $unreadNotificationsCount . ' belum dibaca'
                        : ''
                    }}
                "
                aria-expanded="false"
                aria-haspopup="dialog"
            >

                <svg
                    class="h-5 w-5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                    aria-hidden="true"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="1.8"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 01-6 0v-1m6 0H9"
                    />
                </svg>

                <span
                    data-notification-badge
                    aria-live="polite"
                    class="
                        absolute
                        -right-0.5
                        -top-0.5

                        flex
                        h-5
                        min-w-5
                        items-center
                        justify-center

                        rounded-full
                        bg-red-600

                        px-1

                        text-[10px]
                        font-bold
                        leading-none
                        text-white

                        ring-2
                        ring-white

                        {{ $unreadNotificationsCount > 0 ? '' : 'hidden' }}
                    "
                >
                    {{ $unreadNotificationsCount > 99
                        ? '99+'
                        : $unreadNotificationsCount
                    }}
                </span>

            </button>

        @endif

        <section
            data-notification-panel
            class="absolute right-0 z-50 mt-3 hidden w-[calc(100vw-2rem)] max-w-sm overflow-hidden rounded-xl border border-gray-200 bg-white text-left text-gray-900 shadow-xl sm:w-96"
            aria-label="Daftar notifikasi"
        >
            <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3">
                <div>
                    <h2 class="text-sm font-semibold text-gray-900">Notifikasi</h2>
                    <p data-notification-unread-label class="mt-0.5 text-xs text-gray-500">
                        {{ $unreadNotificationsCount > 0 ? $unreadNotificationsCount . ' belum dibaca' : 'Semua sudah dibaca' }}
                    </p>
                </div>

                <form data-notification-read-all action="{{ route('notifications.read-all') }}" method="POST" class="{{ $unreadNotificationsCount > 0 ? '' : 'hidden' }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="rounded-md px-2 py-1 text-xs font-medium text-gray-600 transition hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-400">
                        Tandai semua dibaca
                    </button>
                </form>
            </div>

            @if ($recentNotifications->isEmpty())
                <div class="px-5 py-8 text-center">
                    <svg class="mx-auto h-8 w-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 01-6 0v-1m6 0H9" />
                    </svg>
                    <p class="mt-3 text-sm font-medium text-gray-700">Belum ada notifikasi</p>
                    <p class="mt-1 text-xs leading-5 text-gray-500">Pembaruan pesanan akan muncul di sini.</p>
                </div>
            @else
                <ul class="max-h-96 divide-y divide-gray-100 overflow-y-auto" role="list">
                    @foreach ($recentNotifications as $notification)
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

                        <li class="relative {{ $isUnread ? 'bg-gray-50/80' : 'bg-white' }}">
                            @if ($notificationUrl)
                                <a href="{{ $notificationUrl }}" class="block px-4 py-3 pr-12 transition hover:bg-gray-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-gray-400">
                            @else
                                <div class="px-4 py-3 pr-12">
                            @endif
                                <div class="flex gap-3">
                                    <div class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full {{ in_array($notificationIcon, ['order', 'package', 'shipping'], true) ? 'bg-blue-50 text-blue-600' : 'bg-gray-100 text-gray-600' }}">
                                        @if (in_array($notificationIcon, ['order', 'package', 'shipping'], true))
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                            </svg>
                                        @else
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 01-6 0v-1m6 0H9" />
                                            </svg>
                                        @endif
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-sm font-medium text-gray-900">{{ $notificationTitle }}</p>
                                        <p class="mt-0.5 line-clamp-2 text-xs leading-5 text-gray-600">{{ $notificationMessage }}</p>
                                        <p class="mt-1 text-[11px] text-gray-400">{{ optional($notification->created_at)->diffForHumans() }}</p>
                                    </div>
                                </div>
                            @if ($notificationUrl)
                                </a>
                            @else
                                </div>
                            @endif

                            @if ($isUnread)
                                <form action="{{ route('notifications.read', ['notification' => $notification->id]) }}" method="POST" class="absolute right-2 top-2">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="flex h-8 w-8 items-center justify-center rounded-full text-blue-600 transition hover:bg-blue-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500" aria-label="Tandai {{ $notificationTitle }} sudah dibaca" title="Tandai sudah dibaca">
                                        <span class="h-2 w-2 rounded-full bg-current" aria-hidden="true"></span>
                                    </button>
                                </form>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif

            <a href="{{ route('notifications.index') }}" class="block border-t border-gray-100 px-4 py-3 text-center text-sm font-medium text-gray-700 transition hover:bg-gray-50 hover:text-gray-950 focus:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-gray-400">
                Lihat semua notifikasi
            </a>
        </section>
    </div>
@endauth
