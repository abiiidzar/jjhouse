@extends('layouts.admin')

@section('content')
<div class="p-6 max-w-6xl mx-auto">

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-xl font-semibold">Newsletter Subscribers</h1>

        <div class="flex gap-2">
            <a href="{{ route('admin.newsletter.subscribers.export') }}"
               class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 text-sm">
               Export CSV
            </a>

            <a href="{{ route('admin.newsletter.campaign') }}"
               class="px-4 py-2 bg-black text-white rounded text-sm">
               + New Campaign
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm flex items-center gap-2">
            <x-icon name="check-circle" class="w-4 h-4 shrink-0" />
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- SEARCH -->
    <form method="GET" class="mb-4 flex gap-2">
        <input name="search"
               value="{{ request('search') }}"
               placeholder="Cari email..."
               class="border px-3 py-2 rounded w-64">

        <select name="status" class="border px-3 py-2 rounded">
            <option value="">All</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Unsubscribed</option>
        </select>

        <button class="bg-black text-white px-4 py-2 rounded">Filter</button>
    </form>

    <div class="bg-white border rounded-xl overflow-hidden">

        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="p-3 text-left">Email</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse($subscribers as $sub)
                <tr class="border-t">
                    <td class="p-3">{{ $sub->email }}</td>

                    <td>
                        <span class="text-xs px-2 py-1 rounded
                            {{ $sub->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-600' }}">
                            {{ $sub->is_active ? 'Active' : 'Unsubscribed' }}
                        </span>
                    </td>

                    <td class="text-gray-500 text-xs">
                        {{ $sub->created_at->format('d M Y') }}
                    </td>

                    <td class="text-center">
                        <div class="flex items-center justify-center gap-1.5">
                            <form method="POST" action="{{ route('admin.newsletter.subscribers.toggle-status', $sub->id) }}" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        title="{{ $sub->is_active ? 'Jadikan Unsubscribed' : 'Jadikan Active' }}"
                                        aria-label="{{ $sub->is_active ? 'Jadikan Unsubscribed' : 'Jadikan Active' }}"
                                        class="p-1.5 rounded-lg {{ $sub->is_active ? 'text-yellow-600 hover:text-yellow-900 hover:bg-yellow-50' : 'text-green-600 hover:text-green-900 hover:bg-green-50' }} transition inline-flex items-center justify-center">
                                    <x-icon name="power" class="w-4 h-4" />
                                    <span class="sr-only">{{ $sub->is_active ? 'Jadikan Unsubscribed' : 'Jadikan Active' }}</span>
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.newsletter.subscribers.destroy', $sub->id) }}" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        onclick="return confirm('Yakin ingin menghapus subscriber ini?')"
                                        title="Delete"
                                        aria-label="Delete"
                                        class="p-1.5 rounded-lg text-red-500 hover:text-red-800 hover:bg-red-50 transition inline-flex items-center justify-center">
                                    <x-icon name="trash" class="w-4 h-4" />
                                    <span class="sr-only">Delete</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-6 text-gray-400">
                        Belum ada subscriber
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

    </div>

    <div class="mt-4">
        {{ $subscribers->links() }}
    </div>

</div>
@endsection
