@extends('layouts.app')

@section('content')
<div class="wrapper">
    <div class="main-panel">
        <div class="main-content">
            <div class="content-wrapper">
                <!--Login Page Starts-->
                <section id="login">
                    <div class="container-fluid">
                        <div class="row full-height-vh">
                            <div class="col-12 d-flex align-items-center justify-content-center">
                                <div class="card gradient-indigo-purple text-center width-400">
                                    <div class="card-img overlap">
                                        <img alt="element 06" class="mb-1" src="{{ asset('/images/logo.jpg') }}" width="190">
                                    </div>
                                    <div class="card-body">
                                        <div class="card-block">
                                            <h2 class="white">Login</h2>

                                            <form method="POST" action="{{ route('login') }}">
                                                @csrf

                                                <div class="form-group">
                                                    <div class="col-md-12">
                                                        <input id="login" type="text"
                                                            class="form-control{{ $errors->has('username') || $errors->has('email') ? ' is-invalid' : '' }}"
                                                            name="login" value="{{ old('username') ?: old('email') }}" required autofocus>
                                                
                                                        @if ($errors->has('username') || $errors->has('email'))
                                                            <span class="invalid-feedback">
                                                                <strong>{{ $errors->first('username') ?: $errors->first('email') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <div class="col-md-12">
                                                        <input type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" id="inputPass" placeholder="Password" required>
                                                        @if ($errors->has('password'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('password') }}</strong>
                                                        </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="custom-control custom-checkbox mb-2 mr-sm-2 mb-sm-0 ml-3">
                                                                <input type="checkbox" class="custom-control-input" id="rememberme" {{ old('remember') ? 'checked' : '' }}>
                                                                <label class="custom-control-label float-left white" for="rememberme">Remember Me</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <div class="col-md-12">
                                                        <button type="submit" class="btn btn-pink btn-block btn-raised">Submit</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!--Login Page Ends-->
            </div>
        </div>
    </div>
</div>
@endsection