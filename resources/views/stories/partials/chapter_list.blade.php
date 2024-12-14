<div class="chapter-list-header">
    <h2>Chapters</h2>
</div>
<ul class="list-group mb-4">
    @foreach ($story['chapter'] as $chapter)
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <div>
                <span>{{ $chapter['title'] }}</span>
                <small>Views: {{ $chapter['views'] }} | Created:
                    {{ \Carbon\Carbon::parse($chapter['created_at'])->format('F d, Y') }}
                </small>
            </div>
            <div>
                <!-- Button to view images -->
                <a href="{{ route('chapters.images', $chapter['id']) }}" class="btn btn-primary btn-sm">
                    View Images
                </a>
                <!-- Delete chapter form -->
                <form method="POST" action="{{ route('stories.chapters.destroy', $chapter['id']) }}"
                    style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this chapter?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                </form>
            </div>
        </li>
    @endforeach
</ul>
