@extends('layouts/app')

@section('title', 'test')

@section('content')
    <img src="{{ \Illuminate\Support\Facades\Storage::disk('local')->url('public/buta.jpg') }}" alt="dmo">
@endsection