@extends('app')

@section('title', $story['title'] . ' - Story Details')

@push('styles')
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f8ff;
            /* Nền xanh nhạt */
            color: #333;
        }

        .story-details {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 25px rgba(0, 0, 0, 0.1);
            margin-bottom: 40px;
            transition: box-shadow 0.3s ease-in-out;
        }

        .story-details h1 {
            font-size: 2.7rem;
            font-weight: 800;
            color: #007bff;
            /* Màu xanh nổi bật */
        }

        .story-details p {
            font-size: 1.2rem;
            margin-bottom: 1.2rem;
            line-height: 1.6;
        }

        .story-details img {
            border-radius: 12px;
            object-fit: cover;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .story-details p strong {
            color: #0056b3;
            /* Màu chữ đậm */
        }

        .badge {
            background-color: #17a2b8;
            /* Đổi màu huy hiệu */
            color: #ffffff;
            padding: 0.6rem;
            font-size: 1rem;
            font-weight: 500;
            margin-right: 7px;
            border-radius: 5px;
        }

        .chapter-list-header h2 {
            font-size: 2rem;
            color: #007bff;
            font-weight: 700;
        }

        .list-group-item {
            padding: 18px;
            border-radius: 10px;
            background-color: #f9f9f9;
            margin-bottom: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            transition: background-color 0.3s ease;
        }

        .list-group-item:hover {
            background-color: #f0f8ff;
            transform: scale(1.01);
            transition: transform 0.2s ease-in-out;
        }

        .btn-danger {
            padding: 0.35rem 0.8rem;
            font-size: 0.875rem;
            background-color: #dc3545;
        }

        .btn-primary {
            padding: 0.5rem 1rem;
            background-color: #007bff;
            border: none;
            font-size: 1rem;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }
    </style>
@endpush

@section('content')

    <!-- Back and Delete Buttons -->
    @include('stories.partials.back_delete_buttons', ['story' => $story])

    <!-- Story Details -->
    @include('stories.partials.story_details', ['story' => $story])

    <!-- Flash Messages -->
    @include('stories.partials.flash_messages')

    <!-- Chapters List -->
    @include('stories.partials.chapter_list', ['story' => $story])

    <!-- Upload New Chapter -->
    @include('stories.partials.upload_chapter', ['story' => $story])

@endsection
