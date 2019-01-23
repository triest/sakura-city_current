@extends('layouts.blog3', ['title' => 'Запросы на открытие анкеты'])


@section('content')

    <b>Запросы на открытие доступа к странице:</b>
    <? $count = 1 ?>
    @foreach($requwest as $reg)
        <b> {{$count}}. </b>
        <? $girl = $reg->getWho() ?>
        <b> <a href="{{route('showGirl',['id'=>$girl->id])}}">{{$girl->name}}</a></b> <br>

        <img height="250" src="<?php echo asset("public/images/upload/$girl->main_image")?>"></img></a>
        <br>
        <b><a href="{{route('makeAccess',['id'=>$girl->id])}}">Открыть доступ к приватной информации в анкете</a></b>
        <br>
        <b><a href="{{route('denideAccess',['id'=>$girl->id])}}">Отказать в доступе</a></b>
    @endforeach
    <br>
    <b>Мои запросы:</b> <br>
    <? $count = 1 ?>
    @if($myRequwest!=null)
        @foreach($myRequwest as $reg)

            <b> {{$count}}. </b>
            <? $girl = $reg->getWho() ?>
            <b> <a href="{{route('showGirl',['id'=>$girl->id])}}">{{$girl->name}}</a></b> <br>

            <img height="250" src="<?php echo asset("public/images/upload/$girl->main_image")?>"></img></a>
            <br>
            @if($reg->rezult=='not_dispersed')
                <b>Не рассмотрен</b>
            @elseif ($reg->rezult=='accepted')
                <b>Принят</b>
            @else
                <b>Отклонён</b>
            @endif


        @endforeach
    @endif
@endsection