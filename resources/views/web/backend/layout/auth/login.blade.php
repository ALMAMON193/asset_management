@extends('web.frontend.app')

@section('content')
    <section class="login-section">
        <div class="container">
            <div class="login-container">
                <div class="row g-xxl-5 g-4 flex-column-reverse  flex-lg-row">
                    <div class="col-lg-6 h-100">
                        <div class="login-image-wrap">
                            <div class="login-image">
                                <img src="{{ asset("frontend/assets/images/login-img.png") }}" alt>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 h-100">
                        <div class="login-form-wrapper">
                            <div class="login-title">
                                <h2>Hello! Login now</h2>
                            </div>
                            <form action="{{ route('login') }}" method="POST">
                                @csrf
                                <div class="input-item">
                                    <label for="email">Enter Email</label>
                                    <input name="email" type="email" value="{{ old('email') }}" 
                                        class="form-control shadow-none @error('email') is-invalid @enderror"
                                        placeholder="Enter Email" >
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="input-item">
                                    <label for="password">Password</label>
                                    <input name="password" type="password" 
                                        class="form-control shadow-none @error('password') is-invalid @enderror"
                                        placeholder="Enter Password"  autocomplete="current-password">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="remember-main-wrapper">
                                    <div class="remember-wrapper">
                                        <div class="form-check">
                                            <input class="form-check-input shadow-none" type="checkbox" value
                                                id="flexCheckChecked" checked>
                                            <label class="form-check-label" for="flexCheckChecked">
                                                Remember Me
                                            </label>
                                        </div>
                                    </div>
                                    <!--<div class="forgot-password">-->
                                    <!--    <a href="#">Forget password</a>-->
                                    <!--</div>-->
                                </div>
                                <button type="submit" class="login-button">Log In</button>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div> 
    </section>
@endsection
