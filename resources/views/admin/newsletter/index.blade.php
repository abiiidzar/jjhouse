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

    <!-- SEARCH -->
    <form method="GET" class="mb-4 flex gap-2">
        <input name="search"
               value="{{ request('search') }}"
               placeholder="Cari email..."
               class="border px-3 py-2 rounded w-64">

        <select name="status" class="border px-3 py-2 rounded">
            <option value="">All</option>
            <option value="active">Active</option>
            <option value="inactive">Unsubscribed</option>
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
                        <form method="POST" action="{{ route('admin.newsletter.subscribers.destroy', $sub->id) }}">
                            @csrf @method('DELETE')
                            <button class="text-red-500 hover:underline text-xs">
                                Delete
                            </button>
                        </form>
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
