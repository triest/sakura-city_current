@extends('layouts.blog', ['title' => 'Создание анкеты'])
@section('content')


    <form action="{{route('storeGirl')}}" method="post" enctype="multipart/form-data">
        {{ csrf_field() }}
        <br>
        <div class="form-group">
            <label for="title">Имя:</label>
            <input type="text" class="form-control" id="name" name="name" placeholder="Имя" value="{{$username}}"
                   readonly required>
        </div>

        @if($errors->has('name'))
            <font color="red"><p>  {{$errors->first('name')}}</p></font>
        @endif


        <div class="control-group2" ng-class="{true: 'error'}[submitted && form.pas.$invalid]">
            <div class="form-group">
                <label for="phone">Ваш телефон:</label>
                <!--   <input type="tel" class="form-control" id="phone" name="phone" pattern="^\(\d{3}\)\d{3}-\d{2}-\d{2}$" required></input>-->

            </div>
        </div>
        <b> Пол:</b> <br>
        <input type="radio" id="contactChoice1"
               name="sex" value="famele" checked>
        <label for="contactChoice1">Женский</label>

        <input type="radio" id="contactChoice2"
               name="sex" value="male">
        <label for="contactChoice2">Мужской</label>

        <br>
        <label for="age">Возраст:
            <input type="number" name="age" min="18" value="18" onkeypress="return isNumber(event)" checked>
        </label><br>
        @if($errors->has('age'))
            <font color="red"><p>  {{$errors->first('age')}}</p></font>
        @endif

        <label for="age">Рост:
            <input type="number" name="height" min="100" value="160" onkeypress="return isNumber(event)">
        </label><br>
        <label for="age">Вес:
            <input type="number" name="weight" min="45" step="1" value="50"
                   pattern="[^@]+@[^@]+\.[0-9]{2,3}" onkeypress="return isNumber(event)">
        </label><br>


        <b> С кем хотите познакомиться:</b> <br>
        <input type="radio" id="contactmet"
               name="met" value="famele">
        <label for="contactChoice1">c женщиной</label>
        <br>
        <input type="radio" id="contactmet2"
               name="met" value="male" checked>
        <label for="contactChoice2">с мужчиной</label>
        <br>
        <br>

        <b>Цель знакомства:</b> <br>
        @foreach($tagrets as $target)
            <input class="form-check-input" type="checkbox" value="{{$target->id}}" name="target[]"  >
            <label class="form-check-label" for="{{$target->id}}">{{$target->name}}</label>
            <br>
        @endforeach

        <label>Выберите заглавную фотографию</label>

        <input type="file" id="file" accept="image/*" name="file" value="{{ old('file')}}" required>
        @if($errors->has('file'))
            <font color="red"><p>  {{$errors->first('file')}}</p></font>
        @endif
        <br>


        <br>
        <div class="form-group">
            <label for="exampleInputFile">Текст анкеты:</label><br>
            <textarea name="description" required> </textarea>
        </div>
        @if($errors->has('description'))
            <font color="red"><p class="errors">{{$errors->first('description')}}</p></font>
        @endif

        <div class="form-group">
            <label for="exampleInputFile">Приватный текст, который видно только тем, кому вы откроете доступ:</label>
            <br>
            <textarea name="private" required> </textarea>
        </div>
        @if($errors->has('private'))
            <font color="red"><p class="errors">{{$errors->first('private')}}</p></font>
        @endif

        <input type="hidden" value="{{csrf_token()}}" name="_token">

        <script type="text/javascript">
            $(document).ready(function () {
                $("ul.dropdown-menu input[type=checkbox]").each(function () {
                    $(this).change(function () {
                        var line = "";
                        $("ul.dropdown-menu input[type=checkbox]").each(function () {
                            if ($(this).is(":checked")) {
                                line += $("+ span", this).text() + ";";
                            }
                        });
                        $("input.form-control").val(line);
                    });
                });
            });
        </script>


        <br>
        <script type="text/javascript">
            function isNumber(evt) {
                evt = (evt) ? evt : window.event;
                var charCode = (evt.which) ? evt.which : evt.keyCode;
                if (charCode > 31 && (charCode < 48 || charCode > 57)) {
                    return false;
                }
                return true;
            }

            function isNumber2(evt) {
                evt = (evt) ? evt : window.event;
                return false;
            }
        </script>

        <label>Выберите фотографии для галереи(можно больше одной)</label>
        <input required type="file" class="form-control" name="images[]" accept="image/*" placeholder="Фотографии"
               multiple>
        <br>

        <label>Выберите приватные фотографии для галереи(можно больше одной). Их смогут вилетьтолько те, кому вы
            откроете доступ.</label>
        <input required type="file" class="form-control" name="privateimages[]" accept="image/*"
               placeholder="Фотографии"
               multiple>
        <br>
        <button type="submit" class="btn btn-default">Создать анкету</button>
    </form>
    <br><br>
    <a class="btn btn-primary" href="{{route('main')}}" role="link" onclick=" relocate_home()">К списку анкет</a>
@endsection