@extends('layouts.blog', ['title' => 'Мои подарки'])
@section('content')
    <div class="card-body" id="app4">
        <mypresents :user="{{auth()->user()}}"></mypresents>
    </div>
@endsection