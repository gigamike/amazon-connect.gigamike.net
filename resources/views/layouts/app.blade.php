<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="#">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                          <a class="nav-link active" aria-current="page" href="{{ url('/home') }}">Home</a>
                        </li>
                        @auth
                        <?php if(isset(Auth::user()->role) == 'admin'): ?>
                        <li class="nav-item">
                          <a class="nav-link" href="{{ url('/users') }}"> <i class="fa-solid fa-users"></i> Users</a>
                        </li>
                        @endauth
                        <?php endif; ?>
                        <li class="nav-item">
                          <a class="nav-link" href="{{ url('/customers') }}"> <i class="fa-solid fa-user-group"></i> Customers</a>
                        </li>

                        @auth
                        <?php if(isset(Auth::user()->role) == 'agent'): ?>
                        <li class="nav-item">
                          <a class="nav-link amazonConnectLogin" href="javascript:void(0);"><i class="fa-solid fa-phone"></i> CCP</a>
                        </li>
                        @endauth
                        <?php endif; ?>
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    

                                    {{ Auth::user()->name }} 

                                    @if (Auth::user()->ccp_status == 'Training')
                                    <span class="dot" style="background-color: orange;" title="Training"></span>
                                    @elseif (Auth::user()->ccp_status == 'Meeting')
                                    <span class="dot" style="background-color: yellow;" title="Meeting"></span>
                                    @elseif (Auth::user()->ccp_status == 'Break')
                                    <span class="dot" style="background-color: red;" title="Break"></span>
                                    @elseif (Auth::user()->ccp_status == 'Available')
                                    <span class="dot" style="background-color: green;" title="Available"></span> 
                                    @elseif (Auth::user()->ccp_status == 'Offline')
                                    <span class="dot" title="Available"></span> 
                                    @else
                                    <span class="dot" title="Uknown"></span> 
                                    @endif
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>

    @auth
    <?php if(Auth::user()->role == 'agent'): ?>
    <div class="modal" tabindex="-1" id="amazonConnectLoginModal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Amazon Connect</h5>
          </div>
          <div class="modal-body">
            <p>Please login to <a href="javascript:void(0)" class="amazonConnectLogin">Amazon Connect</a>. After logging in, <a href="javascript:void(0)" class="amazonConnectLogin">click here</a> to continue.</p>
          </div>
        </div>
      </div>
    </div>

    <?php endif; ?>
    @endauth

    <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
    <script src="https://kit.fontawesome.com/22ee0cf71e.js" crossorigin="anonymous"></script>

    @auth
    <?php if(Auth::user()->role == 'agent'): ?>
    <script type="text/javascript">
    var amazonConnectPopupWindow = null;
    var urlAmazonConnectStream = "{{ url('windowOpenAmazonConnectStream') }}";

    $(document).ready(function() {
        check_if_ccp_loggedin();

        $('.amazonConnectLogin').on('click', function () {
            if ((amazonConnectPopupWindow == null) || (amazonConnectPopupWindow.closed)) {
                amazonConnectPopupWindow = amazon_connect_popup_window(urlAmazonConnectStream, "window_open_amazon_connect_stream", "330", "700");
                console.log("opening ccp popup the first time");
            }
            amazonConnectPopupWindow.focus();
        });
    });

    function check_if_ccp_loggedin() {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url : "{{ url('ajaxIsCcpLoggedin') }}",
            type : 'POST',
            dataType : 'json',
            success : function(jObj){
                if (!jObj.successful) {
                    $('#amazonConnectLoginModal').modal({
                        backdrop: 'static',
                        keyboard: false
                    })
                    $("#amazonConnectLoginModal").modal('show');
                }
            }
        });
    }

    function amazon_connect_popup_window(url, title, w, h) {
        var left = (screen.width / 2) - (w / 2);
        var top = (screen.height / 2) - (h / 2);
        return window.open(url, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);
    }
    </script>
    <?php endif; ?>
    @endauth
</body>
</html>
