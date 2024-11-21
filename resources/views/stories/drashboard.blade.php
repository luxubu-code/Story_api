<x-app-layout>
    <style>
        body {
            display: flex;
            min-block-size: 100vh;
            margin: 0;
            overflow: hidden;
        }

        .sidebar {
            inline-size: 16rem;
            transition: inline-size 0.3s;
            block-size: 100vh;
            background-color: #1a202c;
            /* Dark background */
            color: white;
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
        }

        .sidebar.collapsed {
            inline-size: 4rem;
        }

        .main-content {
            flex: 1;
            transition: margin-left 0.3s;
            margin-left: 16rem;
            padding: 1rem;
            width: calc(100% - 16rem);
            position: absolute;
            top: 0;
            bottom: 0;
            right: 0;
            background-color: #f7fafc;
        }

        .main-content.full-width {
            margin-left: 4rem;
            width: calc(100% - 4rem);
        }

        .sidebar .toggle-btn {
            background-color: #4a5568;
            /* Darker background for toggle button */
            color: white;
            padding: 1rem;
            text-align: center;
            cursor: pointer;
        }

        .sidebar ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        .sidebar ul li {
            margin: 1rem 0;
        }

        .sidebar ul li a {
            display: block;
            padding: 1rem;
            color: white;
            text-decoration: none;
            transition: background-color 0.3s, padding-left 0.3s;
        }

        .sidebar ul li a:hover {
            background-color: #2d3748;
            /* Slightly lighter background on hover */
            padding-left: 1.5rem;
            /* Indent on hover */
        }

        .sidebar .logo {
            text-align: center;
            padding: 1rem;
            font-size: 1.5rem;
            font-weight: bold;
        }
    </style>

    <div class="sidebar" id="sidebar">
        <div class="logo">MyLogo</div>
        <div class="toggle-btn" id="sidebar-toggle">☰</div>
        <ul>
            <li><a href="{{ route('dashboard') }}">🏠 Home</a></li>
            <li><a href="{{ route(' stories') }}">📚 Quản lý truyện</a></li>
            <!-- Các liên kết khác -->
        </ul>
    </div>

    <div class="main-content" id="main-content">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h1 class="text-2xl mb-4">Danh sách truyện</h1>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th
                                    class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    ID</th>
                                <th
                                    class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tên truyện</th>
                                <th
                                    class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tác giả</th>
                                <th
                                    class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($stories as $story)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $story->story_id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $story->title }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $story->author }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="{{ route('stories.edit', $story->story_id) }}"
                                            class="text-indigo-600 hover:text-indigo-900">Chỉnh sửa</a>
                                        <form action="{{ route('stories.destroy', $story->story_id) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Xóa</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('sidebar-toggle').addEventListener('click', function() {
                let sidebar = document.getElementById('sidebar');
                let mainContent = document.getElementById('main-content');

                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('full-width');
            });
        });
    </script>
</x-app-layout>
