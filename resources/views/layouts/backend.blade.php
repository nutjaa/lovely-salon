<!DOCTYPE html>
<html lang="en">
	<head>
    <meta charset="utf-8" />
    <title>Love salon</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <meta content="" name="Locvely salon" />
    <meta content="" name="Nut Chanyong" />

    <!-- BEGIN LAYOUT FIRST STYLES -->
    <link href="//fonts.googleapis.com/css?family=Oswald:400,300,700" rel="stylesheet" type="text/css" />
    <!-- END LAYOUT FIRST STYLES -->
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
    <link href="/assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css" />
    <!-- END GLOBAL MANDATORY STYLES -->


    <!-- BEGIN THEME GLOBAL STYLES -->
    <link href="/assets/global/css/components-md.min.css" rel="stylesheet" id="style_components" type="text/css" />
    <link href="/assets/global/css/plugins-md.min.css" rel="stylesheet" type="text/css" />
    <!-- END THEME GLOBAL STYLES -->
    <!-- BEGIN THEME LAYOUT STYLES -->
    <link href="/assets/layouts/layout6/css/layout.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/layouts/layout6/css/custom.min.css" rel="stylesheet" type="text/css" />
    <!-- END THEME LAYOUT STYLES -->

    @section('page-level-styles')
    @show

    <body class="page-md">
    	@include('shared.header')


    	<!-- BEGIN CONTAINER -->
        <div class="container-fluid">

          <div class="page-content page-content-popup">

            <div class="page-content-fixed-header">
              <!-- BEGIN BREADCRUMBS -->
              <ul class="page-breadcrumb">
                @section('breadcrumbs')
                @show
              </ul>

              <div class="content-header-menu">
                @section('header-menu')
                @show
                <!-- BEGIN MENU TOGGLER -->
                <button type="button" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="toggle-icon">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </span>
                </button>
                <!-- END MENU TOGGLER -->
              </div>

            </div>


            @section('page-sidebar')
            @show


            <div class="page-fixed-main-content">



                @if (session('status'))
                    <div class="alert alert-success alert-dismissable">
                        <button class="close" aria-hidden="true" data-dismiss="alert" type="button"></button>
                        {{ session('status') }}
                    </div>
                @endif
                @yield('content')
            </div>
            <p class="copyright-v2">2017 © Lovely salon.
            </p>
          </div>
        </div>
      <!-- END CONTAINER -->

    	<!--[if lt IE 9]>
			<script src="/assets/global/plugins/respond.min.js"></script>
			<script src="/assets/global/plugins/excanvas.min.js"></script>
			<![endif]-->

			<!-- BEGIN CORE PLUGINS -->
			@include('shared.core_plugins')

            @section('page-level-js')
            @show
      <!-- END CORE PLUGINS -->
    </body>
</html>