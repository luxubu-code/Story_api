@props(['title' => ''])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">

    <!-- Scripts -->
    <script src="{{ mix('js/app.js') }}" defer></script>
</head>
<body>
    <div class="sidebar" id="sidebar">
        <div class="logo">MyLogo</div>
        <div class="toggle-btn" id="sidebar-toggle">☰</div>
        <ul>
            <li><a href="{{ route('dashboard') }}">🏠 Home</a></li>
            <li><a href="{{ route('admin.stories') }}">📚 Quản lý truyện</a></li>
            <!-- Các liên kết khác -->
        </ul>
    </div>

    <div class="main-content" id="main-content">
        {{ $slot }}
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
</body>
</html>
