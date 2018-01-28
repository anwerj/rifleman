<!-- Stored in resources/views/layouts/master.blade.php -->

<html>
<head>
    <title>RifleMan | @yield('title')</title>

    <link rel="stylesheet" type="text/css" href="/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="/css/bootstrap-grid.css">
    <link rel="stylesheet" type="text/css" href="/css/prettify.css">
    <link rel="stylesheet" type="text/css" href="/css/app.css">

    <script src="/js/jquery.3.2.1.js" type="text/javascript"></script>
    <script src="/js/popper.min.js" type="text/javascript"></script>
    <script src="/js/bootstrap.js" type="text/javascript"></script>
    <script src="/js/ejs.js" type="text/javascript"></script>
    <script src="/js/prettify.js" type="text/javascript"></script>
    <script src="/js/app.js" type="text/javascript"></script>
    @yield('head')
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="/">RifleMan</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            @yield('navs')
        </ul>
        <form class="form-inline my-2 my-lg-0">
            <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search">
            <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
        </form>
    </div>
</nav>
@section('sidebar')

@show

<div class="col-md-12">
    @yield('content')
</div>
@yield('footer')

@yield('js')
<script type="text/javascript">$(document).ready(function(){core.init();})</script>
</body>
</html>
