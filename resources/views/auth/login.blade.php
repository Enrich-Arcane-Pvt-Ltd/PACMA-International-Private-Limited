@extends('layouts.auth')
@php
    $logo=asset(Storage::url('uploads/logo/'));
    $background=asset(Storage::url('uploads/background/'));
@endphp

@section('page-title')
    {{__('Login')}}
@endsection

@section('content')
    <style>
        body {
            background-image: url('{{ $background . '/login_background.webp?v=' . (@filemtime(storage_path('app/public/uploads/background/login_background.webp')) ?: time()) }}');
            background-repeat: no-repeat;
            background-position: center;
            background-size: cover;
        }
        .simple-footer {
            color: #ffffff;
        }
    </style>
    <section class="section">
        <div class="container" style="height: 100vh; display: flex; align-items: center; justify-content: center; max-width: 500px;">
            <div class="row">

                <div class="col-12">
                    <div class="card glass">
                        <div class="login-logo">
                            <img class="img-fluid logo-img" src="{{$logo.'/logo.png'}}" alt="image">
                        </div>

                        <div class="card-header">
                            <h4>{{__('User Login')}}</h4>
                        </div>

                        <div class="card-body">
                            {{Form::open(array('route'=>'login','method'=>'post','id'=>'loginForm','class'=> 'login-form' ))}}
                            <div class="form-group">
                                {{Form::label('email',__('Email'))}}
                                {{Form::text('email',null,array('class'=>'form-control','placeholder'=>__('Enter Your Email')))}}
                                @error('email')
                                <span class="invalid-email text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <div class="d-block">
                                    {{Form::label('password',__('Password'))}}
                                    <div class="position-relative">
                                        {{Form::password('password',array('class'=>'form-control','id'=>'password','placeholder'=>__('Enter Your Password'),'style'=>'padding-right: 42px;'))}}
                                        <button type="button" id="togglePassword" aria-label="Toggle password visibility"
                                            style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: transparent; border: none; color: #64748B; cursor: pointer; padding: 4px 6px;">
                                            <i class="fas fa-eye" id="togglePasswordIcon"></i>
                                        </button>
                                    </div>
                                    @error('password')
                                    <span class="invalid-password text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="remember">{{__('Remember Me')}}</label>
                                </div>
                            </div>

                            <div class="form-group">
                                {{Form::submit(__('Login'),array('class'=>'btn btn-primary btn-lg btn-block w-100','id'=>'saveBtn'))}}
                            </div>

                            {{Form::close()}}
                        </div>
                    </div>

                    <div class="simple-footer">
                        {{(Utility::getValByName('footer_text')) ? Utility::getValByName('footer_text') :  __('Powered by Enrich Arcane (Pvt) Ltd.') }}
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var toggleBtn = document.getElementById('togglePassword');
            var passwordInput = document.getElementById('password');
            var toggleIcon = document.getElementById('togglePasswordIcon');
            if (toggleBtn && passwordInput && toggleIcon) {
                toggleBtn.addEventListener('click', function () {
                    var isHidden = passwordInput.getAttribute('type') === 'password';
                    passwordInput.setAttribute('type', isHidden ? 'text' : 'password');
                    toggleIcon.classList.toggle('fa-eye', !isHidden);
                    toggleIcon.classList.toggle('fa-eye-slash', isHidden);
                });
            }
        });
    </script>
@endsection
