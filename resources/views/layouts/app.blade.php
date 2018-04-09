<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title><?php echo env('APP_NAME'); ?></title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/users.css') }}" rel="stylesheet">
    <link href="{{ asset('mdbootstrap/css/bootstrap.css') }}" rel="stylesheet">
    <link href="{{ asset('mdbootstrap/css/mdb.css') }}" rel="stylesheet">
    <link href="{{ asset('font-awesome/css/font-awesome.css') }}" rel="stylesheet">
    <link href="/bootstrap-template/css/<?php echo env('BOOTSTRAP_THEME_TEMPLATE_FILENAME') ?>" rel="stylesheet">
    <link href="/css/modal.css" rel="stylesheet">
    <link href="/css/assetmgr.css" rel="stylesheet">

    <script>
      window.laravel = {
        csrfToken: "<?php echo csrf_token(); ?>"
      }
    </script>

</head>
<body>


    <!-- assetmgr.blade.php -->
    <div id="app">

<?php
$driver = AuthHelper::AuthDriverName();
?>

    <!-- Navbar-->

  <div id="app-header">
    <app-header :apptitle="{{ json_encode(env('APP_NAME')) }}" :user="{{ json_encode(Auth::user())}}"
    :driver="{{ json_encode($driver) }}"></app-header>
  </div>


  <div id="side-bar">
    <side-bar :routename="{{ json_encode(\Request::route()->getName()) }}"
    :user="{{ json_encode(AuthHelper::getLoggedInUser()) }}" ></side-bar>
  </div>



  <main id="main-div" class="app-content">
       @yield('content')
  </main>

</div>

<!-- Asset manager template js includes -->


    <script src="{{ asset('js/app.js') }}"></script>

      <script src="{{ asset('mdbootstrap/js/mdb.js') }}"></script>

      <script src="{{ asset('bootstrap-template/js/bootstrap-template.js') }}"></script>



<script>
  @yield('js-content')
</script>

</body>
</html>
