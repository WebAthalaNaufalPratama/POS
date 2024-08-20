@extends('layouts.auth-master')

@section('content')
    <form method="post" action="{{ route('login.perform') }}">
        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
        <!-- <img class="mb-4" src="{!! url('images/bootstrap-logo.svg') !!}" alt="" width="72" height="57"> -->
        <div class="form-holder text-center">
        <img src="/assets/img/logo.png" width="200px">
        </div>
        
        <h1 class="h3 mb-3 fw-normal">Login</h1>

        @include('layouts.partials.messages')

        <div class="form-holder">
            <span class="fa fa-user fa-2x" aria-hidden="true"></span>
            <input type="text" class="form-signin" name="username" value="{{ old('username') }}" placeholder="Username" required="required" autofocus>
            <label for="floatingName">Email or Username</label>
            @if ($errors->has('username'))
                <span class="text-danger text-left">{{ $errors->first('username') }}</span>
            @endif
        </div>
        
        <div class="form-holder">
            <span class="fa fa-lock fa-2x" aria-hidden="true"></span>
            <input type="password" class="form-signin" name="password" value="{{ old('password') }}" placeholder="Password" required="required">
            <label for="floatingPassword">Password</label>
            @if ($errors->has('password'))
                <span class="text-danger text-left">{{ $errors->first('password') }}</span>
            @endif
        </div>

        <!-- <div class="form-holder">
            <label for="remember">Remember me</label>
            <input type="checkbox" name="remember" value="1">
        </div> -->

        <button class="w-100 btn btn-lg btn-primary" type="submit">Login</button>
        
        <!-- @include('auth.partials.copy') -->
    </form>
@endsection


