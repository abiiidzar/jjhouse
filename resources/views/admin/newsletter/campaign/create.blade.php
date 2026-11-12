@extends('layouts.admin')

@section('title', 'Create Newsletter Campaign')
@section('header', 'Newsletter Campaign')
@section('subheader', 'Buat dan kirim email campaign ke subscriber')

@section('content')
<div class="p-6 max-w-4xl mx-auto">

    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <h1 class="text-xl font-semibold text-gray-900">Create Newsletter Campaign</h1>

        <a href="{{ route('admin.newsletter.subscribers.index') }}"
           class="inline-flex items-center gap-1.5 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg text-sm font-medium transition">
            <span>← Kembali ke Subscribers</span>
        </a>
    </div>

    @if (session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm flex items-center gap-2">
            <x-icon name="check-circle" class="w-4 h-4 shrink-0" />
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm flex items-center gap-2">
            <x-icon name="exclamation-triangle" class="w-4 h-4 shrink-0" />
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
            <p class="font-semibold mb-1 flex items-center gap-1.5">
                <x-icon name="exclamation-triangle" class="w-4 h-4 shrink-0" />
                <span>Terdapat kesalahan pada form:</span>
            </p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
        <form id="campaignForm" method="POST" action="{{ route('admin.newsletter.send') }}" class="space-y-5">
            @csrf

            <!-- TARGET RECIPIENTS -->
            <div>
                <label for="send_to" class="block text-sm font-medium text-gray-700 mb-1">
                    Kirim Ke <span class="text-red-500">*</span>
                </label>
                <select name="send_to" id="send_to" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                    <option value="active" {{ old('send_to', 'active') === 'active' ? 'selected' : '' }}>
                        Active Subscribers ({{ \App\Models\NewsletterSubscription::where('is_active', true)->count() }})
                    </option>
                    <option value="all" {{ old('send_to') === 'all' ? 'selected' : '' }}>
                        All Subscribers ({{ \App\Models\NewsletterSubscription::count() }})
                    </option>
                </select>
                @error('send_to')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- SUBJECT -->
            <div>
                <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">
                    Subject <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       name="subject"
                       id="subject"
                       value="{{ old('subject') }}"
                       placeholder="Contoh: Koleksi Terbaru JJHouse Minggu Ini"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900"
                       required>
                @error('subject')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- BODY EDITOR -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Content <span class="text-red-500">*</span>
                </label>
                <div id="editor" class="bg-white border border-gray-300 rounded-lg h-64"></div>
                <input type="hidden" name="body" id="body" value="{{ old('body') }}">
                @error('body')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- BUTTONS -->
            <div class="flex flex-wrap gap-3 pt-2">
                <button type="submit"
                        class="inline-flex items-center gap-2 bg-gray-900 hover:bg-gray-800 text-white px-6 py-2.5 rounded-lg text-sm font-medium transition">
                    <x-icon name="check" class="w-4 h-4" />
                    <span>Send Newsletter</span>
                </button>

                <button type="button"
                        onclick="previewEmail()"
                        class="inline-flex items-center gap-2 bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-2.5 rounded-lg text-sm font-medium transition">
                    <x-icon name="eye" class="w-4 h-4" />
                    <span>Preview</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- PREVIEW MODAL -->
<div id="previewModal"
     class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white p-6 rounded-xl shadow-xl w-full max-w-lg max-h-[85vh] flex flex-col">
        <div class="flex items-center justify-between border-b pb-3 mb-4">
            <h2 class="font-semibold text-gray-900">Preview Newsletter</h2>
            <button type="button"
                    onclick="closePreview()"
                    aria-label="Tutup preview"
                    class="p-1 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100">
                <x-icon name="x-mark" class="w-5 h-5" />
            </button>
        </div>

        <p class="text-xs uppercase tracking-wider text-gray-500 mb-1">Subject</p>
        <p id="previewSubject" class="font-medium text-gray-900 mb-4"></p>

        <p class="text-xs uppercase tracking-wider text-gray-500 mb-1">Content</p>
        <div id="previewContent" class="prose prose-sm max-w-none overflow-y-auto border rounded-lg p-3 bg-gray-50 flex-1"></div>

        <div class="mt-4 flex justify-end">
            <button type="button"
                    onclick="closePreview()"
                    class="bg-gray-900 hover:bg-gray-800 text-white px-4 py-2 rounded-lg text-sm font-medium">
                Close
            </button>
        </div>
    </div>
</div>

<!-- QUILL -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

<script>
    const quill = new Quill('#editor', { theme: 'snow' });
    const bodyInput = document.getElementById('body');

    if (bodyInput.value) {
        quill.root.innerHTML = bodyInput.value;
    }

    function syncBodyInput() {
        bodyInput.value = quill.getText().trim().length === 0 ? '' : quill.root.innerHTML;
    }

    quill.on('text-change', syncBodyInput);

    document.getElementById('campaignForm').addEventListener('submit', syncBodyInput);

    function previewEmail() {
        document.getElementById('previewSubject').textContent = document.getElementById('subject').value || '(No Subject)';
        document.getElementById('previewContent').innerHTML = quill.root.innerHTML;
        const modal = document.getElementById('previewModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closePreview() {
        const modal = document.getElementById('previewModal');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }
</script>
@endsection

