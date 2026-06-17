@extends('layouts.admin')

@section('content')
<div class="p-6 max-w-4xl mx-auto">

    <h1 class="text-xl font-semibold mb-6">Create Newsletter Campaign</h1>

    <form method="POST" action="{{ route('admin.newsletter.send') }}">
        @csrf

        <!-- SUBJECT -->
        <div class="mb-4">
            <label class="text-sm">Subject</label>
            <input type="text" name="subject"
                   class="w-full border px-3 py-2 rounded mt-1"
                   required>
        </div>

        <!-- EDITOR -->
        <div class="mb-4">
            <label class="text-sm">Content</label>

            <div id="editor" class="bg-white border rounded h-64"></div>
            <input type="hidden" name="content" id="content">
        </div>

        <!-- BUTTON -->
        <div class="flex gap-3">
            <button type="submit"
                    class="bg-black text-white px-6 py-2 rounded">
                Send Newsletter
            </button>

            <button type="button"
                    onclick="previewEmail()"
                    class="bg-gray-200 px-6 py-2 rounded">
                Preview
            </button>
        </div>

    </form>

</div>

<!-- PREVIEW MODAL -->
<div id="previewModal"
     class="fixed inset-0 bg-black/50 hidden items-center justify-center">

    <div class="bg-white p-6 rounded w-full max-w-lg">
        <h2 class="font-semibold mb-3">Preview</h2>
        <div id="previewContent"></div>

        <button onclick="closePreview()"
                class="mt-4 bg-black text-white px-4 py-2 rounded">
            Close
        </button>
    </div>
</div>

<!-- QUILL -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

<script>
    let quill = new Quill('#editor', { theme: 'snow' });

    document.querySelector('form').onsubmit = function() {
        document.getElementById('content').value = quill.root.innerHTML;
    }

    function previewEmail() {
        document.getElementById('previewContent').innerHTML = quill.root.innerHTML;
        document.getElementById('previewModal').classList.remove('hidden');
        document.getElementById('previewModal').classList.add('flex');
    }

    function closePreview() {
        document.getElementById('previewModal').classList.add('hidden');
    }
</script>

@endsection
