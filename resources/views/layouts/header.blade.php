<div class="page-header">
    <div class="header-wrapper row m-0">
        <form class="form-inline search-full col" action="#" method="get">
            <div class="form-group w-100">
                <div class="Typeahead Typeahead--twitterUsers">
                    <div class="u-posRelative">
                        <input class="demo-input Typeahead-input form-control-plaintext w-100" type="text"
                            placeholder="Search Riho .." name="q" title="" autofocus>
                        <div class="spinner-border Typeahead-spinner" role="status"><span
                                class="sr-only">Loading... </span></div><i class="close-search"
                            data-feather="x"></i>
                    </div>
                    <div class="Typeahead-menu"> </div>
                </div>
            </div>
        </form>
        <div class="header-logo-wrapper col-auto p-0">
            <div class="logo-wrapper"> <a href="index.html"><img class="img-fluid for-light"
                        src="{{ asset('dashboard_assets/assets/images/logo/logo_dark.png') }}" alt="logo-light"><img
                        class="img-fluid for-dark" src="{{ asset('dashboard_assets/assets/images/logo/logo.png') }}"
                        alt="logo-dark"></a></div>
            <div class="toggle-sidebar"> <i class="status_toggle middle sidebar-toggle"
                    data-feather="align-center"></i></div>
        </div>
        <div class="left-header col-xxl-5 col-xl-6 col-lg-5 col-md-4 col-sm-3 p-0">
            <div> <a class="toggle-sidebar" href="#"> <i class="iconly-Category icli"> </i></a>
                <div class="d-flex align-items-center gap-2 ">
                    <h4 class="f-w-600">Selamat datang {{ auth()->user()->name }}</h4><img class="mt-0"
                        src="{{ asset('dashboard_assets/assets/images/hand.gif') }}" alt="hand-gif">
                </div>
            </div>
            <div class="welcome-content d-xl-block d-none"><span class="text-truncate col-12">Manage Produk Anda Pada Aplikasi Ini </span></div>
        </div>
        <div class="nav-right col-xxl-7 col-xl-6 col-md-7 col-8 pull-right right-header p-0 ms-auto">
            <ul class="nav-menus">
                {{-- <li class="d-md-block d-none">
                    <div class="form search-form mb-0">
                        <div class="input-group"><span class="input-icon">
                                <svg>
                                    <use href="{{ asset('dashboard_assets/assets/svg/icon-sprite.svg#search-header') }}"></use>
                                </svg>
                                <input class="w-100" type="search" placeholder="Search"></span></div>
                    </div>
                </li> --}}
                <li class="d-md-none d-block">
                    <div class="form search-form mb-0">
                        <div class="input-group"> <span class="input-show">
                                <svg id="searchIcon">
                                    <use href="{{ asset('dashboard_assets/assets/svg/icon-sprite.svg#search-header') }}"></use>
                                </svg>
                                <div id="searchInput">
                                    <input type="search" placeholder="Search">
                                </div>
                            </span></div>
                    </div>
                </li>
                <li class="onhover-dropdown">
                    <svg>
                        <use href="{{ asset('dashboard_assets/assets/svg/icon-sprite.svg#star') }}"></use>
                    </svg>
                    <div class="onhover-show-div bookmark-flip">
                        <div class="flip-card">
                            <div class="flip-card-inner">
                                <div class="front">
                                    <h6 class="f-18 mb-0 dropdown-title">Shorcut</h6>
                                    <ul class="bookmark-dropdown">
                                        <li>
                                            <div class="row">
                                                <div class="col-4 text-center">
                                                    <div class="bookmark-content">
                                                        <div class="bookmark-icon"><i class="fa fa-tachometer" aria-hidden="true"></i></div>
                                                        <span>Dashboard</span>
                                                    </div>
                                                </div>
                                                <div class="col-4 text-center">
                                                    <div class="bookmark-content">
                                                        <div class="bookmark-icon"><i class="fa fa-user" aria-hidden="true"></i>
                                                        </div><span>Profile</span>
                                                    </div>
                                                </div>
                                                <div class="col-4 text-center">
                                                    <div class="bookmark-content">
                                                        <div class="bookmark-icon"><i class="fa fa-unlock-alt" aria-hidden="true"></i></div>
                                                        <span>Reset Password</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="mode"><i class="moon" data-feather="moon"> </i></div>
                </li>
                {{-- <li class="onhover-dropdown notification-down">
                    <div class="notification-box">
                        <svg>
                            <use href="{{ asset('dashboard_assets/assets/svg/icon-sprite.svg#notification-header') }}"></use>
                        </svg><span class="badge rounded-pill badge-secondary">4 </span>
                    </div>
                    <div class="onhover-show-div notification-dropdown">
                        <div class="card mb-0">
                            <div class="card-header">
                                <div class="common-space">
                                    <h4 class="text-start f-w-600">Notitications</h4>
                                    <div><span>4 New</span></div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="notitications-bar">
                                    <ul class="nav nav-pills nav-primary p-0" id="pills-tab" role="tablist">
                                        <li class="nav-item p-0"> <a class="nav-link active"
                                                id="pills-aboutus-tab" data-bs-toggle="pill"
                                                href="#pills-aboutus" role="tab"
                                                aria-controls="pills-aboutus" aria-selected="true">All(3)</a>
                                        </li>
                                        <li class="nav-item p-0"> <a class="nav-link" id="pills-blog-tab"
                                                data-bs-toggle="pill" href="#pills-blog" role="tab"
                                                aria-controls="pills-blog" aria-selected="false">
                                                Messages</a></li>
                                        <li class="nav-item p-0"> <a class="nav-link"
                                                id="pills-contactus-tab" data-bs-toggle="pill"
                                                href="#pills-contactus" role="tab"
                                                aria-controls="pills-contactus" aria-selected="false">
                                                Cart </a></li>
                                    </ul>
                                    <div class="tab-content" id="pills-tabContent">
                                        <div class="tab-pane fade show active" id="pills-aboutus"
                                            role="tabpanel" aria-labelledby="pills-aboutus-tab">
                                            <div class="user-message">
                                                <div class="cart-dropdown notification-all">
                                                    <ul>
                                                        <li class="pr-0 pl-0 pb-3 pt-3">
                                                            <div class="media"><img
                                                                    class="img-fluid b-r-5 me-3 img-60"
                                                                    src="{{ asset('dashboard_assets/assets/images/other-images/receiver-img.jpg') }}"
                                                                    alt="">
                                                                <div class="media-body"><a
                                                                        class="f-light f-w-500"
                                                                        href="{{ asset('dashboard_assets/template/product.html') }}">Men
                                                                        Blue T-Shirt</a>
                                                                    <div class="qty-box">
                                                                        <div class="input-group"> <span
                                                                                class="input-group-prepend">
                                                                                <button
                                                                                    class="btn quantity-left-minus"
                                                                                    type="button"
                                                                                    data-type="minus"
                                                                                    data-field="">-
                                                                                </button></span>
                                                                            <input
                                                                                class="form-control input-number"
                                                                                type="text" name="quantity"
                                                                                value="1"><span
                                                                                class="input-group-prepend">
                                                                                <button
                                                                                    class="btn quantity-right-plus"
                                                                                    type="button"
                                                                                    data-type="plus"
                                                                                    data-field="">+</button></span>
                                                                        </div>
                                                                    </div>
                                                                    <h6 class="font-primary">$695.00</h6>
                                                                </div>
                                                                <div class="close-circle"><a class="bg-danger"
                                                                        href="#"><i
                                                                            data-feather="x"></i></a></div>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <ul>
                                                    <li>
                                                        <div class="user-alerts"><img
                                                                class="user-image rounded-circle img-fluid me-2"
                                                                src="{{ asset('dashboard_assets/assets/images/dashboard/user/5.jpg') }}"
                                                                alt="user" />
                                                            <div class="user-name">
                                                                <div>
                                                                    <h6><a class="f-w-500 f-14"
                                                                            href="{{ asset('dashboard_assets/template/user-profile.html') }}">Floyd
                                                                            Miles</a></h6><span
                                                                        class="f-12 light-font"> commented on
                                                                        your photo </span><span
                                                                        class="f-12 light-font">20 min
                                                                        ago</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <div class="user-alerts"><img
                                                                class="user-image rounded-circle img-fluid me-2"
                                                                src="{{ asset('dashboard_assets/assets/images/dashboard/user/8.jpg') }}"
                                                                alt="user" />
                                                            <div class="user-name">
                                                                <div>
                                                                    <h6><a class="f-w-500 f-14"
                                                                            href="{{ asset('dashboard_assets/template/user-profile.html') }}">Willie
                                                                            Cunningham</a></h6><span
                                                                        class="f-12 light-font"> commented on
                                                                        your photo </span><span
                                                                        class="f-12 light-font">2 hour
                                                                        ago</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <div class="user-alerts"><img
                                                                class="user-image rounded-circle img-fluid me-2"
                                                                src="{{ asset('dashboard_assets/assets/images/dashboard/user/3.jpg') }}"
                                                                alt="user" />
                                                            <div class="user-name">
                                                                <div>
                                                                    <h6><a class="f-w-500 f-14"
                                                                            href="{{ asset('dashboard_assets/template/user-profile.html') }}">Jessica
                                                                            King</a></h6><span
                                                                        class="f-12 light-font"> commented on
                                                                        your photo </span><span
                                                                        class="f-12 light-font">3 hour
                                                                        ago</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li>
                                                </ul>
                                                <div class="user-message text-center">
                                                    <a class="f-w-700" href="javascript:void(0)">See all</a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="pills-blog" role="tabpanel"
                                            aria-labelledby="pills-blog-tab">
                                            <div class="user-message">
                                                <div class="cart-dropdown notification-all">
                                                    <ul>
                                                        <li class="pr-0 pl-0 pb-3 pt-3">
                                                            <div class="media"><img
                                                                    class="img-fluid b-r-5 me-3 img-60"
                                                                    src="{{ asset('dashboard_assets/assets/images/other-images/receiver-img.jpg') }}"
                                                                    alt="">
                                                                <div class="media-body"><a
                                                                        class="f-light f-w-500"
                                                                        href="{{ asset('dashboard_assets/template/product.html') }}">Men
                                                                        Blue T-Shirt</a>
                                                                    <div class="qty-box">
                                                                        <div class="input-group"> <span
                                                                                class="input-group-prepend">
                                                                                <button
                                                                                    class="btn quantity-left-minus"
                                                                                    type="button"
                                                                                    data-type="minus"
                                                                                    data-field="">-
                                                                                </button></span>
                                                                            <input
                                                                                class="form-control input-number"
                                                                                type="text" name="quantity"
                                                                                value="1"><span
                                                                                class="input-group-prepend">
                                                                                <button
                                                                                    class="btn quantity-right-plus"
                                                                                    type="button"
                                                                                    data-type="plus"
                                                                                    data-field="">+</button></span>
                                                                        </div>
                                                                    </div>
                                                                    <h6 class="font-primary">$695.00</h6>
                                                                </div>
                                                                <div class="close-circle"><a class="bg-danger"
                                                                        href="#"><i
                                                                            data-feather="x"></i></a></div>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <ul>
                                                    <li>
                                                        <div class="user-alerts"><img
                                                                class="user-image rounded-circle img-fluid me-2"
                                                                src="{{ asset('dashboard_assets/assets/images/dashboard/user/5.jpg') }}"
                                                                alt="user" />
                                                            <div class="user-name">
                                                                <div>
                                                                    <h6><a class="f-w-500 f-14"
                                                                            href="{{ asset('dashboard_assets/template/user-profile.html') }}">Floyd
                                                                            Miles</a></h6><span
                                                                        class="f-12 light-font"> commented on
                                                                        your photo </span><span
                                                                        class="f-12 light-font">20 min
                                                                        ago</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <div class="user-alerts"><img
                                                                class="user-image rounded-circle img-fluid me-2"
                                                                src="{{ asset('dashboard_assets/assets/images/dashboard/user/8.jpg') }}"
                                                                alt="user" />
                                                            <div class="user-name">
                                                                <div>
                                                                    <h6><a class="f-w-500 f-14"
                                                                            href="{{ asset('dashboard_assets/template/user-profile.html') }}">Willie
                                                                            Cunningham</a></h6><span
                                                                        class="f-12 light-font"> commented on
                                                                        your photo </span><span
                                                                        class="f-12 light-font">2 hour
                                                                        ago</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <div class="user-alerts"><img
                                                                class="user-image rounded-circle img-fluid me-2"
                                                                src="{{ asset('dashboard_assets/assets/images/dashboard/user/3.jpg') }}"
                                                                alt="user" />
                                                            <div class="user-name">
                                                                <div>
                                                                    <h6><a class="f-w-500 f-14"
                                                                            href="{{ asset('dashboard_assets/template/user-profile.html') }}">Jessica
                                                                            King</a></h6><span
                                                                        class="f-12 light-font"> commented on
                                                                        your photo </span><span
                                                                        class="f-12 light-font">3 hour
                                                                        ago</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li>
                                                </ul>
                                                <div class="user-message text-center">
                                                    <a class="f-w-700" href="javascript:void(0)">See all</a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="pills-contactus" role="tabpanel"
                                            aria-labelledby="pills-contactus-tab">
                                            <div class="user-message">
                                                <div class="cart-dropdown notification-all">
                                                    <ul>
                                                        <li class="pr-0 pl-0 pb-3 pt-3">
                                                            <div class="media"><img
                                                                    class="img-fluid b-r-5 me-3 img-60"
                                                                    src="{{ asset('dashboard_assets/assets/images/other-images/receiver-img.jpg') }}"
                                                                    alt="">
                                                                <div class="media-body"><a
                                                                        class="f-light f-w-500"
                                                                        href="{{ asset('dashboard_assets/template/product.html') }}">Men
                                                                        Blue T-Shirt</a>
                                                                    <div class="qty-box">
                                                                        <div class="input-group"> <span
                                                                                class="input-group-prepend">
                                                                                <button
                                                                                    class="btn quantity-left-minus"
                                                                                    type="button"
                                                                                    data-type="minus"
                                                                                    data-field="">-
                                                                                </button></span>
                                                                            <input
                                                                                class="form-control input-number"
                                                                                type="text" name="quantity"
                                                                                value="1"><span
                                                                                class="input-group-prepend">
                                                                                <button
                                                                                    class="btn quantity-right-plus"
                                                                                    type="button"
                                                                                    data-type="plus"
                                                                                    data-field="">+</button></span>
                                                                        </div>
                                                                    </div>
                                                                    <h6 class="font-primary">$695.00</h6>
                                                                </div>
                                                                <div class="close-circle"><a class="bg-danger"
                                                                        href="#"><i
                                                                            data-feather="x"></i></a></div>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <ul>
                                                    <li>
                                                        <div class="user-alerts"><img
                                                                class="user-image rounded-circle img-fluid me-2"
                                                                src="{{ asset('dashboard_assets/assets/images/dashboard/user/5.jpg') }}"
                                                                alt="user" />
                                                            <div class="user-name">
                                                                <div>
                                                                    <h6><a class="f-w-500 f-14"
                                                                            href="{{ asset('dashboard_assets/template/user-profile.html') }}">Floyd
                                                                            Miles</a></h6><span
                                                                        class="f-12 light-font"> commented on
                                                                        your photo </span><span
                                                                        class="f-12 light-font">20 min
                                                                        ago</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <div class="user-alerts"><img
                                                                class="user-image rounded-circle img-fluid me-2"
                                                                src="{{ asset('dashboard_assets/assets/images/dashboard/user/8.jpg') }}"
                                                                alt="user" />
                                                            <div class="user-name">
                                                                <div>
                                                                    <h6><a class="f-w-500 f-14"
                                                                            href="{{ asset('dashboard_assets/template/user-profile.html') }}">Willie
                                                                            Cunningham</a></h6><span
                                                                        class="f-12 light-font"> commented on
                                                                        your photo </span><span
                                                                        class="f-12 light-font">2 hour
                                                                        ago</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <div class="user-alerts"><img
                                                                class="user-image rounded-circle img-fluid me-2"
                                                                src="{{ asset('dashboard_assets/assets/images/dashboard/user/3.jpg') }}"
                                                                alt="user" />
                                                            <div class="user-name">
                                                                <div>
                                                                    <h6><a class="f-w-500 f-14"
                                                                            href="{{ asset('dashboard_assets/template/user-profile.html') }}">Jessica
                                                                            King</a></h6><span
                                                                        class="f-12 light-font"> commented on
                                                                        your photo </span><span
                                                                        class="f-12 light-font">3 hour
                                                                        ago</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li>
                                                </ul>
                                                <div class="user-message text-center">
                                                    <a class="f-w-700" href="javascript:void(0)">See all</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="bottom-btn"><a href="javascript:void(0)">View all</a></div>
                            </div>
                        </div>
                    </div>
                </li> --}}
                <li style="padding-left: 40px; padding-right: 40px; padding-top: 40px">
                    <div class="user-nav onhover-dropdown">
                        <div class="user-name media">
                            <div class="user"><img class="img-fluid rounded-circle"
                                    src="{{ asset('dashboard_assets/assets/  images/dashboard/user.png') }}" alt="">
                                <ul class="profile-dropdown onhover-show-div p-20">
                                    <li><a href="/profile"><i
                                                data-feather="user"></i>Profile</a></li>
                                    <li id="logout"><a><i
                                                data-feather="log-out"></i>Log out</a></li>
                                </ul>
                            </div>
                            <div class="media-body">
                                <h6>{{ auth()->user()->name }}</h6>
                                <p>{{ auth()->user()->role }}</p>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
            <div class="d-lg-none mobile-toggle pull-right"><i data-feather="more-horizontal"></i></div>
        </div>
    </div>
</div>