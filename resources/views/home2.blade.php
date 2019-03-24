@extends('layouts.blog', ['title' => 'Диалоги'])

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">We Code Messenger</div>

                    <div class="card-body" id="app">
                        <chat-app2 :user="{{auth()->user()}}"></chat-app2>
                    </div>
                </div>
            </div>
        </div>
        <a class="button blue" href="{{route('main')}}" role="link" onclick=" relocate_home()">К списку анкет</a>
    </div>
@endsection