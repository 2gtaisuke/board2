<div class="card mt-4 board">
    <div class="card-header">
        <div class="row">
            <div class="col-4">
                {{ $board->title }}
            </div>
            <div class="col-8 text-right">
                <a class="text-dark mr-2 text-decoration-none" href="{{ route('user.show', ['id' => $board->user->id], false) }}">
                    <i class="fas fa-user mr-1"></i>
                    {{ $board->user->name }}
                </a>
                <i class="far fa-clock mr-1"></i><time>{{ $board->created_at->isoFormat('Y/M/D H:m:s') }}</time>
                <i class="fas fa-bars ml-2"  id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i>
                <div class="dropdown-menu dropdown-menu-right board-dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <a class="dropdown-item like-board-button" id="likeBoardButton{{ $board->id }}" data-board-id="{{ $board->id }}">
                        @if($login_user)
                            <i class="@if($board->likes->count() == 0) far @else fas @endif fa-heart mr-2 like-icon" id="likeBoardIcon{{ $board->id }}"></i>いいね
                        @else
                            <i class="far fa-heart mr-2" id="likeBoardIcon{{ $board->id }}"></i>いいね
                        @endif
                    </a>
                    <a class="dropdown-item" id="favoriteBoardButton{{ $board->id }}" data-board-id="{{ $board->id }}">
                        <i class="far fa-bookmark mr-2"></i>お気に入り
                    </a>
                    <a class="dropdown-item" id="deleteBoard" href="" data-toggle="modal" data-target="#modal-{{ $board->id }}">削除する</a>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="tags mb-2">
            @foreach($board->tags as $tag)
                <a href="{{ route('tag.show_board', ['tag_name' => $tag->name], false) }}"><span class="badge badge-success p-2">{{ $tag->name }}</span></a>
            @endforeach
        </div>
{{--        TODO: 現状だと無限に出力できてしまうので直す--}}
        @foreach($board->comments as $comment)
            @include('board._comment')
        @endforeach
        @if($is_displayed_read_more)
            <a href="{{ route('board.show', ['id' => $board->id], false) }}">
                <button class="btn btn-primary float-right mt-3 mr-3">続きを読む</button>
            </a>
        @endif
    </div>
</div>