@extends('layouts.blog', ['title' => $girl->name])



@section('content')

    <br>
    <br>
    <div class="col-sm-4">
        <img width="200" src="<?php echo asset("/images/upload/$girl->main_image")?>">
        <br>
        {{$girl->status}}
        @if (Auth::guest())
        @else

            @if($girl->user_id!=auth()->user()->id and Auth::user()->anketisExsis()!=null)
                <br>
                <div class="card-body" id="app7">
                    <br>
                    <privatepanel :id="{{$girl->id}}" :user_id="{{$girl->user_id}}"></privatepanel>
                </div>
            @else
                <br>
                <b><a href="{{route('createGirlPage')}}">Создайте анкету</a> что-бы общаться и смотреть закрытые данные!</b>
            @endif
            @if(auth()->user()->get_id()!=$girl->user_id)
            @endif
        @endif

    </div>
    <div class="col-sm-4">
        <h4 class="card-title">
            {{$girl->name}}
        </h4>

        <b>Пол:</b>
        @if($girl->sex=='famele')
            <b> Женский</b>
        @endif

        @if($girl->sex=='male')
            <b> Мужской</b>
        @endif
        <br>

        <p class="card-text"><b>Рост : {{$girl->height}}</b>
        <p class="card-text"><b>Вес : {{$girl->weight}}</b>
        <p class="card-text"><b>Возраст : {{$girl->age}}</b>

        <p class="card-text"><b>Хочу встретиться с :</b> @if($girl->meet=='famele')
                <b> женщиной</b>
            @endif

            @if($girl->meet=='male')
                <b> мужчиной</b>
            @endif
            <br>
            <b>Цели знакомства:</b> <br>

        @foreach($targets as $target)
            <li>{{$target->name}}</li>
        @endforeach

        <p class="card-text"><b> {!!$girl->description  !!}</b></p>

        @if($girl->private!=null)
            <label>Приватное сообщение:</label>
            <p class="card-text>"><b>{!!$girl->private  !!}<</b></p>
        @endif
        <br>

        <br>
        <div class="container gallery-container">
            <div class="tz-gallery">

                <div class="row">
                    @foreach($images as $image)
                        <div class="col-sm-5 col-md-5">
                            <a class="lightbox" href="<?php echo asset("/images/upload/$image->photo_name")?>">
                                <img height="150" src="<?php echo asset("/images/upload/$image->photo_name")?>">
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        @if($girl->private!=null)
            <label>Приватное сообщение:</label>
            <p class="card-text>"><b>{!!$girl->private  !!}<</b></p>
            @if (empty($privatephotos))
            @else
                <label>Приватные фотографии:</label>
                <div class="container gallery-container">
                    <div class="tz-gallery">
                        <div class="row">
                            @foreach($privatephotos as $image)
                                <div class="col-sm-7 col-md-7">
                                    <a class="lightbox" href="<?php echo asset("/images/upload/$image->photo_name")?>">
                                        <img height="200" width="200"
                                             src="<?php echo asset("/images/upload/$image->photo_name")?>">
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        @endif
        <br>
        <a class="btn btn-primary" href="{{route('main')}}" role="link" onclick=" relocate_home()">К списку анкет</a>
    </div>


@endsection