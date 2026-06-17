@extends('layouts.admin')

@section('title', 'Instagram Feed')
@section('header', 'Instagram Feed Settings')
@section('subheader', 'Configure Instagram Basic Display API')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Settings Form -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="font-semibold mb-4">API Configuration</h3>
        <form action="{{ route('admin.instagram.save') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Instagram User ID</label>
                    <input type="text" name="user_id" value="{{ $userId }}" class="w-full border border-gray-300 rounded-md px-3 py-2" placeholder="Enter your Instagram User ID">
                    <p class="text-xs text-gray-400 mt-1">Find your User ID from Instagram Basic Display API</p>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Access Token</label>
                    <textarea name="access_token" rows="3" class="w-full border border-gray-300 rounded-md px-3 py-2" placeholder="Enter your Instagram Access Token">{{ $accessToken }}</textarea>
                    <p class="text-xs text-gray-400 mt-1">Long-lived access token (valid for 60 days)</p>
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="bg-gray-900 text-white px-4 py-2 rounded-md hover:bg-gray-800">Save Settings</button>
                    <a href="{{ route('admin.instagram.test') }}" class="border border-gray-300 px-4 py-2 rounded-md hover:bg-gray-50">Test Connection</a>
                    <a href="{{ route('admin.instagram.refresh') }}" class="border border-gray-300 px-4 py-2 rounded-md hover:bg-gray-50">Refresh Cache</a>
                </div>
            </div>
        </form>

        <div class="mt-6 pt-6 border-t border-gray-100">
            <h4 class="font-medium text-sm mb-2">How to Get Your Access Token:</h4>
            <ol class="text-xs text-gray-600 space-y-1 list-decimal list-inside">
                <li>Go to Facebook Developers → Instagram Basic Display</li>
                <li>Create or select your app</li>
                <li>Add Instagram Basic Display product</li>
                <li>Generate Long-lived Access Token</li>
                <li>Copy User ID and Access Token</li>
            </ol>
        </div>
    </div>

    <!-- Preview Feed -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-semibold">Feed Preview</h3>
            <button onclick="refreshFeed()" class="text-sm text-blue-600 hover:text-blue-800">Refresh Preview</button>
        </div>
        <div id="feed-preview" class="grid grid-cols-3 gap-2">
            <div class="aspect-square bg-gray-100 rounded animate-pulse"></div>
            <div class="aspect-square bg-gray-100 rounded animate-pulse"></div>
            <div class="aspect-square bg-gray-100 rounded animate-pulse"></div>
        </div>
        <p class="text-xs text-gray-400 mt-4">Feed is cached for 1 hour. Click refresh to update.</p>
    </div>
</div>

@push('scripts')
<script>
    function refreshFeed() {
        document.getElementById('feed-preview').innerHTML = `
            <div class="aspect-square bg-gray-100 rounded animate-pulse"></div>
            <div class="aspect-square bg-gray-100 rounded animate-pulse"></div>
            <div class="aspect-square bg-gray-100 rounded animate-pulse"></div>
        `;

        fetch('{{ route("admin.instagram.refresh") }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
            .then(() => location.reload());
    }
</script>
@endpush
@endsection
