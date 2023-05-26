<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>@hasSection('title')@yield('title') &middot; @endif{{ config('app.name', 'SimpliCMS') }}</title>
        <link href="{{ themes('css/bootstrap.css') }}" rel="stylesheet">
        @stack('style')
        <link href="{{ themes('css/custom.css') }}" rel="stylesheet">
        <style>
            .message-container {
                position: relative;
                width: 100%;
                padding-bottom: 75%; /* Adjust the aspect ratio based on your needs */
                overflow: hidden;
            }

            .message-container iframe {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                border: none;
            }
        </style>
    </head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">{{ config('app.name', 'SimpliCMS') }}</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('webmail.mailbox') }}">Inbox</a>
                        </li>
                        <!-- Add more menu items as needed -->
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container-fluid mt-1">
            <a class="btn btn-primary" href="{{ route('webmail.compose', ['folder' => 'INBOX']) }}" role="button">Compose Mail</a>
            <div class="row mt-1">
                <div class="col-md-3">
                    @include('webmail-admin::partials.sidebar', ['folders' => $folders])
                </div>
                <div class="col-md-9">
                    <div class="content">
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>

        @stack('alpine')
        <!-- Scripts -->
        <script src="{{ themes('js/alpine.js') }}"></script>
        <script src="{{ themes('js/jquery.js') }}"></script>
        <script src="{{ themes('js/bootstrap.bundle.js') }}"></script>
        @stack('scripts')
    </body>
</html>

