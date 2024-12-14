<div class="content-card">
    <h2>Comments</h2>
    @auth
        @include('partials.comment-form')
    @endauth
    <div id="comments-container">
        @foreach ($story['comments'] as $comment)
            <div class="comment-card">
                <h6>{{ $comment['user']['name'] ?? 'Anonymous' }}</h6>
                <p>{{ $comment['content'] }}</p>
                <small class="text-muted">
                    {{ \Carbon\Carbon::parse($comment['created_at'])->diffForHumans() }}
                </small>
            </div>
        @endforeach
    </div>
</div>
