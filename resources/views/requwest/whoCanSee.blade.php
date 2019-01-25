@extends('layouts.blog3', ['title' => 'Кто может видеть мою анкету'])


@section('content')
    <? $count = 1 ?>
    @if($requwest!=null)
        @foreach($girls as $girl)
            <b> <?= $count ?>. </b>
            <a href="{{route('showGirl',['id'=>$girl->id])}}"> <? $count + 1?> <b>{{$girl->name}}</b> <br>
                <img height="250" src="<?php echo asset("public/images/upload/$girl->main_image")?>"></img></a>
            <h4 class="card-title">
                <b> <a href="{{route('clouseAccess',['id'=>$girl->id])}}">Закрыть доступ</a></b>
            </h4>
        @endforeach
    @endif



@endsection