@extends('layouts.blog', ['title' => 'Редактировать галлерею'])

@section('content')
    <div class="card-body" id="app3">
        <editimages :user="{{auth()->user()}}"></editimages>
    </div>
    <br>
    <a class="btn btn-primary" href="{{route('main')}}" role="link" onclick=" relocate_home()">Назад</a>
@endsection