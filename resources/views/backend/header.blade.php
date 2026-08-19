<nav class="main-header navbar navbar-expand navbar-dark navbar-gray-dark">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#!">
                <i class="fas fa-bars"></i>
            </a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="{{ route('backend_home') }}" class="nav-link">Home</a>
        </li>
        {{--        <li class="nav-item d-none d-sm-inline-block">--}}
        {{--            <a href="{{ route('backend_post') }}" class="nav-link">Tin tức</a>--}}
        {{--        </li>--}}
    </ul>
    <ul class="navbar-nav ml-auto">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('home_page') }}" target="_blank" title="Xem website">
                <i class="fas fa-globe-americas"></i> Xem website
            </a>
        </li>
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#!">
                @if($current_locale == '')
                    <i class="flag-icon flag-icon-gb"></i>
                @else
                    <i class="flag-icon flag-icon-vn"></i>
                @endif
            </a>
            <div class="dropdown-menu dropdown-menu-right p-0">
{{--                <a href="{{ url('/') }}/vn/backend" class="dropdown-item @if($current_locale == 'vn')active @endif">--}}
{{--                    <i class="flag-icon flag-icon-vn mr-2"></i> Việt Nam--}}
{{--                </a>--}}
                <a href="{{ url('/') }}/backend" class="dropdown-item @if($current_locale == '')active @endif">
                    <i class="flag-icon flag-icon-gb mr-2"></i> Tiếng anh
                </a>
            </div>
        </li>
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#!" title="Profiles">
                <i class="fas fa-th-large"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <a href="#!" class="dropdown-item">
                    <div class="media">
                        <img src="{{ asset('backend_assets/images/logo.png') }}"
                             alt="User Avatar"
                             class="img-size-50 mr-3 img-circle">
                        <div class="media-body">
                            <h3 class="dropdown-item-title">
                                {{ Auth::user()->name ?? '' }}
                                <span class="float-right text-sm text-danger"><i class="fas fa-star"></i></span>
                            </h3>
                            <p class="text-sm">{{ Auth::user()->email ?? '' }}</p>
                            <p class="text-sm text-muted">
                                <i class="far fa-clock mr-1"></i>
                                {{ date('d/m/Y') }}
                            </p>
                        </div>
                    </div>
                </a>
                <div class="dropdown-divider"></div>
                <a href="{{ route('logout') }}" class="dropdown-item dropdown-footer" onclick="event.preventDefault();
                                       document.getElementById('logout-form').submit();">Đăng xuất</a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                      style="display: none;">
                    @csrf
                </form>
            </div>
        </li>
    </ul>
</nav>
