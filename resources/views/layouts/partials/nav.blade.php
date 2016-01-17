<div role="navigation" class="navbar navbar-default navbar-static-top topnav">
    <div class="container">
        <div class="navbar-header">
            <a href="/" class="navbar-brand">L5Forum</a>
        </div>

        <div id="top-navbar-collapse" class="navbar-collapse">
            <ul class="nav navbar-nav">
                <li class="{{ (request()->is('topics*') ? ' active' : '') }}"><a href="{{ route('topics.index') }}">{{ lang('Topics') }}</a></li>
                <li class="{{ (request()->is('wiki*') ? ' active' : '') }}"><a href="{{ route('wiki') }}">{{ lang('Wiki') }}</a></li>
                <li class="{{ (request()->is('about*') ? ' active' : '') }}"><a href="{{ route('about') }}">{{ lang('About') }}</a></li>
            </ul>

            <div class="navbar-right">
                {!! Form::open(['route'=>'search', 'method'=>'get', 'class'=>'navbar-form navbar-left']) !!}
                <div class="form-group">
                    {!! Form::text('q', null, ['class' => 'form-control search-input mac-style', 'placeholder' => lang('Search')]) !!}
                </div>
                {!! Form::close() !!}
                <ul class="nav navbar-nav github-login">
                    @if (auth()->check())
                        <li>
                            <a href="{{ route('notifications.index') }}" class="text-warning">
                                <span class="badge badge-{{ auth()->user()->notification_count > 0 ? 'important' : 'fade' }}" id="notification-count">
                                    {{ auth()->user()->notification_count }}
                                </span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('users.show', auth()->id()) }}">
                                <i class="fa fa-user"></i> {{ auth()->user()->name }}
                            </a>
                        </li>
                        <li>
                            <a class="button" href="{{ route('logout') }}" onclick="return confirm('{{ lang('Are you sure want to logout?') }}')">
                                <i class="fa fa-sign-out"></i> {{ lang('Logout') }}
                            </a>
                        </li>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-info" id="login-btn">
                            <i class="fa fa-github-alt"></i> {{ lang('Login') }}
                        </a>
                    @endif
                </ul>
            </div>
        </div>

    </div>
</div>
