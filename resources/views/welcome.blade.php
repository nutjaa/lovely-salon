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
    <form class="login-form" action="index.html" method="post">
      <div class="form-title">
        <span class="form-title">Welcome.</span>
        <span class="form-subtitle">Please login.</span>
      </div>
      <div class="alert alert-danger display-hide">
        <button class="close" data-close="alert"></button>
        <span> Enter any username and password. </span>
      </div>
      <div class="form-group">
        <!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
        <label class="control-label visible-ie8 visible-ie9">Username</label>
        <input class="form-control form-control-solid placeholder-no-fix" type="text" autocomplete="off" placeholder="Username" name="username" /> </div>
      <div class="form-group">
        <label class="control-label visible-ie8 visible-ie9">Password</label>
        <input class="form-control form-control-solid placeholder-no-fix" type="password" autocomplete="off" placeholder="Password" name="password" /> </div>
      <div class="form-actions">
          <button type="submit" class="btn red btn-block uppercase">Login</button>
      </div>
      <div class="form-actions">
        <div class="pull-left">
          <label class="rememberme mt-checkbox mt-checkbox-outline">
            <input type="checkbox" name="remember" value="1" /> Remember me
            <span></span>
          </label>
        </div>
      </div>
    </form>
    <!-- END LOGIN FORM -->
  </div>
  <div class="copyright hide"> 2017 © Lovely Salon.</div>

@endsection