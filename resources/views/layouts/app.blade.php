app.blade.php
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- Scripts -->
    <script src="{{ mix('js/app.js') }}" defer></script>

    <style>
        .styled-table {
            border-collapse: collapse;
            margin: 25px 0;
            font-size: 0.9em;
            font-family: 'Roboto', sans-serif;
            inline-size: 100%;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
            border-radius: 10px;
            overflow: hidden;
        }

        .styled-table thead tr {
            background-color: #6a0dad;
            text-align: start;
            color: #ffffff;
        }

        .styled-table th,
        .styled-table td {
            padding: 12px 15px;
            border-block-end: 1px solid #dddddd;
        }

        .styled-table tbody tr:nth-of-type(even) {
            background-color: #f3f3f3;
        }

        .styled-table tbody tr:hover {
            background-color: #e9e9ff;
            transition: background-color 0.3s;
        }

        .styled-table tbody tr.active-row {
            font-weight: bold;
            color: #6a0dad;
        }

        .text-black {
            color: black;
        }

        body {
            display: flex;
            min-block-size: 100vh;
            margin: 0;
            overflow: hidden;
            font-family: 'Roboto', sans-serif;
            justify-content: center;
        }

        .sidebar {
            inline-size: 16rem;
            transition: width 0.3s;
            block-size: 100vh;
            background-color: #4b0082;
            color: white;
            position: fixed;
            inset-inline-start: 0;
            inset-block-start: 0;
            inset-block-end: 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
            z-index: 10;
        }

        .sidebar.collapsed {
            inline-size: 4rem;
        }

        .main-content {
            flex: 1;
            transition: margin-left 0.3s;
            margin-inline-start: 2rem;
        }

        .table-content {
            flex: 1;
            transition: margin-left 0.3s;
            padding: 1rem;
        }

        .sidebar .toggle-btn {
            background-color: #7b68ee;
            color: white;
            padding: 1rem;
            text-align: center;
            cursor: pointer;
            border-block-end: 1px solid #9370db;
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
            background-color: #9370db;
            padding-inline-start: 1.5rem;
        }

        .main-content .header {
            padding: 1rem;
            margin-block-end: 1rem;
            font-size: 1.5rem;
            font-weight: bold;
            background-color: #7b68ee;
            border-block-end: 1px solid #9370db;
        }

        .sidebar .logo {
            text-align: center;
            padding: 1rem;
            font-size: 1.5rem;
            font-weight: bold;
            background-color: #7b68ee;
            border-block-end: 1px solid #9370db;
        }

        .create-button {
            display: inline-block;
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            text-align: center;
            text-decoration: none;
            font-size: 16px;
            font-weight: bold;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s, transform 0.3s;
        }

        .create-button:hover {
            background-color: #45a049;
            transform: scale(1.05);
        }

        .action-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 1rem 0;
        }

        .search-bar {
            display: flex;
            align-items: center;
        }

        .search-bar input {
            padding: 0.5rem;
            max-inline-size: 400px;
            border-radius: 5px;
            border: 1px solid #ccc;
            transition: all 0.3s;
        }

        .search-bar button {
            padding: 0.5rem;
            border-radius: 5px;
            background-color: #4CAF50;
            color: white;
            border: none;
            margin-inline-start: 0.5rem;
            transition: all 0.3s;
        }

        .search-bar button:hover {
            background-color: #45a049;
        }

        .action-buttons button {
            border-radius: 12px;
            padding: 8px 16px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
        }

        .action-buttons .edit-button {
            background-color: #E0F7FA;
            color: #00796B;
        }

        .action-buttons .edit-button:hover {
            background-color: #B2EBF2;
            transform: scale(1.05);
        }

        .action-buttons .delete-button {
            background-color: #FFEBEE;
            color: #C62828;
        }

        .action-buttons .delete-button:hover {
            background-color: #FFCDD2;
            transform: scale(1.05);
        }

        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-block-start: 20px;
            font-family: 'Roboto', sans-serif;
        }

        .pagination a,
        .pagination span {
            color: #6a0dad;
            padding: 8px 16px;
            margin: 0 4px;
            border-radius: 5px;
            border: 1px solid #6a0dad;
            text-decoration: none;
            transition: background-color 0.3s, color 0.3s;
        }

        .pagination a:hover {
            background-color: #6a0dad;
            color: #ffffff;
        }

        .pagination .active {
            background-color: #6a0dad;
            color: #ffffff;
            border: 1px solid #6a0dad;
        }
    </style>

</head>

<body>
    <div class="sidebar" id="sidebar">
        <div class="logo">SEGGAY</div>
        <ul>
            <li><a href="{{ route('stories') }}">📚 Quản lý truyện</a></li>
        </ul>
    </div>

    <div class="main-content" id="main-content">
        <div class="header">Danh sách truyện</div>
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
