        <div class="row story-details">
            <div class="col-md-4">
                <img src="{{ $story['image_path'] }}" alt="{{ $story['title'] }}" alt="Static Test Image" class="img-fluid">
            </div>
            <div class="col-md-8">
                <h1>{{ $story['title'] }}</h1>
                <p><strong>Author:</strong> {{ $story['author'] }}</p>
                <p><strong>Description:</strong> {{ $story['description'] }}</p>
                <p><strong>Views:</strong> {{ $story['views'] }}</p>
                <p><strong>Average Rating:</strong> {{ $story['averageRating'] }}</p>
                <p><strong>Categories:</strong>
                    @foreach ($story['categories'] as $category)
                        <span class="badge">{{ $category['title'] }}</span>
                    @endforeach
                </p>
                <p><strong>Status:</strong> {{ $story['status'] }}</p>
            </div>
        </div>
