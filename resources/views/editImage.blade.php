@extends('layouts.blog3', ['title' => 'Редактирование галереи'])

@section('content')
    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 portfolio-item">
        <a class="button blue" href="{{ route('girlsEditAuchAnket') }}">Редактировать анкету</a><br>
        <div class="container gallery-container">


            <div class="row">
                Загрузить фотографию:
                <form action="{{route('uploadImage')}}" method="post" enctype="multipart/form-data" novalidate>
                    {{ csrf_field() }}
                    <input type="file"  id="file"  name="file" accept="image/x-png,image/gif,image/jpeg" value="{{ old('file')}}" required>
                    @if($errors->has('file'))
                        <font color="red"><p>  {{$errors->first('file')}}</p></font>
                    @endif
                    <button type="submit" class="btn btn-default">Загрузить фотографию</button>
                </form>
            </div>
            <br>
            <div class="container gallery-container">
                <div class="tz-gallery">
                    <div class="row">

                        @foreach($images as $image)
                            <div class="col-lg-7 col-md-4 col-sm-4 col-xs-4 portfolio-item">
                                <img  height="250" src="<?php echo asset("public/images/upload/$image->photo_name")?>">
                                <a class="button blue" href="{{route('deleteImage',['id'=>$image->photo_name])}}" role="link">Удалить</a>
                            </div>
                        @endforeach

                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- Placed at the end of the document so the pages load faster -->


@endsection