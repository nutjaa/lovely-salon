@extends('layouts.backend')

@section('page-sidebar')

<div class="page-sidebar-wrapper">
	 <div class="page-sidebar navbar-collapse collapse">
	 		<ul class="page-sidebar-menu  page-header-fixed " data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">
        <li class="nav-item start ">
            <a href="javascript:;" class="nav-link nav-toggle">
                <i class="icon-home"></i>
                <span class="title">Dashboard</span>
                <span class="arrow"></span>
            </a>
            <ul class="sub-menu">
                <li class="nav-item start ">
                    <a href="{{ url($shop_url) }}" class="nav-link ">
                        <i class="icon-bar-chart"></i>
                        <span class="title">Employee Income</span>
                    </a>
                </li>
            </ul>
        </li>
        <li class="nav-item start ">
            <a href="{{ url($shop_url . '/daily-jobs') }}" class="nav-link ">
                <i class="icon-notebook"></i>
                <span class="title">Daily Tasks</span>
                <span class="arrow"></span>
            </a>
        </li>
        <li class="nav-item start ">
            <a href="{{ url($shop_url . '/daily-summary') }}" class="nav-link ">
                <i class="icon-notebook"></i>
                <span class="title">Daily Summary</span>
                <span class="arrow"></span>
            </a>
        </li>
        <li class="heading">
            <h3 class="uppercase">Settings</h3>
        </li>
        <li class="nav-item ">
            <a href="javascript:;" class="nav-link nav-toggle">
                <i class="icon-settings"></i>
                <span class="title">Shop</span>
                <span class="arrow"></span>
            </a>
            <ul class="sub-menu">
                <li class="nav-item  ">
                    <a href="{{ url($shop_url . '/employees') }}" class="nav-link ">
                        <i class="icon-settings"></i>
                        <span class="title">Employees</span>
                    </a>
                </li>
                <li class="nav-item  ">
                    <a href="{{ url($shop_url . '/customers') }}" class="nav-link ">
                        <i class="icon-settings"></i>
                        <span class="title">Customers</span>
                    </a>
                </li>
            </ul>
        </li>
        <li class="nav-item  ">
            <a href="javascript:;" class="nav-link nav-toggle">
                <i class="icon-settings"></i>
                <span class="title">System</span>
                <span class="arrow"></span>
            </a>
            <ul class="sub-menu">
                <li class="nav-item  ">
                    <a href="{{ url($shop_url . '/options') }}" class="nav-link ">
                        <i class="icon-settings"></i>
                        <span class="title">Options</span>
                    </a>
                </li>
            </ul>
        </li>
      </ul>
	 </div>
</div>

@endsection