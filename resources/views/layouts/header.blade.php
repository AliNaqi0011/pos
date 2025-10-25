<nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
    <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
        <a class="navbar-brand brand-logo" href="{{route('dashboard')}}"><img src="{{asset('template/images/techesthete-logo.png')}}" alt="logo" /></a>
        <a class="navbar-brand brand-logo-mini" href="{{route('dashboard')}}"><img src="{{asset('template/images/logo-mini.svg')}}" alt="logo" /></a>
        <button class="navbar-toggler navbar-toggler align-self-center d-none d-lg-flex" type="button" data-toggle="minimize">
            <span class="typcn typcn-th-menu"></span>
        </button>
    </div>
    <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
        <ul class="navbar-nav mr-lg-2">
            
            <li class="nav-item d-none d-lg-flex">
    <a class="nav-link" href="/pos">
        <i class="typcn typcn-device-desktop menu-icon"></i>
        <span class="menu-title"><b>POS</b></span>
    </a>
</li>

@if(auth()->user()->role === 'admin')
<li class="nav-item d-none d-lg-flex">
    <a class="nav-link" href="{{ route('admin.project.settings.index') }}">
        <i class="typcn typcn-cog menu-icon"></i>
        <span class="menu-title"><b>Settings</b></span>
    </a>
</li>
@endif

            
            <li class="nav-item  d-none d-lg-flex">
                <form method="post" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn"><i class="typcn typcn-power text-primary"></i>LogOut</button>
                </form>
            </li>
        </ul>
        <ul class="navbar-nav navbar-nav-right">
            <li class="nav-item d-none d-lg-flex  mr-2">
                <a class="nav-link" href="#">
                    Help
                </a>
            </li>
            <li class="nav-item dropdown d-flex">
                <a class="nav-link count-indicator dropdown-toggle d-flex justify-content-center align-items-center" id="messageDropdown" href="#" data-toggle="dropdown">
                    <i class="typcn typcn-message-typing"></i>
                    <span class="count bg-success">2</span>
                </a>
                <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="messageDropdown">
                    <p class="mb-0 font-weight-normal float-left dropdown-header">Messages</p>
                    <a class="dropdown-item preview-item">
                        <div class="preview-thumbnail">
                            <img src="{{asset('template/images/faces/face4.jpg')}}" alt="image" class="profile-pic">
                        </div>
                        <div class="preview-item-content flex-grow">
                            <h6 class="preview-subject ellipsis font-weight-normal">David Grey
                            </h6>
                            <p class="font-weight-light small-text mb-0">
                                The meeting is cancelled
                            </p>
                        </div>
                    </a>
                    <a class="dropdown-item preview-item">
                        <div class="preview-thumbnail">
                            <img src="images/faces/face2.jpg" alt="image" class="profile-pic">
                        </div>
                        <div class="preview-item-content flex-grow">
                            <h6 class="preview-subject ellipsis font-weight-normal">Tim Cook
                            </h6>
                            <p class="font-weight-light small-text mb-0">
                                New product launch
                            </p>
                        </div>
                    </a>
                    <a class="dropdown-item preview-item">
                        <div class="preview-thumbnail">
                            <img src="images/faces/face3.jpg" alt="image" class="profile-pic">
                        </div>
                        <div class="preview-item-content flex-grow">
                            <h6 class="preview-subject ellipsis font-weight-normal"> Johnson
                            </h6>
                            <p class="font-weight-light small-text mb-0">
                                Upcoming board meeting
                            </p>
                        </div>
                    </a>
                </div>
            </li>
            <li class="nav-item dropdown d-flex">
                <a class="nav-link count-indicator dropdown-toggle d-flex align-items-center justify-content-center" id="notificationDropdown" href="#" data-toggle="dropdown">
                    <i class="typcn typcn-bell mr-0"></i>
                    <span class="count bg-danger" id="notification-count">0</span>
                </a>
                <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="notificationDropdown" style="width: 350px; max-height: 400px; overflow-y: auto;">
                    <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                        <p class="mb-0 font-weight-bold">Notifications</p>
                        <button class="btn btn-sm btn-outline-primary" id="mark-all-read">Mark All Read</button>
                    </div>
                    <div id="notifications-container">
                        <div class="text-center py-3">
                            <i class="typcn typcn-bell" style="font-size: 48px; color: #ccc;"></i>
                            <p class="text-muted">No notifications</p>
                        </div>
                    </div>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-center" href="#">
                        <small>View All Notifications</small>
                    </a>
                </div>
            </li>
            <li class="nav-item nav-profile dropdown">
                <a class="nav-link dropdown-toggle  pl-0 pr-0" href="#" data-toggle="dropdown" id="profileDropdown">
                    <i class="typcn typcn-user-outline mr-0"></i>
                    <span class="nav-profile-name">{{Auth::user()->name}}</span>
                </a>
                <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
                    <a class="dropdown-item" href="{{route('user.profile')}}"><i class="typcn typcn-user-outline text-primary"></i>My Profile</a>
                    <a class="dropdown-item" href="{{route('plans')}}"><i class="typcn typcn-document-add text-primary"></i>Subscription</a>
                    <a class="dropdown-item" href="{{route('user.settings')}}"><i class="typcn typcn-cog text-primary"></i>User Settings</a>
                    {{-- <a class="dropdown-item">
                        <i class="typcn typcn-cog text-primary"></i>
                        Settings
                    </a> --}}
                    <!-- <a class="dropdown-item">
                        <i class="typcn typcn-power text-primary"></i>
                        Logout
                    </a> -->
                    <form method="post" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-primary  dropdown-item"><i class="typcn typcn-power text-primary"></i>LogOut</button>
                    </form>
                </div>
            </li>
        </ul>
        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
            <span class="typcn typcn-th-menu"></span>
        </button>
    </div>
</nav>
