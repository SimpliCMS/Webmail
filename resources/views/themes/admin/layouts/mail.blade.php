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
                padding-bottom: 65%; /* Adjust the aspect ratio based on your needs */
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

            .list-group-item-action:active {
                color: #fff;
                background-color: #665E77;
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
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                               id="account-dropdown-link">
                                <img src="{{ Auth::user()->profile->getProfileAvatar() }}" class="rounded-circle" style="width: 20px;">

                                    {{ Auth::user()->name }}

                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="account-dropdown-link">
                                <li><a class="dropdown-item" href="{{ route('admin.index') }}">{{ __('Return to Admin') }}</a></li>
                                <li><a class="dropdown-item" href="{{ route('appshell.account.display') }}">{{ __('Account') }}</a></li>
                                <li><a class="dropdown-item" href="{{ route('appshell.preferences.index') }}">{{ __('Preferences') }}</a></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route($appshell->routes['logout']) }}"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>
                                    <form id="logout-form" action="{{ route($appshell->routes['logout']) }}" method="POST" style="display: none;">
                                        {{ csrf_field() }}
                                    </form>
                                </li>
                            </ul>
                        </li>

                        <!-- Add more menu items as needed -->
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container-fluid mt-1">
            <a class="btn btn-primary" href="{{ route('webmail.compose', ['folder' => 'INBOX']) }}" role="button">Compose Mail</a>
            <div class="row mt-1">
                <div class="col-md-2">
                    @include('webmail-admin::partials.sidebar', ['folders' => $folders])
                </div>
                @yield('content')
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

