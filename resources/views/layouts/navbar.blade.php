<div class="quixnav-scroll">
    <ul class="metismenu" id="menu">
        <li class="nav-label first">Main Menu</li>
        @if (Auth::user()->role == 'admin')
            <li><a href="/" aria-expanded="false"><i class="icon icon-single-04"></i><span
                        class="nav-text">Dashboard</span></a></li>
            <li class="nav-label">Apps</li>
            <li><a class="has-arrow" href="javascript:void()" aria-expanded="false"><i
                        class="icon icon-app-store"></i><span class="nav-text">Apps</span></a>
                <ul aria-expanded="false">
                    <li><a href="/admin/customers">Customers</a></li>
                    <li><a href="/admin/orders">Pesanan</a></li>
                    <li><a href="/admin/calendar">Kalender</a></li>
                </ul>
            </li>
        @else
            <li><a href="/" aria-expanded="false"><i class="icon icon-single-04"></i><span
                        class="nav-text">Dashboard</span></a></li>
            <li class="nav-label">Apps</li>
            <li><a class="has-arrow" href="javascript:void()" aria-expanded="false"><i
                        class="icon icon-app-store"></i><span class="nav-text">Apps</span></a>
                <ul aria-expanded="false">
                    <li><a href="/orders">Data Order</a></li>
                    <li><a href="/check-needs"></a></li>
                </ul>
            </li>
        @endif
    </ul>
</div>
