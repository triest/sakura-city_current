@extends('layouts.blog', ['title' => 'Управление анкетой'])

@section('content')
    <div class="card-body" id="myAnketApp">
        <myanket :user="{{auth()->user()}}"></myanket>
    </div>
@endsection