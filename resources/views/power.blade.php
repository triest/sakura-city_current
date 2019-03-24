@extends('layouts.blog', ['title' => 'Управление анкетой'])

@section('content')
    <div class="card-body" id="powerApp">
        <power :user="{{auth()->user()}}"></power>

        <br>
        <h2>Пополнить счет</h2>
        <b>Яндекс деньги:</b>
        <form method="POST" action="https://money.yandex.ru/quickpay/confirm.xml">
            <!-- настя    <input type="hidden" name="receiver" value="410015938987820"> -->
            <input type="hidden" name="receiver" value="410018886300919"> <!-- я -->
            <input type="hidden" name="label" value="{{Auth::user()->email}}">
            <input type="hidden" name="quickpay-form" value="donate">
            <input type="hidden" name="targets" value="{{Auth::user()->email}}"><br>
            <input name="sum" value="200" data-type="number"><br>
            <input type="hidden" name="comment" value="Введите коментарий если хотите">
            <label><input type="radio" name="paymentType" value="PC">Яндекс.Деньгами</label><br>
            <label><input type="radio" name="paymentType" value="AC" checked>Банковской картой</label><br>
            <input type="submit" value="Пополнить">
        </form>
        <br>
        <a class="btn btn-primary" href="{{route('main')}}" role="link" onclick=" relocate_home()">Назад</a>
    </div>
@endsection