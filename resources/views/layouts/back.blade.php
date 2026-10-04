<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Tableau de bord') | Admin {{ config('app.name') }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/back.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body>
    <div class="ha-admin">
        @include('back.partials.sidebar')

        <div class="ha-admin-main">
            @include('back.partials.topbar')

            <div class="container-fluid p-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
                    <h1 class="h3 mb-0">@yield('page-title', 'Tableau de bord')</h1>
                    <div>@yield('page-actions')</div>
                </div>

                <x-flash />
                @yield('content')
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('form').forEach(function (form) {
                form.setAttribute('novalidate', 'novalidate');
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('sidebarToggle')?.addEventListener('click', () =>
            document.querySelector('.ha-admin').classList.toggle('sidebar-open'));
    </script>
    @stack('scripts')
</body>
</html>
