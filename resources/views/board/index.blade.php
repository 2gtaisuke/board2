@extends('layouts/app')

@section('title', '掲示版一覧')

@section('content')
    @foreach($boards as $board)
        @include('.board._board', ['is_displayed_read_more' => true])
    @endforeach
    <div class="pagination-link mt-3">
        {{ $boards->links() }}
    </div>
@endsection
