@extends('layouts.blog', ['title' => 'Управление подарками'])
@section('content')
    <div class="card-body" id="app4">
        <presents :user="{{auth()->user()}}"></presents>
    </div>
@endsection