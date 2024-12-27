<div class="left-side-menu">

    <div class="h-100" data-simplebar>
        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <ul id="side-menu">
                <li class="menu-title">Menu</li>
                <li>
                    <a href="{{ route('home') }}">
                        <i class="mdi mdi-view-dashboard-outline"></i>
                        <span> Beranda </span>
                    </a>
                </li>
                @php
                    $daftar_menu = filter_menu();
                    $daftar_menu1 = sub_menu();
                    $daftar_menu2 = sub_menu1();
                @endphp
                @foreach ($daftar_menu as $key => $menu)
                    @if ($menu->name !='' && $menu->name!= null)    
                        <li>
                            <a href="{{ route($menu->name) }}">
                                <i class="{{ $menu->icon_menu }}"></i>
                                <span> {{ $menu->display_name }} </span>
                            </a>
                        </li>
                    @else
                        <li>
                            <a href="#menu{{ $key+1 }}" data-bs-toggle="collapse">
                                <i class="{{ $menu->icon_menu }}"></i>
                                <span> {{ $menu->display_name }} </span>
                                <span class="menu-arrow"></span>
                            </a>
                            <div class="collapse" id="menu{{ $key+1 }}">
                                <ul class="nav-second-level">
                                    @foreach ($daftar_menu1 as $submenu)
                                        @php
                                            $menu1 = $submenu->name;
                                        @endphp
                                        @if ($menu->id == $submenu->parent_id)
                                            @if ($submenu->name)
                                                <li>
                                                    <a href="{{ route($menu1) }}">
                                                        <span> {{ $submenu->display_name }} </span>
                                                    </a>
                                                </li>
                                            @else 
                                            <li>
                                                <a href="#menulevel{{ $key+1 }}" data-bs-toggle="collapse">
                                                    Lampiran I <span class="menu-arrow"></span>
                                                </a>
                                                <div class="collapse" id="menulevel{{ $key+1 }}">
                                                    <ul class="nav-second-level">
                                                        @foreach ($daftar_menu2 as $submenu1)
                                                            @php
                                                                $menu2 = $submenu1->name;
                                                            @endphp
                                                            @if ($submenu->id == $submenu1->parent_id)
                                                                <li><a href="{{ route($menu2) }}">{{ $submenu1->display_name }}</a>
                                                                </li>
                                                            @endif
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </li>
                                            @endif
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>
        <!-- End Sidebar -->
        <div class="clearfix"></div>
    </div>
    <!-- Sidebar -left -->
</div>