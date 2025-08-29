<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Techesthete</title>
    <link rel="stylesheet" href="{{asset('template/vendors/typicons.font/font/typicons.css')}}">
    <link rel="stylesheet" href="{{asset('template/vendors/css/vendor.bundle.base.css')}}">
    <link rel="stylesheet" href="{{asset('template/css/vertical-layout-light/style.css')}}">
    <link rel="shortcut icon" href="{{asset('template/images/favicon.png')}}" />
    
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css' rel='stylesheet' />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />
    <link href="{{ asset('css/app.css') }}" rel="stylesheet" type="text/css" >

    <!-- Toaster Link -->
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    
</head>

<body>
    <div class="container-scroller">
        @include('layouts.header')
        <div class="container-fluid page-body-wrapper">
            @include('layouts.sidebar-setting')
            @include('layouts.sidebar-role-based')
            <div class="main-panel">
                <div class="content-wrapper">
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="mb-0 font-weight-bold">{{Auth::user()->name}}</h3>
                            <br><br><b><br></b>
                            {{-- <p>Your last login: {{Auth::user()->last_login->format('d M Y H:i:s')}}</p> --}}
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center justify-content-md-end">
                               
                               
                            </div>
                        </div>
                    </div>
                    @yield('content')
                </div>
                @include('layouts.footer')
            </div>
        </div>
    </div>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>

    <script src="{{asset('template/vendors/js/vendor.bundle.base.js')}}"></script>
    <script src="{{asset('template/js/off-canvas.js')}}"></script>
    <script src="{{asset('template/js/hoverable-collapse.js')}}"></script>
    <script src="{{asset('template/js/template.js')}}"></script>
    <script src="{{asset('template/js/settings.js')}}"></script>
    <script src="{{asset('template/js/todolist.js')}}"></script>
    <script src="{{asset('template/vendors/progressbar.js/progressbar.min.js')}}"></script>
    <script src="{{asset('template/vendors/chart.js/Chart.min.js')}}"></script>
    <script src="{{asset('template/js/dashboard.js')}}"></script>


    <!-- Toaster script -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    @if(Session::has('success'))
    <script>
        toastr.options = {
            "closeButton": true,
            "progressBar": true
        }
        toastr.success("{{session('success')}}")
    </script>
    @endif
    @if(Session::has('error'))
    <script>
        toastr.options = {
            "closeButton": true,
            "progressBar": true
        }
        toastr.error("{{session('error')}}")
    </script>
    @endif
    @stack('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- JS Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
    
    <!-- Notification Script -->
    <script>
        $(document).ready(function() {
            loadNotifications();
            
            // Load notifications every 30 seconds
            setInterval(loadNotifications, 30000);
            
            // Mark all as read
            $('#mark-all-read').click(function() {
                $.post('{{ route("notifications.mark-all-read") }}', {
                    _token: '{{ csrf_token() }}'
                }, function() {
                    loadNotifications();
                });
            });
        });
        
        function loadNotifications() {
            $.get('{{ route("notifications.latest") }}', function(data) {
                updateNotificationCount();
                updateNotificationDropdown(data);
            });
        }
        
        function updateNotificationCount() {
            $.get('{{ route("notifications.count") }}', function(data) {
                const count = data.count;
                $('#notification-count').text(count);
                $('#sidebar-notification-count').text(count);
                if (count > 0) {
                    $('#notification-count').show();
                    $('#sidebar-notification-count').show();
                } else {
                    $('#notification-count').hide();
                    $('#sidebar-notification-count').hide();
                }
            });
        }
        
        function updateNotificationDropdown(notifications) {
            const container = $('#notifications-container');
            
            if (notifications.length === 0) {
                container.html(`
                    <div class="text-center py-3">
                        <i class="typcn typcn-bell" style="font-size: 48px; color: #ccc;"></i>
                        <p class="text-muted">No notifications</p>
                    </div>
                `);
                return;
            }
            
            let html = '';
            notifications.forEach(function(notification) {
                const isUnread = !notification.read_at;
                html += `
                    <a class="dropdown-item preview-item ${isUnread ? 'bg-light' : ''}" href="#" onclick="markAsRead('${notification.id}')">
                        <div class="preview-thumbnail">
                            <div class="preview-icon bg-${notification.color}">
                                <i class="${notification.icon} mx-0"></i>
                            </div>
                        </div>
                        <div class="preview-item-content">
                            <h6 class="preview-subject font-weight-normal">${notification.data.title || 'Notification'}</h6>
                            <p class="font-weight-light small-text mb-0">
                                ${notification.data.message || ''}
                            </p>
                            <small class="text-muted">${notification.created_at}</small>
                        </div>
                        ${isUnread ? '<div class="badge badge-primary badge-sm">New</div>' : ''}
                    </a>
                `;
            });
            
            container.html(html);
        }
        
        function markAsRead(notificationId) {
            $.post('{{ route("notifications.mark-read", "") }}/' + notificationId, {
                _token: '{{ csrf_token() }}'
            }, function() {
                loadNotifications();
            });
        }
    </script>
    

</body>


</html>
