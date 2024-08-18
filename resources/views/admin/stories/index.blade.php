index.blade.php
<x-app-layout>
    <div class="table-content" id="main-content">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="action-bar">
                        <a href="{{ route('stories.create') }}" class="create-button">
                            <i class="fas fa-plus mr-2"></i>Tạo truyện mới
                        </a>
                        <div class="search-bar">
                            <form action="{{ route('admin.stories') }}" method="GET" style="display: flex;">
                                <input type="text" name="search" placeholder="Tìm kiếm truyện..."
                                    style="padding: 0.5rem; max-inline-size: 400px; border-radius: 5px; border: 1px solid #ccc;">
                                <button type="submit"
                                    style="padding: 0.5rem; border-radius: 5px; background-color: #4CAF50; color: white; border: none; margin-inline-start: 0.5rem;">Tìm
                                    kiếm</button>
                            </form>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700 styled-table">
                            <thead class="bg-gray-100 dark:bg-gray-700">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                        ID
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                        Tên truyện
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                        Tác giả
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                        Thao tác
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($stories as $story)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-200">
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $story->story_id }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $story->title }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $story->author_name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap flex space-x-4">
                                            <a href="{{ route('stories.edit', $story->story_id) }}">
                                                <button type="button"
                                                    class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                                                    style="border-radius: 12px; padding: 8px 16px; background-color: #E0F7FA; border: none;">
                                                    <i class="fas fa-edit mr-1"></i>Chỉnh sửa
                                                </button>
                                            </a>
                                            <form action="{{ route('stories.destroy', $story->story_id) }}"
                                                method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                                    style="border-radius: 12px; padding: 8px 16px; background-color: #FFEBEE; border: none;">
                                                    <i class="fas fa-trash-alt mr-1"></i>Xóa
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="mt-4">
                            <div class="pagination">
                                {{ $stories->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
