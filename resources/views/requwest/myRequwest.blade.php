@extends('layouts.blog3', ['title' => 'Запросы на открытие анкеты'])


@section('content')

    <? $count=1 ?>
    @foreach($requwest as $reg)
        <b> {{$count}}. </b>
        <? $girl=$reg->getWho() ?>
        <b> <a href="{{route('showGirl',['id'=>$girl->id])}}">{{$girl->name}}</a></b> <br>

        <img height="250" src="<?php echo asset("public/images/upload/$girl->main_image")?>"></img></a>
        <br>
        <b><a href="{{route('makeAccess',['id'=>$girl->id])}}">Открыть доступ к приватной информации в анкете</a></b> <br>
        <b><a href="{{route('showGirl',['id'=>$girl->id])}}">Отказать в доступе</a></b>
    @endforeach
@endsection