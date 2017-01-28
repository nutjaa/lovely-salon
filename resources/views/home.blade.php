@extends('layouts.backend')

@section('breadcrumbs')
<li>
    <a href="#">Shops</a>
</li>
@endsection

@section('page-level-styles')
<!-- BEGIN PAGE LEVEL STYLES -->
<link href="/assets/pages/css/blog.min.css" rel="stylesheet" type="text/css" />
<!-- END PAGE LEVEL STYLES -->
@endsection

@section('header-menu')
<!-- BEGIN DROPDOWN AJAX MENU -->
<div class="dropdown-ajax-menu btn-group">
    <button type="button" class="btn dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
        <i class="fa fa-circle"></i>
        <i class="fa fa-circle"></i>
        <i class="fa fa-circle"></i>
    </button>
    <ul class="dropdown-menu-v2">
        <li>
            <a href="{{ url('shops/create') }}">Create new shop</a>
        </li>
    </ul>
</div>
<!-- END DROPDOWN AJAX MENU -->
@endsection

@section('content')
<div class="blog-page blog-content-1">
    <div class="row">
        @foreach($user->company_users as $company_user)
            <div class="col-sm-4">
                <div class="blog-post-sm bordered blog-container">
                    <div class="blog-img-thumb">
                        <a href="javascript:;">
                            <img src="/assets/images/02.jpg">
                        </a>
                    </div>
                    <div class="blog-post-content">
                        <h2 class="blog-title blog-post-title">
                            <a href="{{ url($company_user->company->url) }}">{{ $company_user->company->name }}</a>
                        </h2>
                        <p class="blog-post-desc"></p>
                        <div class="blog-post-foot">
                            <div class="blog-post-meta">
                                <i class="icon-users font-blue"></i>
                                <a href="javascript:;">14 Employees</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
