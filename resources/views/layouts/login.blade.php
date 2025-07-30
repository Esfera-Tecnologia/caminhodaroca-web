<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset('assets/favicon.ico') }}" type="image/x-icon">
  <title>@yield('title', 'Login')</title>

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

  <!-- Seu CSS personalizado -->
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">



</head>
<body>

  @yield('content')

    <!-- jQuery e Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- JS (antes de fechar </body>) -->
    <script src="{{ asset('js/scripts.js') }}"></script>

  @stack('scripts')

</body>
</html>
