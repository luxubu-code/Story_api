<div class="d-flex justify-content-between mb-3">
    <a href="{{ route('stories.index') }}" class="btn btn-secondary">Back</a>
    <form method="POST" action="{{ route('stories.destroy', $story['id']) }}"
        onsubmit="return confirm('Are you sure you want to delete this story?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger">Delete Story</button>
    </form>
</div>
