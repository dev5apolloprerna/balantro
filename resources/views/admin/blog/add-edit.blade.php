@extends('layouts.super_admin')

@section('title', isset($blog) && $blog ? 'Edit Blog' : 'Add Blog')

@section('content')
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-6">
            <h6 class="text-lg font-semibold text-gray-800 dark:text-white">
                {{ isset($blog) && $blog ? 'Edit Blog' : 'Add Blog' }}
            </h6>

            <a href="{{ route('super-admin.blog.index') }}"
                class="inline-flex items-center gap-2 rounded-lg bg-gray-600 px-4 py-2 text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500">
                <i class="fas fa-arrow-left"></i>
                Back
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12">
            <div class="col-span-12">
                <div class="bg-white dark:bg-gray-800 shadow rounded-2xl overflow-hidden">
                    <div class="p-6">
                        <form
                            action="{{ isset($blog) && $blog ? route('super-admin.blog.update', $blog->blog_id) : route('super-admin.blog.store') }}"
                            method="POST" enctype="multipart/form-data" class="space-y-6">
                            @csrf

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Title <span class="text-rose-600">*</span>
                                    </label>
                                    <input type="text" name="title" value="{{ old('title', $blog->title ?? '') }}"
                                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-700 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/30 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                    @error('title')
                                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Image
                                    </label>
                                    <input type="file" name="image"
                                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-700 file:mr-4 file:rounded-lg file:border-0 file:bg-primary-600 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-primary-700 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                    @error('image')
                                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                @if (isset($blog) && $blog && !empty($blog->image))
                                    <div>
                                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Current Image
                                        </label>
                                        <img src="{{ asset('uploads/Blog/' . $blog->image) }}" alt="Current Blog Image"
                                            class="h-20 w-20 rounded-lg object-cover shadow-sm">
                                    </div>
                                @endif

                                <div>
                                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Category <span class="text-rose-600">*</span>
                                    </label>

                                    <select name="category_id"
                                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-700 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/30 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                        <option value="">Select Category</option>

                                        @foreach ($categories as $category)
                                            <option value="{{ $category->category_id }}"
                                                {{ old('category_id', $blog->category_id ?? '') == $category->category_id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @error('category_id')
                                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Description
                                    </label>
                                    <textarea name="description" rows="4"
                                        class="ckeditor w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-700 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/30 dark:border-gray-700 dark:bg-gray-900 dark:text-white">{{ old('description', $blog->description ?? '') }}</textarea>
                                    @error('description')
                                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Meta Title
                                    </label>
                                    <input type="text" name="metaTitle"
                                        value="{{ old('metaTitle', $blog->metaTitle ?? '') }}"
                                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-700 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/30 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                    @error('metaTitle')
                                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Meta Keyword
                                    </label>
                                    <input type="text" name="metaKeyword"
                                        value="{{ old('metaKeyword', $blog->metaKeyword ?? '') }}"
                                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-700 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/30 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                    @error('metaKeyword')
                                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Meta Description
                                    </label>
                                    <textarea name="metaDescription" rows="3"
                                        class="ckeditor w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-700 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/30 dark:border-gray-700 dark:bg-gray-900 dark:text-white">{{ old('metaDescription', $blog->metaDescription ?? '') }}</textarea>
                                    @error('metaDescription')
                                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Head
                                    </label>
                                    <textarea name="head" rows="4"
                                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-700 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/30 dark:border-gray-700 dark:bg-gray-900 dark:text-white">{{ old('head', $blog->head ?? '') }}</textarea>
                                    @error('head')
                                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Body
                                    </label>
                                    <textarea name="body" rows="8"
                                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-700 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/30 dark:border-gray-700 dark:bg-gray-900 dark:text-white">{{ old('body', $blog->body ?? '') }}</textarea>
                                    @error('body')
                                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="flex justify-end gap-3 border-t border-gray-200 pt-6 dark:border-gray-700">
                                <a href="{{ route('super-admin.blog.index') }}"
                                    class="rounded-lg border border-gray-300 px-5 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:border-gray-700 dark:text-white dark:hover:bg-gray-700">
                                    Cancel
                                </a>

                                <button type="submit"
                                    class="rounded-lg bg-primary-600 px-5 py-2 text-sm font-semibold text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500">
                                    {{ isset($blog) && $blog ? 'Update' : 'Submit' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.ckeditor.com/4.12.1/standard/ckeditor.js"></script>
@endsection
