@extends('layouts.blog3', ['title' => 'Регистрация'])


@section('content')



    <div class='container text--center join--title'>
        <h1>Регистрация аккаунта</h1>
    </div>
    <div class='wrapper wrapper--login'>

        <div class=''>
            <div class='panel-body'>

                <form action="{{route('joinStore')}}" class='form-horizontal joinForm' role='form' id='joinForm' method='POST' >
                    {{ csrf_field() }}
                    <input type='hidden' name='step' value='2'>
                    <input type='hidden' name='join' value='yes'>
                    <div id='mail_selection_div' class='card island' style='display: block;'>
                        <ul>
                            <div class='FormGroup'>
                                <label>Вы
                                    <div class='ControlGroup'>

                                        <div class='select'>
                                            <select name='you'>
                                                <option value='male'>Мужчина</option>
                                                <option value='famele'>Женщина</option>
                                            </select>
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <label>Ищете</label>

                            <div class='FormGroup'>
                                <div class='ControlGroup'>
                                    <div style='line-height: 40px;'>
                                        <div class='select'>
                                            <select name='kogo'>
                                                <option value='famale'>Женщину</option>
                                                <option value='male'>Мужчину</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <label>Ваше имя</label>
                            <input type='text' name='name' class='text-input  ' value='' required>

                            <p class='mailHint'></p>
                            <label>Ваш email</label>
                            <input type='text' name='email' class='text-input  ' value='' required>


                            <p class='mailHint'></p>
                            <label>Пароль</label>
                            <input type='text' name='password' class='text-input  ' value='' required>


                            <p class='mailHint'></p>

                            <span id='suggestion'></span>

                                <button class='submit'>Создать анкету</button>
                                <div>
                                    <p class='Login-helpText'>Продолжая, вы соглашаетесь с <a href=/rules
                                                                                              rel='nofollow'>правилами</a>
                                        сайта Sakura-city и подтверждаете что вам больше 18 лет. Запрещается
                                        продвигать незаконную коммерческую деятельность (например,
                                        проституцию). </p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </form>
            </div>
        </div>
    </div>



@endsection