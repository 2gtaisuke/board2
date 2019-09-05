@extends('layouts/app')

@section('title', $board->title)

@section('content')
    @include('board._board', ['is_displayed_read_more' => false])
    <div class="pagination-link mt-3">
        {{ $board->comments->links() }}
    </div>
    <div class="card">
        <div class="card-header">コメント</div>
        <div class="card-body">
            <form class="create-comment-form" action="{{ route('comment.store', ['id' => $board->id], false) }}" method="post">
                @csrf
                @include('board._comment_form')
                <div class="form-button-wrapper text-right">
                    <button type="submit" class="btn btn-primary">投稿する</button>
                </div>
            </form>
        </div>
    </div>
@endsection