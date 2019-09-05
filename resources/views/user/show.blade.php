@extends('layouts/app')

@section('title', $user->name)

@section('content')
    <div class="card">
        <div class="card-header">ユーザー情報</div>
        <div class="card-body">
            <div class="row">
                <div class="col-4">
                    <img src="{{ $user_profile_path }}" alt="user_profile_image" height="320" width="320">
                    @if($login_user && !$login_user->isMyself($user))
                        @if($login_user->isFollowing($user))
                            <form action="{{ route('user.unfollow', ['id' => $user->id], false) }}" method="post">
                                @csrf
                                <input type="hidden" name="follow" value="true">
                                <button type="button" class="btn btn-danger form-button">unfollow</button>
                            </form>
                        @else
                            <form action="{{ route('user.follow', ['id' => $user->id], false) }}" method="post">
                                @csrf
                                <input type="hidden" name="follow" value="true">
                                <button type="button" class="btn btn-primary form-button">follow</button>
                            </form>
                        @endif
                    @endif
                </div>
                <div class="col-8">
                    <table class="table table-borderd">
                        <thead>
                        <tr>
                            <th scope="row">name</th>
                            <td>{{ $user->name }}</td>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <th scope="row">email</th>
                            <td>
                                {{  $user->email }}
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-4">
            <div class="card">
                <div class="card-header">フォロワー({{ $followers->count() }})</div>
                <div class="card-body">
                    @foreach($followers as $follower)
                        <a href="{{ route('user.show', ['id' => $follower->id], false) }}">
                            <img class="rounded-circle" src="{{ $follower->profile_image }}" alt="" height="35" width="35" data-toggle="tooltip" data-placement="top" title="Tooltip on top">
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-8">
            <div class="card">
                <div class="card-body">
{{--                    TODO: 掲示版、コメント、と余裕があればアクティビティ全部--}}
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">掲示版</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="profile-tab" data-toggle="tab" href="#like" role="tab" aria-controls="like" aria-selected="false">いいね</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="contact-tab" data-toggle="tab" href="#favorite" role="tab" aria-controls="favorite" aria-selected="false">お気に入り</a>
                        </li>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">a</div>
                        <div class="tab-pane fade" id="like" role="tabpanel" aria-labelledby="profile-tab">
{{--                            TODO: フロントでページネーションを実装する --}}
                            <ul class="list-group">
                                @foreach($likes as $like)
                                    <li class="list-group-item">
                                        <a href="{{ route('board.show', ['id' => $like->id], false) }}">
                                            {{ $like->title }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="tab-pane fade" id="favorite" role="tabpanel" aria-labelledby="contact-tab">c</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection