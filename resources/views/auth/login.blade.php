@extends('layouts.backend-login')

@section('content')

	<div class="logo">
    <a href="index.html">
      <h1>Lovely Salon</h1>
    </a>
  </div>

 <!-- BEGIN LOGIN -->
  <div class="content">
    <!-- BEGIN LOGIN FORM -->
    <form class="form-horizontal" role="form" method="POST" action="{{ url('/login') }}">
      {{ csrf_field() }}
      <div class="form-title">
        <span class="form-title">Welcome.</span>
        <span class="form-subtitle">Please login.</span>
      </div>
      @if (count($errors) > 0)
      <div class="alert alert-danger">
        <button class="close" data-close="alert"></button>
        <span> Incorrect username or password. </span>
      </div>
      @endif
      <div class="form-group">
        <!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
        <label class="control-label visible-ie8 visible-ie9">Username</label>
        <input id="email" type="email" class="form-control form-control-solid placeholder-no-fix" name="email"  placeholder="Email"  value="{{ old('username') }}" required autofocus>
      </div>
      <div class="form-group">
        <label class="control-label visible-ie8 visible-ie9">Password</label>
        <input id="password" type="password" autocomplete="off" class="form-control  form-control-solid placeholder-no-fix"  placeholder="Password"  name="password" required>
      </div>
      <div class="form-actions">
          <button type="submit" class="btn red btn-block uppercase">Login</button>
      </div>
      <div class="form-actions">
        <div class="pull-left">
          <label class="rememberme mt-checkbox mt-checkbox-outline">
            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : ''}}>
            Remember me
            <span></span>
          </label>
        </div>
      </div>
    </form>
    <!-- END LOGIN FORM -->
  </div>
  <div class="copyright hide"> 2017 © Lovely Salon.</div>

@endsection