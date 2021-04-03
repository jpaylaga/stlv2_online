<!-- main menu-->
<!--.main-menu(class="#{menuColor} #{menuOpenType}", class=(menuShadow == true ? 'menu-shadow' : ''))-->
<div data-active-color="white" data-background-color="white" data-image="" class="app-sidebar">
    <!-- main menu header-->
    <!-- Sidebar Header starts-->
    <div class="sidebar-header">
        <div class="logo clearfix">
            <a href="index.html" class="logo-text float-left">
                <!-- <div class="logo-img">
                    <img src="{{ asset('/assets/img/mekas_logo.jpg') }}" />
                </div> -->
                <span class="text align-middle">{{ config('app.name') }}</span>
            </a>
            <a id="sidebarToggle" href="javascript:;" class="nav-toggle d-none d-sm-none d-md-none d-lg-block">
                <i data-toggle="expanded" class="ft-toggle-right toggle-icon"></i>
            </a>
            <a id="sidebarClose" href="javascript:;" class="nav-close d-block d-md-block d-lg-none d-xl-none"><i class="ft-x"></i></a>
        </div>
    </div>
    <!-- Sidebar Header Ends-->
    <!-- / main menu header-->
    <!-- main menu content-->


    <div class="sidebar-content">
        <div class="nav-container">

            @can('manage-users')
            <ul id="main-menu-navigation" data-menu="menu-navigation" class="navigation navigation-main">
                <li class="has-sub nav-item"><a href="#"><i class="ft-home"></i><span data-i18n="" class="menu-title">Dashboard</span></a>
                    <ul class="menu-content">
                        <li class="{{ Request::segment(1) == '' ? 'active' : '' }}">
                            <a href="/" class="menu-item">Dashboard</a>
                        </li>
                        <li class="{{ Request::segment(1) == 'winners' ? 'active' : '' }}">
                            <a href="/winners" class="menu-item">Winners</a>
                        </li>
                        @can('draw-results')
                        <li class="{{ Request::segment(1) == 'draw-results' ? 'active' : '' }}">
                            <a href="/draw-results" class="menu-item">Draw Results</a>
                        </li>
                        @endcan
                    </ul>
                </li>
                <li class="has-sub nav-item">
                    <a href="#">
                        <i class="icon-docs"></i><span data-i18n="" class="menu-title">Transactions</span>
                    </a>
                    <ul class="menu-content">
                        <li class="{{ Request::segment(1) == 'transactions' ? 'active' : '' }}">
                            <a href="/transactions" class="menu-item">All</a>
                        </li>
                        <li class="{{ Request::segment(1) == 'cancelled-tickets' ? 'active' : '' }}">
                            <a href="/cancelled-tickets" class="menu-item">Cancelled</a>
                        </li>
                    </ul>
                </li>
                @if( env('SHOW_PAYOUTS_PAGE', true) )
                <li class="nav-item {{ Request::segment(1) == 'payouts' ? 'active' : '' }}">
                    <a href="/payouts">
                        <i class="ft-user-check"></i><span data-i18n="" class="menu-title">Payouts</span>
                    </a>
                </li>
                @endif
                @can('manage-credits')
                <li class="has-sub nav-item">
                    <a href="#">
                        <i class="fa fa-rub"></i><span data-i18n="" class="menu-title">Credits</span>
                    </a>
                    <ul class="menu-content">
                        <li class="{{ Request::segment(1) == 'credits' ? 'active' : '' }}">
                            <a href="/credits" class="menu-item">List</a>
                        </li>
                        <li class="{{ Request::segment(1) == 'credit-requests' ? 'active' : '' }}">
                            <a href="/credit-requests" class="menu-item">Requests</a>
                        </li>
                        <li class="{{ Request::segment(1) == 'credit-references' ? 'active' : '' }}">
                            <a href="/credit-references" class="menu-item">References</a>
                        </li>
                    </ul>
                </li>
                @endcan
                <li class="nav-item" style="display: none;">
                    <a href="/manage-outlets">
                        <i class="icon-home"></i><span data-i18n="" class="menu-title">Outlets</span>
                    </a>
                </li>
                @can('edit-area')
                <li class="has-sub nav-item">
                    <a href="#">
                        <i class="icon-users"></i>
                        <span data-i18n="" class="menu-title">Users</span>
                    </a>
                    <ul class="menu-content">
                        @can('edit-area')
                        <li class="{{ Request::segment(1) == 'manage-coordinators' ? 'active' : '' }}">
                            <a href="/manage-coordinators" class="menu-item">Coordinators</a>
                        </li>
                        <li class="{{ Request::segment(1) == 'manage-tellers' ? 'active' : '' }}">
                            <a href="/manage-tellers" class="menu-item">Agents/Tellers</a>
                        </li>
                        <li class="{{ Request::segment(1) == 'manage-players' ? 'active' : '' }}">
                            <a href="/manage-players" class="menu-item">Players</a>
                        </li>
                        @endcan
                        @if( Auth::user()->role == 'super_admin' )
                        <li class="{{ Request::segment(1) == 'manage-area-admins' ? 'active' : '' }}">
                            <a href="/manage-area-admins" class="menu-item">Area Admins</a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endcan
                @if( Auth::user()->role == 'coordinator' )
                    <!-- <li class="nav-item {{ Request::segment(1) == 'manage-tellers' ? 'active' : '' }}">
                        <a href="/manage-tellers" class="menu-item">
                            <i class="icon-users"></i>
                            <span data-i18n="" class="menu-title">Agents/Tellers</span>
                        </a>
                    </li>
                    <li class="nav-item {{ Request::segment(1) == 'manage-players' ? 'active' : '' }}">
                        <a href="/manage-players" class="menu-item">
                            <i class="fa fa-users"></i>
                            <span data-i18n="" class="menu-title">Players</span>
                        </a>
                    </li> -->
                    <!-- <li class="nav-item {{ Request::segment(1) == 'credit-requests' ? 'active' : '' }}">
                        <a href="/credit-requests" class="menu-item">
                            <i class="fa fa-rub"></i>
                            <span data-i18n="" class="menu-title">Credit Requests</span>
                        </a>
                    </li> -->
                    <li class="has-sub nav-item">
                        <a href="#">
                            <i class="icon-users"></i>
                            <span data-i18n="" class="menu-title">Users</span>
                        </a>
                        <ul class="menu-content">
                            <li class="nav-item {{ Request::segment(1) == 'manage-tellers' ? 'active' : '' }}">
                                <a href="/manage-tellers" class="menu-item">
                                    <i class="icon-users"></i>
                                    <span data-i18n="" class="menu-title">Agents/Tellers</span>
                                </a>
                            </li>
                            <li class="nav-item {{ Request::segment(1) == 'manage-players' ? 'active' : '' }}">
                                <a href="/manage-players" class="menu-item">
                                    <i class="fa fa-users"></i>
                                    <span data-i18n="" class="menu-title">Players</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="has-sub nav-item">
                        <a href="#">
                            <i class="fa fa-rub"></i>
                            <span data-i18n="" class="menu-title">Credit</span>
                        </a>
                        <ul class="menu-content">
                            <!-- <li class="{{ Request::segment(2) == 'info' ? 'active' : '' }}">
                                <a href="/credit-balance/info" class="menu-item">
                                    <i class="fa fa-address-book"></i>
                                    <span data-i18n="" class="menu-title">Requests</span>
                                </a>
                            </li> -->
                            <li class="nav-item {{ Request::segment(1) == 'credit-requests' ? 'active' : '' }}">
                                <a href="/credit-requests" class="menu-item">
                                    <i class="fa fa-address-book"></i>
                                    <span data-i18n="" class="menu-title">Requests</span>
                                </a>
                            </li>
                            <li class="{{ Request::segment(2) == 'topup' ? 'active' : '' }}">
                                <a href="/credit-balance/topup" class="menu-item">
                                    <i class="fa fa-download"></i>
                                    <span data-i18n="" class="menu-title">Topup</span>
                                </a>
                            </li>
                            <li class="{{ Request::segment(2) == 'withdraw' ? 'active' : '' }}">
                                <a href="/credit-balance/withdraw" class="menu-item">
                                    <i class="fa fa-upload"></i>
                                    <span data-i18n="" class="menu-title">Withdraw</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif
                <!-- <li class="nav-item {{ Request::segment(1) == 'manage-users' ? 'active' : '' }}">
                    <a href="#">
                        <i class="icon-users"></i><span data-i18n="" class="menu-title">Users</span>
                    </a>
                </li> -->
                <li class="has-sub nav-item"><a href="#"><i class="icon-layers"></i><span data-i18n="" class="menu-title">Reports</span></a>
                    <ul class="menu-content">
                        <!-- <li class="{{ Request::segment(1) == 'summary-sales' ? 'active' : '' }}">
                            <a href="/summary-sales" class="menu-item">Summary Sales</a>
                        </li> -->
                        <li class="{{ Request::segment(1) == 'reports-coordinators' ? 'active' : '' }}">
                            <a href="/reports-coordinators" class="menu-item">Coordinators</a>
                        </li>
                        @if( Auth::user()->role == 'coordinator' )
                        <li class="{{ Request::segment(1) == 'summary-report' ? 'active' : '' }}">
                            <a href="/summary-report" class="menu-item">Summary Reports</a>
                        <li>
                        @endif
                        @if( Auth::user()->role == 'super_admin' )
                        <!-- <li class="{{ Request::segment(1) == 'area-summary' ? 'active' : '' }}">
                            <a href="/area-summary" class="menu-item">Area Summary</a>
                        </li> -->
                        <li class="{{ Request::segment(1) == 'highest-bet' ? 'active' : '' }}">
                            <a href="/highest-bet" class="menu-item">Highest Bet</a>
                        </li>
                        <li class="{{ Request::segment(1) == 'hot-numbers' ? 'active' : '' }}">
                            <a href="/hot-numbers" class="menu-item">Hot Numbers</a>
                        </li>
                        @endif

                    </ul>
                </li>

                @if( Auth::user()->role == 'super_admin' )
                <li class="has-sub nav-item">
                    <a href="#">
                        <i class="icon-settings"></i>
                        <span data-i18n="" class="menu-title">General Settings</span>
                    </a>
                    <ul class="menu-content">
                        @can('edit-area')
                        <li class="{{ Request::segment(2) == 'area-settings' ? 'active' : '' }}">
                            <a href="/settings/area-settings" class="menu-item">Area Settings</a>
                        </li>
                        @endcan
                        <li class="{{ Request::segment(2) == 'bet-limits' ? 'active' : '' }}">
                            <a href="/settings/bet-limits" class="menu-item">Bet Limits</a>
                        </li>
                        <li class="{{ Request::segment(2) == 'payout-rates' ? 'active' : '' }}">
                            <a href="/settings/payout-rates" class="menu-item">Payout Rates</a>
                        </li>
                        <li class="{{ Request::segment(1) == 'outlets' ? 'active' : '' }}">
                            <a href="/outlets" class="menu-item">Outlets</a>
                        </li>
                        <!-- <li class="{{ Request::segment(2) == 'payout-rates' ? 'active' : '' }}">
                            <a href="/settings/payout-rates" class="menu-item">Games</a>
                        </li> -->
                    </ul>
                </li>
                @endif

                <li class="nav-item {{ Request::segment(1) == 'area-settings' ? 'active' : '' }}">
                    <a href="{{ route('logout') }}" onclick="event.preventDefault();
                                document.getElementById('logout-form').submit();">
                        <i class="ft-power mr-2"></i>
                        <span data-i18n="" class="menu-title">Logout</span>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </li>

            </ul>
            @elsecan('manage-bets')
            <ul id="main-menu-navigation" data-menu="menu-navigation" class="navigation navigation-main">
                <li class="nav-item {{ Request::segment(1) == 'manage-bets' ? 'active' : '' }}">
                    <a href="/manage-bets">
                        <i class="ft-file-plus"></i>
                        <span data-i18n="" class="menu-title">Add Bet</span>
                    </a>
                </li>
                <li class="nav-item {{ Request::segment(1) == 'transactions' ? 'active' : '' }}">
                    <a href="/transactions">
                        <i class="icon-docs"></i>
                        <span data-i18n="" class="menu-title">Transactions</span>
                    </a>
                </li>
            </ul>
            @endcan

            <!-- PLAYERS ONLY -->
            @if( Auth::user()->role == 'player' )
                <ul id="main-menu-navigation" data-menu="menu-navigation" class="navigation navigation-main">
                    <li class="nav-item {{ Request::segment(1) == 'player_dashboard' ? 'active' : '' }}">
                        <a href="/player_dashboard">
                            <i class="fa fa-tachometer"></i>
                            <span data-i18n="" class="menu-title">Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item {{ Request::segment(1) == 'transactions' ? 'active' : '' }}">
                        <a href="/transactions">
                            <i class="icon-book-open"></i>
                            <span data-i18n="" class="menu-title">Transactions</span>
                        </a>
                    </li>
                    <li class="nav-item {{ Request::segment(1) == 'winnings' ? 'active' : '' }}">
                        <a href="/winnings">
                            <i class="fa fa-trophy"></i>
                            <span data-i18n="" class="menu-title">Winnings</span>
                        </a>
                    </li>
                    <li class="has-sub nav-item">
                        <a href="#">
                            <i class="fa fa-rub"></i>
                            <span data-i18n="" class="menu-title">Credit</span>
                        </a>
                        <ul class="menu-content">
                            <li class="{{ Request::segment(2) == 'info' ? 'active' : '' }}">
                                <a href="/credit-balance/info" class="menu-item">Requests</a>
                            </li>
                            <li class="{{ Request::segment(2) == 'topup' ? 'active' : '' }}">
                                <a href="/credit-balance/topup" class="menu-item">Topup</a>
                            </li>
                            <li class="{{ Request::segment(2) == 'withdraw' ? 'active' : '' }}">
                                <a href="/credit-balance/withdraw" class="menu-item">Withdraw</a>
                            </li>
                        </ul>
                    </li>
                    <li class="has-sub nav-item">
                        <a href="#">
                            <i class="fa fa-plus-square"></i>
                            <span data-i18n="" class="menu-title">Play</span>
                        </a>
                        <ul class="menu-content">
                            <li class="{{ Request::segment(2) == 'stl' ? 'active' : '' }}">
                                <a href="/play/stl" class="menu-item">STL</a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('logout') }}" onclick="event.preventDefault();
                                    document.getElementById('logout-form').submit();">
                            <i class="ft-power mr-2"></i>
                            <span data-i18n="" class="menu-title">Logout</span>
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </li>
                </ul>
            @endif

        </div>
    </div>


    <!-- main menu content-->
    <div class="sidebar-background"></div>
    <!-- main menu footer-->
    <!-- include includes/menu-footer-->
    <!-- main menu footer-->
</div>
<!-- / main menu-->