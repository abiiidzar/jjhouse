@extends('layouts.admin')

@section('title', 'Media')
@section('header', 'Media Management')
@section('subheader', 'Manage home page media')

@section('content')
<div class="min-h-screen bg-gray-100 ">

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        @if ($message = Session::get('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ $message }}
            </div>
        @endif

        @if ($message = Session::get('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                {{ $message }}
            </div>
        @endif

        <!-- Hero Image Section -->
        {{-- <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-6">
            <div class="p-6 border-b">
                <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                    <span class="text-3xl mr-3">🖼️</span> Hero Image
                </h2>
                <p class="text-gray-600 text-sm mt-1">Upload gambar utama yang tampil di bagian atas halaman home</p>
            </div>

            <div class="p-6">
                <div class="grid md:grid-cols-2 gap-6">
                    <!-- Preview -->
                    <div>
                        <p class="font-semibold text-gray-700 mb-3">Current Image</p>
                        <div class="bg-gray-200 rounded-lg overflow-hidden border-2 border-gray-300">
                            <img src="{{ $homeImages['hero_image'] }}" alt="Hero Image" loading="lazy" decoding="async" class="w-full h-64 object-cover">
                        </div>
                    </div>

                    <!-- Upload Form -->
                    <div>
                        <p class="font-semibold text-gray-700 mb-3">Upload New Image</p>
                        <form action="{{ route('admin.media.upload.hero') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="border-2 border-dashed border-blue-400 rounded-lg p-6 text-center cursor-pointer hover:bg-blue-50 transition"
                                 onclick="document.getElementById('hero_image').click()">
                                <p class="text-gray-600">📁 Click to upload or drag and drop</p>
                                <p class="text-sm text-gray-500">PNG, JPG, GIF (Max 5MB)</p>
                                <input type="file" id="hero_image" name="hero_image" class="hidden" accept="image/*" required>
                            </div>

                            @error('hero_image')
                                <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                            @enderror

                            <button type="submit" class="w-full mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                                ✓ Upload Hero Image
                            </button>
                        </form>

                        <form action="{{ route('admin.media.reset') }}" method="POST" class="mt-3">
                            @csrf
                            <input type="hidden" name="image_type" value="hero">
                            <button type="submit" class="w-full px-4 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500 font-medium text-sm">
                                ↺ Reset to Default
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div> --}}
        <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-6">
            <div class="p-6 border-b">
                <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2.5">
                    <x-icon name="image" class="w-7 h-7 text-gray-700" />
                    <span>Hero Slider Images</span>
                </h2>
                <p class="text-gray-600 text-sm mt-1">
                    Upload 3 gambar untuk slider hero (01, 02, 03)
                </p>
            </div>

            <div class="p-6 grid md:grid-cols-3 gap-6">

                @foreach(['hero_image_1','hero_image_2','hero_image_3'] as $key => $imageKey)

                <div class="border rounded-lg p-4">

                    <p class="font-semibold mb-3">Slide {{ $key + 1 }}</p>

                    <!-- PREVIEW -->
                    <div class="bg-gray-200 rounded mb-3 overflow-hidden">
                        <img src="{{ $homeImages[$imageKey] ?? '' }}"
                            loading="lazy"
                            decoding="async"
                            class="w-full h-40 object-cover">
                    </div>

                    <!-- FORM -->
                    <form action="{{ route('admin.media.upload.hero') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="position" value="{{ $key + 1 }}">

                        <input type="file" name="image" required class="mb-2">

                        <button class="w-full bg-blue-600 text-white py-1 rounded">
                            Upload
                        </button>
                    </form>

                </div>

                @endforeach

            </div>
        </div>

        <!-- Story Image Section -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-6">
            <div class="p-6 border-b">
                <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-3">
                    <x-icon name="book-open" class="w-7 h-7 text-gray-700" />
                    <span>Story Image</span>
                </h2>
                <p class="text-gray-600 text-sm mt-1">Upload gambar yang tampil di samping kiri section story</p>
            </div>

            <div class="p-6">
                <div class="grid md:grid-cols-2 gap-6">
                    <!-- Preview -->
                    <div>
                        <p class="font-semibold text-gray-700 mb-3">Current Image</p>
                        <div class="bg-gray-200 rounded-lg overflow-hidden border-2 border-gray-300">
                            <img src="{{ $homeImages['story_image'] }}" alt="Story Image" loading="lazy" decoding="async" class="w-full h-64 object-cover">
                        </div>
                    </div>

                    <!-- Upload Form -->
                    <div>
                        <p class="font-semibold text-gray-700 mb-3">Upload New Image</p>
                        <form action="{{ route('admin.media.upload.story') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="border-2 border-dashed border-green-400 rounded-lg p-6 text-center cursor-pointer hover:bg-green-50 transition"
                                 onclick="document.getElementById('story_image').click()">
                                <p class="text-gray-600 inline-flex items-center justify-center gap-2">
                                    <x-icon name="folder" class="w-5 h-5 text-green-600" />
                                    <span>Click to upload or drag and drop</span>
                                </p>
                                <p class="text-sm text-gray-500">PNG, JPG, GIF (Max 5MB)</p>
                                <input type="file" id="story_image" name="story_image" class="hidden" accept="image/*" required>
                            </div>

                            @error('story_image')
                                <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                            @enderror

                            <button type="submit" class="w-full mt-4 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium inline-flex items-center justify-center gap-2">
                                <x-icon name="check" class="w-4 h-4" />
                                <span>Upload Story Image</span>
                            </button>
                        </form>

                        <form action="{{ route('admin.media.reset') }}" method="POST" class="mt-3">
                            @csrf
                            <input type="hidden" name="image_type" value="story">
                            <button type="submit" class="w-full px-4 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500 font-medium text-sm inline-flex items-center justify-center gap-1.5">
                                <x-icon name="arrow-path" class="w-4 h-4" />
                                <span>Reset to Default</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Home Page Settings Section -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-6">
            <div class="p-6 border-b">
                <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-3">
                    <x-icon name="cog" class="w-7 h-7 text-gray-700" />
                    <span>Home Page Settings</span>
                </h2>
                <p class="text-gray-600 text-sm mt-1">Control visibility dan tampilan section di halaman home</p>
            </div>

            <div class="p-6">
                <form action="{{ route('admin.media.settings') }}" method="POST">
                    @csrf

                    <div class="space-y-6">
                        <!-- Featured Products Count -->
                        <div>
                            <label for="featured_products_count" class="flex items-center gap-2 text-sm font-medium text-gray-700 mb-2">
                                <x-icon name="shopping-bag" class="w-4 h-4 text-gray-600" />
                                <span>Jumlah Featured Products yang Ditampilkan</span>
                            </label>
                            <input type="number" id="featured_products_count" name="featured_products_count"
                                   min="1" max="20" value="{{ $homeSettings['featured_products_count'] }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <p class="text-gray-500 text-xs mt-1">Atur berapa produk yang tampil di carousel featured products (1-20)</p>
                        </div>

                        <!-- Section Visibility Settings -->
                        <div class="border-t pt-6">
                            <p class="text-sm font-semibold text-gray-700 mb-4 flex items-center gap-2">
                                <x-icon name="clipboard-list" class="w-4 h-4 text-gray-600" />
                                <span>Visibility Sections</span>
                            </p>

                            <div class="space-y-4">
                                <!-- Story Section Toggle -->
                                <div class="flex items-center justify-between p-4 border rounded-lg hover:bg-gray-50 transition">
                                    <div>
                                        <p class="font-medium text-gray-700 flex items-center gap-2">
                                            <x-icon name="book-open" class="w-4 h-4 text-gray-600" />
                                            <span>Story Section</span>
                                        </p>
                                        <p class="text-sm text-gray-500">Gambar story dengan teks di samping</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="hidden" name="show_story_section" value="0">
                                        <input type="checkbox" name="show_story_section" value="1"
                                               {{ $homeSettings['show_story_section'] ? 'checked' : '' }}
                                               class="sr-only peer">
                                        <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                    </label>
                                </div>

                                <!-- Featured Products Toggle -->
                                <div class="flex items-center justify-between p-4 border rounded-lg hover:bg-gray-50 transition">
                                    <div>
                                        <p class="font-medium text-gray-700 flex items-center gap-2">
                                            <x-icon name="sparkles" class="w-4 h-4 text-gray-600" />
                                            <span>Featured Products</span>
                                        </p>
                                        <p class="text-sm text-gray-500">Carousel produk featured</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="hidden" name="show_featured_products" value="0">
                                        <input type="checkbox" name="show_featured_products" value="1"
                                               {{ $homeSettings['show_featured_products'] ? 'checked' : '' }}
                                               class="sr-only peer">
                                        <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                    </label>
                                </div>

                                <!-- Editorial Grid Toggle -->
                                <div class="flex items-center justify-between p-4 border rounded-lg hover:bg-gray-50 transition">
                                    <div>
                                        <p class="font-medium text-gray-700 flex items-center gap-2">
                                            <x-icon name="swatch" class="w-4 h-4 text-gray-600" />
                                            <span>Editorial Grid</span>
                                        </p>
                                        <p class="text-sm text-gray-500">Grid 3 gambar editorial di bawah</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="hidden" name="show_editorial_grid" value="0">
                                        <input type="checkbox" name="show_editorial_grid" value="1"
                                               {{ $homeSettings['show_editorial_grid'] ? 'checked' : '' }}
                                               class="sr-only peer">
                                        <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium inline-flex items-center gap-2">
                            <x-icon name="check" class="w-4 h-4" />
                            <span>Save Settings</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Editorial Images Section -->

            <div class="p-6">
                <div class="grid md:grid-cols-3 gap-6">
                    @foreach([1, 2, 3] as $position)
                    <div class="border rounded-lg p-4 hover:shadow-lg transition">
                        <div class="mb-4">
                            <p class="font-semibold text-gray-700 mb-2">Editorial Image {{ $position }}</p>
                            <div class="bg-gray-200 rounded-lg overflow-hidden border-2 border-gray-300 h-48">
                                <img src="{{ $homeImages['editorial_image_' . $position] }}" alt="Editorial {{ $position }}" loading="lazy" decoding="async" class="w-full h-full object-cover">
                            </div>
                        </div>

                        <form action="{{ route('admin.media.upload.editorial') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="position" value="{{ $position }}">

                            <div class="border-2 border-dashed border-purple-400 rounded-lg p-4 text-center cursor-pointer hover:bg-purple-50 transition mb-3"
                                 onclick="document.getElementById('editorial_{{ $position }}').click()">
                                <p class="text-sm text-gray-600 inline-flex items-center justify-center gap-1.5">
                                    <x-icon name="folder" class="w-4 h-4 text-purple-600" />
                                    <span>Click to upload</span>
                                </p>
                                <input type="file" id="editorial_{{ $position }}" name="editorial_image" class="hidden" accept="image/*" required>
                            </div>

                            <button type="submit" class="w-full px-3 py-2 bg-purple-600 text-white rounded text-sm hover:bg-purple-700 font-medium">
                                Upload
                            </button>
                        </form>

                        <form action="{{ route('admin.media.reset') }}" method="POST" class="mt-2">
                            @csrf
                            <input type="hidden" name="image_type" value="editorial_{{ $position }}">
                            <button type="submit" class="w-full px-3 py-2 bg-gray-400 text-white rounded text-sm hover:bg-gray-500 font-medium">
                                Reset
                            </button>
                        </form>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Info Box -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mt-6">
            <p class="text-blue-800 flex items-start gap-2">
                <x-icon name="light-bulb" class="w-5 h-5 text-blue-600 mt-0.5" />
                <span>
                    <strong>Tips:</strong> Gunakan ukuran gambar yang optimal untuk performa lebih baik.
                    Rekomendasi: Hero image 1920x1080px, Story image 600x600px, Editorial images 600x600px.
                </span>
            </p>
        </div>
    </div>
</div>
@endsection
