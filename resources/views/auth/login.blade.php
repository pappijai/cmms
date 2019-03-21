<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>CMMS</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="/css/app.css">
    <link rel="stylesheet" href="/css/login-style.css">
    <link rel="shortcut icon" href="./img/favicon.png">
    <style>
        html, body {
            background-image: linear-gradient(-38deg, #08a4a7, #ffffff) !important;
        }        
    </style>
</head>
<body class="bg-blue">
    <div class=" d-flex align-content-center flex-wrap" id="app">
        <div class="container">
            <div class="row">
                <div class="col-md-6 pt-5">
                    <img src="./img/3d-login.png">    
                </div>
                <div class="col-md-6">
                    <div class="login-content">
                        <h1 class="text-white text-center">LOGIN</h1>
                        <div class="login-form">
                            <form method="POST" action="{{ route('login') }}">
                                @csrf
                                <div class="form-group">
                                    <label>Email</label>
                                    {{-- <input type="text" class="form-control" placeholder="E-mail Address"> --}}
                                    <input id="email" type="email" placeholder="E-mail Address" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" autofocus>

                                    @if ($errors->has('email'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                    <div class="form-group">
                                        <label>Password</label>
                                        {{-- <input type="password" class="form-control" placeholder="Password"> --}}
                                        <input id="password" placeholder="Password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password">

                                        @if ($errors->has('password'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('password') }}</strong>
                                            </span>
                                        @endif                                                             
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                        <label class="form-check-label" for="remember">
                                            {{ __('Remember Me') }}
                                        </label>
                                    </label>

                                </div>
                                <button type="submit" class="btn but">Sign in</button>
                            </form>
                        </div>
                    </div>
                 </div>
            </div>  
        </div>
    </div>
    <script src="/js/app.js"></script>

</body>
</html>