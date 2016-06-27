<nav class="navbar navbar-custom navbar-fixed-top">
    <div class="container-fluid">
        <div class="navbar-header">
            <a href="/" class="navbar-brand">
                {{ Html::image('assets/images/logo.png', null, ['height' => '25px']) }}
            </a>
        </div>
        @if (Auth::check())
            <ul class="nav navbar-nav navbar-left">
                <li>
                    <a href="/campaigns">
                        <i class="fa fa-refresh"></i>
                        {{ __('Campaigns') }}
                    </a>
                </li>
                <li>
                    <a href="/deals">
                        <i class="fa fa-paper-plane"></i>
                        {{ __('Deals') }}
                    </a>
                </li>
                <li>
                    <a href="/users">
                        <i class="fa fa-users"></i>
                        {{ __('Users') }}
                    </a>
                </li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-user"></i>
                        {{ Auth::user()->name }}
                        <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="/my-account">
                                <i class="fa fa-user"></i>
                                {{ __('My Account') }}
                            </a>
                        </li>
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