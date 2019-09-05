<div class="media comment p-2">
    <a class="text-dark mr-2 text-decoration-none" href="{{ route('user.show', ['id' => $comment->user->id], false) }}">
        <img src="{{ get_user_profile_image($comment->user->image_path) }}" class="mr-3" alt="{{ $comment->user->name }}" width="30" height="30">
    </a>
    <div class="media-body comment-content">
        <h5 class="mt-0">
            <a class="text-dark mr-2 text-decoration-none" href="{{ route('user.show', ['id' => $comment->user->id], false) }}">
                {{ $comment->user->name }}
            </a>
            <time class="text-muted comment-time"><i class="far fa-clock mr-1"></i>{{ $comment->created_at->isoFormat('Y/M/D H:m:s') }}</time>
        </h5>
        {{ $comment->content }}
    </div>
</div>