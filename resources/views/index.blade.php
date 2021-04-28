<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Comfortaa:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('css/style.css')}}">
    <title>MonroGym</title>
</head>
<body>

<div id="app">
    @auth
        <form id="logout-form" action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit">Выйти</button>
        </form>

        @if(\Illuminate\Support\Facades\Auth::user()->coach)
            <index
            coach-name="{{\Illuminate\Support\Facades\Auth::user()->name}}"
            client-name='no'
            user-id="{{\Illuminate\Support\Facades\Auth::user()->id}}"
            coach-user="true"
            ></index>
        @else
            <index
            coach-name="no"
            client-name='{{ \Illuminate\Support\Facades\Auth::user()->name }}'
            user-id="{{\Illuminate\Support\Facades\Auth::user()->id}}"
            coach-user="false"
            ></index>
        @endif
    @endauth
</div>

<script src="{{asset('js/app.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-JEW9xMcG8R+pH31jmWH6WWP0WintQrMb4s7ZOdauHnUtxwoG2vI5DkLtS3qm9Ekf"
        crossorigin="anonymous"></script>
</body>
</html>
