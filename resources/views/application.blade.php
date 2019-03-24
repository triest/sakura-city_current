@extends('layouts.blog', ['title' => 'Заявки на открытие анкеты'])
@section('content')
    <div class="card-body" id="applicationClass">
        <application :user="{{auth()->user()}}"></application>
    </div>
    <b><a class="button blue" href="{{route('main')}}" role="link" onclick=" relocate_home()">К списку анкет</a></b>
@endsection