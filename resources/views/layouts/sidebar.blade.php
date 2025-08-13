<div class="sidebar-wrapper" data-layout="stroke-svg">

    <div class="logo-wrapper">
        <a href="/dashboard">
            <img class="img-fluid" width="40px" src="{{ asset('landing_assets/images/logo-light.png') }}" alt=""
                style="width: 80px">
        </a>
        <div class="back-btn">
            <i class="fa fa-angle-left"> </i>
        </div>
        <div class="toggle-sidebar">
            <i class="status_toggle middle sidebar-toggle" data-feather="grid"> </i>
        </div>
    </div>


    <div class="logo-icon-wrapper">
        <a href="index.html">
            <img class="img-fluid" src="{{ asset('landing_assets/images/logo-light.png') }}" alt="">
        </a>
    </div>

    <nav class="sidebar-main">
        <div class="left-arrow" id="left-arrow"><i data-feather="arrow-left"></i></div>
        <div id="sidebar-menu">
            <ul class="sidebar-links" id="simple-bar">

                <li class="back-btn">
                    <a href="index.html">
                        <img class="img-fluid" src="{{ asset('landing_assets/images/logo-light.png') }}" alt="">
                    </a>
                    <div class="mobile-back text-end">
                        <span>Back </span><i class="fa fa-angle-right ps-2" aria-hidden="true"></i>
                    </div>
                </li>
                <li class="pin-title sidebar-main-title">
                    <div>
                        <h6>Pinned</h6>
                    </div>
                </li>
                <li class="sidebar-main-title">
                    <div>
                        <h6 class="">Dashboard</h6>
                    </div>
                </li>
                <li class="sidebar-list"><i class="fa fa-thumb-tack"></i><a class="sidebar-link sidebar-title link-nav"
                        href="/dashboard">
                        <i class="fa fa-tachometer text-white" aria-hidden="true"></i>
                        </svg><span class="">Dashboard</span></a>
                </li>

                <li class="sidebar-main-title">
                    <div>
                        <h6 class="">Master</h6>
                    </div>
                </li>

                <li class="sidebar-list" style="margin-top: -20px"><i class="fa fa-thumb-users"></i><a
                        class="sidebar-link sidebar-title link-nav" href="/users">
                        <i class="fa fa-users text-white" aria-hidden="true"></i>
                        </svg><span class="">Data User</span></a>
                </li>

                <li class="sidebar-list" style="margin-top: -20px"><i class="fa fa-thumb-users"></i><a
                        class="sidebar-link sidebar-title link-nav" href="/dokumen">
                        <i class="fa fa-users text-white" aria-hidden="true"></i>
                        </svg><span class="">Dokumen</span></a>
                </li>

                <li class="sidebar-main-title">
                    <div>
                        <h6 class="">Settings</h6>
                    </div>
                </li>

            </ul>
            <div class="right-arrow" id="right-arrow"><i data-feather="arrow-right"></i></div>
        </div>
    </nav>
</div>
