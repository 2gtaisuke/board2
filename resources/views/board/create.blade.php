@extends('layouts/app')

@section('title', '掲示版作成')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <form class="create-board-form" action="{{ route('board.store', [], false) }}" method="post">
                @csrf
                <div class="form-group">
                    <label for="createBoardFormTitle">タイトル</label>
                    <input type="text" name="board[title]" id="createBoardFormTitle" class="form-control @error('board.title') is-invalid @enderror" value="{{ old('board.title') }}">
                    @if($errors->has('board.title'))
                        <div class="invalid-feedback">
                            {{ $errors->get('board.title')[0] }}
                        </div>
                    @else
                        <small id="boardTitleHelp" class="form-text text-muted">5文字から32文字以内で入力してください</small>
                    @endif
                </div>
                <div class="form-group form-tags">
                    <label for="createBoardFormTags">タグ</label>
                    <input type="text" name="tags" class="form-control">
                </div>
                @include('board._comment_form')
                <div class="form-button-wrapper text-right">
                    <button type="submit" class="btn btn-primary">作成する</button>
                </div>
            </form>
        </div>
    </div>
@endsection