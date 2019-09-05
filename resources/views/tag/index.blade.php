@extends('layouts/app')

@section('title', '掲示版一覧')

@section('content')
    <ul class="list-group">
    @foreach($tags as $tag)
            <a href="{{ route('tag.show_board', ['tag_name' => $tag->name], false) }}">
                <li class="list-group-item">{{ $tag->name }} ({{ $tag->board_count }})</li>
            </a>
    @endforeach
    </ul>
@endsection