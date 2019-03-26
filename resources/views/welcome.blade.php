<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>CMMS</title>
        <link rel="shortcut icon" href="/img/favicon.png">
        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="/css/app.css">

        <!-- Styles -->
        <style>
            html, body {
                /* background-image: linear-gradient(180deg, #08a4a7, #0388e6); */
                background-image: linear-gradient(-38deg, #08a4a7, #ffffff);
                /* background-image: url('./img/landing.jpg');
                background-repeat: no-repeat;
                background-size: cover; */
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 200;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 13px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
            .text-white{
                color: #fff !important;
            }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            @if (Route::has('login'))
                <div class="top-right links">
                    @auth
                        <a class="text-white" href="{{ url('/dashboard') }}">Home</a>
                    @else
                        <a class="text-white" href="{{ route('login') }}">Login</a>

                        {{-- @if (Route::has('register'))
                            <a class="text-white" href="{{ route('register') }}">Register</a>
                        @endif --}}
                    @endauth
                </div>
            @endif

            <div class="content">
                <div class="title m-b-md text-white">
                    {{ config('app.name')}}
                </div>
                <div class="text-white">
                    <a href="/view_floorplan" class="btn bgc-teal text-white"><h3><i class="fas fa-map-marked"></i> View Floorplan</h3></a>
                    <a href="/view_schedule" class="btn btn-primary"><h3><i class="fas fa-clock"></i> Sections Schedule</h3></a>
                    <a href="/view_schedule_professor" class="btn bgc-purple text-white"><h3><i class="fas fa-chalkboard-teacher"></i> Professor Schedule</h3></a>
                </div>
            </div>
        </div>
    </body>
</html>
