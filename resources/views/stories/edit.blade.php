<x-app-layout>
    <style>
        .main-content .header {
            padding: 1rem;
            margin-block-end: 1rem;
            font-size: 1.5rem;
            font-weight: bold;
            background-color: #7b68ee;
            border-block-end: 1px solid #9370db;
        }

        .table-content {
            flex: 1;
            transition: margin-left 0.3s;
            padding: 1rem;
        }

        .main-content {
            flex: 1;
            transition: margin-left 0.3s;
            margin-inline-start: 9rem;
        }

        .form-container {
            background-color: #f9f9f9;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .form-container label {
            font-weight: bold;
            color: #333;
        }

        .form-container input,
        .form-container textarea {
            inline-size: 100%;
            padding: 0.75rem;
            border: 1px solid #ccc;
            border-radius: 8px;
            margin-block-start: 0.5rem;
            margin-block-end: 1rem;
        }

        .form-container button {
            background-color: #4CAF50;
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 25px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
        }

        .form-container button:hover {
            background-color: #45a049;
            transform: scale(1.05);
        }
    </style>

    <div class="table-content" id="main-content">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100 form-container">
                    <h1 class="text-2xl mb-4">Chỉnh sửa truyện</h1>
                    <form action="{{ route('stories.update', $story->story_id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label for="title" class="block text-sm font-medium text-gray-700">Tên truyện</label>
                            <input type="text" name="title" id="title"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                value="{{ $story->title }}" required>
                        </div>
                        <div class="mb-4">
                            <label for="author_name" class="block text-sm font-medium text-gray-700">Tác giả</label>
                            <input type="text" name="author_name" id="author_name"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                value="{{ $story->author_name }}" required>
                        </div>
                        <div class="mb-4">
                            <label for="description" class="block text-sm font-medium text-gray-700">Mô tả</label>
                            <textarea name="description" id="description"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                required>{{ $story->description }}</textarea>
                        </div>
                        <div class="mb-4">
                            <label for="image_path" class="block text-sm font-medium text-gray-700">Đường dẫn hình
                                ảnh</label>
                            <input type="text" name="image_path" id="image_path"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                value="{{ $story->image_path }}" required>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow-lg transition duration-300">Cập
                                nhật</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
