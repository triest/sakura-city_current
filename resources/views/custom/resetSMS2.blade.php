@extends('layouts.blog', ['title' => 'Подтверждение телефоного номера'])

@section('content')
    <div class="row">
        <div class="card-body" id="phoneApp">
            <phoneComponent></phoneComponent>
        </div>
    </div>
    <a class="button blue" href="{{route('main')}}" role="link">К списку анкет</a>
@endsection
