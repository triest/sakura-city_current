@extends('layouts.blog3', ['title' => 'Анкета создана!'])


@section('content')
    <b>Ваша анкета создана.</b>
    <a class="button blue" href="{{route('continion')}}" role="link">Продолжить заполнять анкету</a>
@endsection