<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <meta name="theme-color" content="#206bc4">
  <link rel="manifest" href="{{ asset('manifest.json') }}">
  <link rel="icon" type="image/png" sizes="32x32"   href="{{ asset('favicon-32.png') }}">
  <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('images/icon-192.png') }}">
  <link rel="apple-touch-icon"                      href="{{ asset('images/icon-192.png') }}">

  <title>@yield('title', config('app.name', 'RuangTerra'))</title>

  <!-- Google Fonts: Plus Jakarta Sans -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Bootstrap 5 CSS CDN -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  
  <!-- DataTables Bootstrap 5 CSS CDN -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">

  <!-- Select2 CSS CDN & Bootstrap 5 Theme -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">

  <!-- Tabler Icons Webfont CDN -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">

  <!-- Custom Template CSS -->
  <link rel="stylesheet" href="{{ asset('template/be/assets/css/custom.css') }}">

  @stack('styles')
</head>
<body>
