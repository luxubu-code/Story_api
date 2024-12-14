<div class="chapter-list-header">
    <h2>Upload New Chapter</h2>
</div>
<form method="POST" action="{{ route('stories.upload', $story['id']) }}" enctype="multipart/form-data">
    @csrf
    <div class="mb-3">
        <label for="title" class="form-label">Chapter Title</label>
        <input type="text" class="form-control" id="title" name="title" required>
    </div>
    <div class="mb-3">
        <label for="file_zip" class="form-label">Upload Images (ZIP File)</label>
        <input type="file" class="form-control" id="file_zip" name="file_zip" accept=".zip" required>
    </div>
    <button type="submit" class="btn btn-primary">Upload Chapter</button>
</form>
