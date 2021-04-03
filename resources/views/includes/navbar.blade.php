<!-- Navbar (Header) Starts-->
<nav class="navbar navbar-expand-lg navbar-light bg-faded">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" data-toggle="collapse" class="navbar-toggle d-lg-none float-left"><span class="sr-only">Toggle navigation</span><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span></button>
            <form role="search" class="navbar-form navbar-right mt-1">
                <div class="position-relative has-icon-right">
                    <input type="text" placeholder="Search" class="form-control round" />
                    <div class="form-control-position"><i class="ft-search"></i></div>
                </div>
            </form>
        </div>
        <div class="navbar-container">
            <div id="navbarSupportedContent" class="collapse navbar-collapse">
                <ul class="navbar-nav">
                    <li class="nav-item mr-2">
                        <a id="navbar-fullscreen" href="javascript:;" class="nav-link apptogglefullscreen"><i class="ft-maximize font-medium-3 blue-grey darken-4"></i>
                            <p class="d-none">fullscreen</p>
                        </a>
                    </li>
                    <li class="dropdown nav-item" style="display:none;">
                        <a id="dropdownBasic2" href="#" data-toggle="dropdown" class="nav-link position-relative dropdown-toggle"><i class="ft-bell font-medium-3 blue-grey darken-4"></i><span class="notification badge badge-pill badge-danger">4</span>
                            <p class="d-none">Notifications</p>
                        </a>
                        <div class="notification-dropdown dropdown-menu dropdown-menu-right">
                            <div class="noti-list">
                                <a class="dropdown-item noti-container py-3 border-bottom border-bottom-blue-grey border-bottom-lighten-4"><i class="ft-bell info float-left d-block font-large-1 mt-1 mr-2"></i><span class="noti-wrapper"><span class="noti-title line-height-1 d-block text-bold-400 info">New Order Received</span><span class="noti-text">Lorem ipsum dolor sit ametitaque in, et!</span></span></a>
                                <a class="dropdown-item noti-container py-3 border-bottom border-bottom-blue-grey border-bottom-lighten-4"><i class="ft-bell warning float-left d-block font-large-1 mt-1 mr-2"></i><span class="noti-wrapper"><span class="noti-title line-height-1 d-block text-bold-400 warning">New User Registered</span><span class="noti-text">Lorem ipsum dolor sit ametitaque in </span></span></a>
                                <a class="dropdown-item noti-container py-3 border-bottom border-bottom-blue-grey border-bottom-lighten-4"><i class="ft-bell danger float-left d-block font-large-1 mt-1 mr-2"></i><span class="noti-wrapper"><span class="noti-title line-height-1 d-block text-bold-400 danger">New Order Received</span><span class="noti-text">Lorem ipsum dolor sit ametest?</span></span></a>
                                <a class="dropdown-item noti-container py-3"><i class="ft-bell success float-left d-block font-large-1 mt-1 mr-2"></i><span class="noti-wrapper"><span class="noti-title line-height-1 d-block text-bold-400 success">New User Registered</span><span class="noti-text">Lorem ipsum dolor sit ametnatus aut.</span></span></a>
                            </div>
                            <a class="noti-footer primary text-center d-block border-top border-top-blue-grey border-top-lighten-4 text-bold-400 py-1">Read All Notifications</a>
                        </div>
                    </li>
                    <li class="dropdown nav-item">
                        <a id="dropdownBasic3" href="#" data-toggle="dropdown" class="nav-link position-relative dropdown-toggle">
                            <i class="ft-user font-medium-3 blue-grey darken-4"></i>
                            <p class="d-none">User Settings</p>
                        </a>
                        <div ngbdropdownmenu="" aria-labelledby="dropdownBasic3" class="dropdown-menu dropdown-menu-right">
                            <a href="/user-profile" class="dropdown-item py-1">
                                <i class="ft-settings mr-2"></i><span>Account Settings ({{Auth::user()->username}})</span>
                            </a>
                            <a href="javascript:;" class="dropdown-item py-1">
                                <i class="ft-mail mr-2"></i><span>My Inbox</span>
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="{{ route('logout') }}" class="dropdown-item" onclick="event.preventDefault();
                                document.getElementById('logout-form').submit();">
                                <i class="ft-power mr-2"></i><span>Logout</span>
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </li>
                    <li class="nav-item"><a href="javascript:;" class="nav-link position-relative notification-sidebar-toggle"><i class="ft-align-left font-medium-3 blue-grey darken-4"></i>
                            <p class="d-none">Notifications Sidebar</p>
                        </a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>
<!-- Navbar (Header) Ends-->