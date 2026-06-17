@extends('layouts.admin')

@section('title', 'Discount')
@section('header', 'Discount Management')
@section('subheader', 'Manage discounts')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Kelola Diskon</h1>
        <a href="{{ route('admin.discounts.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Tambah Diskon
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="min-w-full">
            <thead class="bg-gray-100 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Nama Diskon</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Tipe</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Nilai</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Periode</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Status</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Prioritas</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($discounts as $discount)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $discount->name }}</td>
                        <td class="px-6 py-4 text-sm">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                {{ $discount->discount_type === 'percentage' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $discount->discount_type === 'fixed' ? 'bg-purple-100 text-purple-800' : '' }}
                                {{ $discount->discount_type === 'bogo' ? 'bg-pink-100 text-pink-800' : '' }}
                                {{ $discount->discount_type === 'volume' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $discount->discount_type === 'flash' ? 'bg-red-100 text-red-800' : '' }}
                            ">
                                {{ ucfirst($discount->discount_type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm font-semibold">
                            @if($discount->discount_type === 'percentage')
                                {{ $discount->discount_value }}%
                            @else
                                Rp {{ number_format($discount->discount_value, 0, ',', '.') }}
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            <div class="text-xs">Dari: {{ $discount->starts_at->format('d M Y') }}</div>
                            @if($discount->ends_at)
                                <div class="text-xs">Sampai: {{ $discount->ends_at->format('d M Y') }}</div>
                            @else
                                <div class="text-xs text-red-600">Tidak ada batas akhir</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm">
                            @if($discount->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Tidak Aktif
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm">{{ $discount->priority }}</td>
                        <td class="px-6 py-4 text-sm space-x-2">
                            <a href="{{ route('admin.discounts.show', $discount) }}" class="text-gray-600 hover:text-gray-900 font-medium">
                                Detail
                            </a>
                            <a href="{{ route('admin.discounts.edit', $discount) }}" class="text-blue-600 hover:text-blue-900 font-medium">
                                Edit
                            </a>
                            <form action="{{ route('admin.discounts.toggle-status', $discount) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-yellow-600 hover:text-yellow-900 font-medium">
                                    {{ $discount->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </form>
                            <form action="{{ route('admin.discounts.destroy', $discount) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Yakin ingin menghapus?')" class="text-red-600 hover:text-red-900 font-medium">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                            Tidak ada data diskon
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $discounts->links() }}
    </div>
</div>
@endsection
