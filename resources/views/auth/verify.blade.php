@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('メールアドレスを確認してください') }}</div>

                <div class="card-body">
                    @if (session('resent'))
                        <div class="alert alert-success" role="alert">
                            {{ __('確認メールを送信しました。ご確認ください。') }}
                        </div>
                    @endif

                    {{ __('まだメール認証がされていません。メールをご確認ください。') }}
                    {{ __('もしもメールが届いていない場合は') }}, <a href="{{ route('verification.resend') }}">{{ __('もう一度送信してください') }}</a>.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
