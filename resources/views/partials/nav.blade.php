<nav class="navbar navbar-custom navbar-fixed-top">
    <div class="container-fluid">
        <div class="navbar-header">
            <a href="/" class="navbar-brand">
                {{ Html::image('assets/images/logo.png', null, ['height' => '25px']) }}
            </a>
        </div>
        @if (Auth::check())
            <ul class="nav navbar-nav navbar-right">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-user"></i>
                        {{ Auth::user()->name }}
                        <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="/change-password">
                                <i class="fa fa-lock"></i>
                                {{ __('Change Password') }}
                            </a>
                        </li>
                        <li>
                            <a href="/logout">
                                <i class="fa fa-sign-out"></i>
                                {{ __('Logout') }}
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        @endif
    </div>
</nav>