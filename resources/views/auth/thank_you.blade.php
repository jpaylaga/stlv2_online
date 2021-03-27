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
                                            <h2 class="white mb-4">Thank You, {{$user->firstname}}!</h2>
                                            <div class="white">
                                                @if( $user->deleted )
                                                    <p>We will contact you via this number: <span class="underline">{{$user->phone}}</span> to confirm your registration.</p>
                                                @else
                                                    <p>You can now login to your account using your username and password. <a href="/login" class="underline">Go to Login</a></p>
                                                @endif
                                                <small class="mt-4">For questions and enquiries, please contact: <a href="tel:09123456789">0912-345-6789</a>.</small>
                                            </div>
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
