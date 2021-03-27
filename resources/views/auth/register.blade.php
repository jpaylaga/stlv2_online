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
                                <div class="card gradient-indigo-purple text-center width-600">
                                    <div class="card-img overlap">
                                        <img alt="element 06" class="mb-1" src="{{ asset('/images/logo.png') }}" width="190">
                                    </div>
                                    <div class="card-body">
                                        <div class="card-block">
                                            <h2 class="white mb-4">Register your Account</h2>

                                            <form method="POST" action="{{ route('web-register') }}">
                                                @csrf

                                                @if( app('request')->input('referral') )
                                                <input id="referral" type="hidden" name="referral" value="{{app('request')->input('referral')}}">
                                                @endif

                                                <div class="form-group row">
                                                    <label for="username" class="col-md-4 col-form-label text-md-left white">{{ __('Username') }}</label>
                                                    <div class="col-md-8">
                                                        <input id="username" type="text" class="form-control @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}" required>
                                                        @error('username')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label for="firstname" class="col-md-4 col-form-label text-md-left white">{{ __('First Name') }}</label>
                                                    <div class="col-md-8">
                                                        <input id="firstname" type="text" class="form-control @error('firstname') is-invalid @enderror" name="firstname" value="{{ old('firstname') }}" required>
                                                        @error('firstname')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label for="lastname" class="col-md-4 col-form-label text-md-left white">{{ __('Last Name') }}</label>
                                                    <div class="col-md-8">
                                                        <input id="lastname" type="text" class="form-control @error('lastname') is-invalid @enderror" name="lastname" value="{{ old('lastname') }}" required>
                                                        @error('lastname')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label for="phone" class="col-md-4 col-form-label text-md-left white">{{ __('Phone Number') }}</label>
                                                    <div class="col-md-8">
                                                        <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone') }}">
                                                        @error('phone')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label for="facebook" class="col-md-4 col-form-label text-md-left white">{{ __('Facebook Name') }}</label>
                                                    <div class="col-md-8">
                                                        <input id="facebook" type="text" class="form-control" name="facebook" value="{{ old('facebook') }}">
                                                    </div>
                                                </div>

                                                <!-- <div class="form-group row">
                                                    <label for="email" class="col-md-4 col-form-label text-md-left white">{{ __('E-Mail Address') }}</label>
                                                    <div class="col-md-8">
                                                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}">
                                                        @error('email')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div> -->

                                                <div class="form-group row">
                                                    <label for="password" class="col-md-4 col-form-label text-md-left white">{{ __('Password') }}</label>
                                                    <div class="col-md-8">
                                                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" value="{{ old('password') }}" required>
                                                        @error('password')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label for="password-confirm" class="col-md-4 col-form-label text-md-left white">{{ __('Confirm') }}</label>
                                                    <div class="col-md-8">
                                                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" value="{{ old('password_confirmation') }}" required>
                                                    </div>
                                                </div>

                                                <div class="form-group mt-4">
                                                    <div class="col-md-12">
                                                        <button type="submit" class="btn btn-success btn-block btn-raised">Register</button>
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
