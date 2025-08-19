<!DOCTYPE html>
<html lang="en">

<head>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <meta name="description" content="TechWave">
    <meta name="author" content="Radar">

    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <title>Chat Bot - Radar</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        /* .full_logo img,
        .short_logo img {
            filter: grayscale(100%) contrast(150%) brightness(110%);
        } */

        input[type="file"] {
            margin: auto;
            padding: 0.5em;
            border: 2px dashed #2B2830;
            background-color: #17151B;
            transition: border-color .25s ease-in-out;

            &::file-selector-button {
                padding: 1em 1.5em;
                border-width: 0;
                border-radius: 2em;
                background-color: hsl(210 70% 30%);
                color: hsl(210 40% 90%);
                transition: all .25s ease-in-out;
                cursor: pointer;
                margin-right: 1em;
            }

            &:hover {
                border-color: #2B2830;

                &::file-selector-button {

                    background-color: hsl(210 70% 40%);
                }
            }
        }
    </style>


    <script>
        if (!localStorage.frenify_skin) {
            localStorage.frenify_skin = 'dark';
        }
        if (!localStorage.frenify_panel) {
            localStorage.frenify_panel = '';
        }
        document.documentElement.setAttribute("data-techwave-skin", localStorage.frenify_skin);
        if (localStorage.frenify_panel !== '') {
            document.documentElement.classList.add(localStorage.frenify_panel);
        }
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com/">
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Heebo:wght@100;200;300;400;500;600;700;800;900&amp;family=Work+Sans:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&amp;display=swap"
        rel="stylesheet">

    <link type="text/css" rel="stylesheet" href="{{ asset('chat_assets/css/plugins8a54.css?ver=1.0.0') }}" />
    <link type="text/css" rel="stylesheet" href="{{ asset('chat_assets/css/style8a54.css?ver=1.0.0') }}" />


</head>

<body>
    <input type="hidden" name="" id="group-chat">
    <div class="techwave_fn_fixedsub">
        <ul></ul>
    </div>

    <div class="techwave_fn_preloader">
        <svg>
            <circle class="first_circle" cx="50%" cy="50%" r="110"></circle>
            <circle class="second_circle" cx="50%" cy="50%" r="110"></circle>
        </svg>
    </div>

    <div class="techwave_fn_font">
        <a class="font__closer_link fn__icon_button" href="#"><img src="{{ asset('chat_assets/svg/close.svg') }}"
                alt="" class="fn__svg"></a>
        <div class="font__closer"></div>
        <div class="font__dialog">
            <h3 class="title">Font Options</h3>
            <label for="font_size">Font Size</label>
            <select id="font_size">
                <option value="10">10 px</option>
                <option value="12">12 px</option>
                <option value="14">14 px</option>
                <option value="16" selected>16 px</option>
                <option value="18">18 px</option>
                <option value="20">20 px</option>
                <option value="22">22 px</option>
                <option value="24">24 px</option>
                <option value="26">26 px</option>
                <option value="28">28 px</option>
            </select>
            <a href="#" class="apply techwave_fn_button"><span>Apply</span></a>
        </div>
    </div>

    <div class="techwave_fn_chat-type">
        <a class="font__closer_link fn__icon_button" href="#"><img src="{{ asset('chat_assets/svg/close.svg') }}"
                alt="" class="fn__svg"></a>
        <div class="font__closer"></div>
        <div class="font__dialog">
            <h3 class="title">Chat Options</h3>
            <label for="chat-option">Options</label>
            <select id="chat-option">
                <option value="Tanya Jawab">Tanya Jawab</option>
                <option value="Comply ISO 27001">Comply Iso 27001</option>
                <option value="Comply ISO 20000">Comply Iso 20000</option>
            </select>
            <a class="apply techwave_fn_button"><span>Apply</span></a>
        </div>
    </div>

    <div class="techwave_fn_rename">
        <a class="font__closer_link fn__icon_button" href="#"><img src="{{ asset('chat_assets/svg/close.svg') }}"
                alt="" class="fn__svg"></a>
        <div class="font__closer"></div>
        <div class="font__dialog">
            <h3 class="title">Rename Chats</h3>
            <label for="chat-raname">Rename</label>
            <textarea rows="1" placeholder="Enter chats name" id="chat-raname"></textarea>
            <a class="apply techwave_fn_button"><span>Apply</span></a>
        </div>
    </div>

    <div class="techwave_fn_wrapper fn__has_sidebar">
        <div class="techwave_fn_wrap">

            <!-- Searchbar -->
            <div class="techwave_fn_searchbar">
                <div class="search__bar">
                    <input class="search__input" type="text" placeholder="Search here...">
                    <img src="{{ asset('chat_assets/svg/search.svg') }}" alt="" class="fn__svg search__icon">
                    <a class="search__closer" href="#"><img src="{{ asset('chat_assets/svg/close.svg') }}"
                            alt="" class="fn__svg"></a>
                </div>
                <div class="search__results">
                    <!-- Results will come here (via ajax after the integration you made after purchase as it doesn't work in HTML) -->
                    <div class="results__title">Results</div>
                    <div class="results__list">
                        <ul>
                            <li><a href="#">Artificial Intelligence</a></li>
                            <li><a href="#">Learn about the impact of AI on the financial industry</a></li>
                            <li><a href="#">Delve into the realm of AI-driven manufacturing</a></li>
                            <li><a href="#">Understand the ethical implications surrounding AI</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- !Searchbar -->

            <!-- HEADER -->
            <header class="techwave_fn_header">

                <!-- Header left: token information -->
                <div class="header__left">
                    <div class="fn__token_info">
                        {{-- <span class="token_summary">
                            <span class="count">120</span>
                            <span class="text">Tokens<br>Remain</span>
                        </span> --}}
                        <a id="chat_type__trigger" style="cursor: pointer"
                            class="token_upgrade techwave_fn_button"><span id="chat-type"
                                data-type="Tanya Jawab">Tanya Jawab</span></a>
                        <div class="token__popup">
                            Silahkan pilih jenis chat yang ingin Anda gunakan. Setiap jenis chat memiliki fitur dan
                            batasan yang berbeda.
                        </div>
                    </div>
                </div>
                <!-- /Header left: token information -->


                <!-- Header right: navigation bar -->
                <div class="header__right">
                    <div class="fn__nav_bar">

                        <!-- Search (bar item) -->
                        {{-- <div class="bar__item bar__item_search">
                            <a href="#" class="item_opener">
                                <img src="{{ asset('chat_assets/svg/search.svg') }}" alt="" class="fn__svg">
                            </a>
                            <div class="item_popup">
                                <input type="text" placeholder="Search">
                            </div>
                        </div> --}}
                        <!-- !Search (bar item) -->

                        <!-- Notification (bar item) -->
                        {{-- <div class="bar__item bar__item_notification has_notification">
                            <a href="#" class="item_opener">
                                <img src="{{ asset('chat_assets/svg/bell.svg') }}" alt="" class="fn__svg">
                            </a>
                            <div class="item_popup">
                                <div class="ntfc_header">
                                    <h2 class="ntfc_title">Notifications</h2>
                                    <a href="notifications.html">View All</a>
                                </div>
                                <div class="ntfc_list">
                                    <ul>
                                        <li>
                                            <p><a href="notification-single.html">Version 4.1.2 has been launched</a>
                                            </p>
                                            <span>34 Min Ago</span>
                                        </li>
                                        <li>
                                            <p><a href="notification-single.html">Video Generation has been
                                                    released</a></p>
                                            <span>12 Apr</span>
                                        </li>
                                        <li>
                                            <p><a href="notification-single.html">Terms has been updated</a></p>
                                            <span>12 Apr</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div> --}}
                        <!-- !Notification (bar item) -->

                        <!-- Full Screen (bar item) -->
                        <div class="bar__item bar__item_fullscreen">
                            <a href="#" class="item_opener">
                                <img src="{{ asset('chat_assets/svg/fullscreen.svg') }}" alt=""
                                    class="fn__svg f_screen">
                                <img src="{{ asset('chat_assets/svg/smallscreen.svg') }}" alt=""
                                    class="fn__svg s_screen">
                            </a>
                        </div>
                        <!-- !Full Screen (bar item) -->

                        <!-- Language (bar item) -->
                        {{-- <div class="bar__item bar__item_language">
                            <a href="#" class="item_opener">
                                <img src="{{ asset('chat_assets/svg/language.svg') }}" alt=""
                                    class="fn__svg">
                            </a>
                            <div class="item_popup">
                                <ul>
                                    <li>
                                        <span class="active">English</span>
                                    </li>
                                    <li>
                                        <a href="#">Spanish</a>
                                    </li>
                                    <li>
                                        <a href="#">French</a>
                                    </li>
                                </ul>
                            </div>
                        </div> --}}
                        <!-- !Language (bar item) -->

                        <!-- Site Skin (bar item) -->
                        <div class="bar__item bar__item_skin">
                            <a href="#" class="item_opener">
                                <img src="{{ asset('chat_assets/svg/sun.svg') }}" alt=""
                                    class="fn__svg light_mode">
                                <img src="{{ asset('chat_assets/svg/moon.svg') }}" alt=""
                                    class="fn__svg dark_mode">
                            </a>
                        </div>
                        <!-- !Site Skin (bar item) -->

                        <!-- User (bar item) -->
                        <div class="bar__item bar__item_user">
                            <a href="#" class="user_opener">
                                <img src="{{ asset('chat_assets/img/user/user.jpg') }}" alt="">
                            </a>
                            <div class="item_popup">
                                <div class="user_profile">
                                    <div class="user_img">
                                        <img src="{{ asset('chat_assets/img/user/user.jpg') }}" alt="">
                                    </div>
                                    <div class="user_info">
                                        <h2 class="user_name">{{ auth()->user()->name }}<span></span></h2>
                                        <p><a href="mailto:{{ auth()->user()->email }}"
                                                class="user_email">{{ auth()->user()->email }}</a>
                                        </p>
                                    </div>
                                </div>
                                <div class="user_nav">
                                    <ul>
                                        {{-- <li>
                                            <a href="user-profile.html">
                                                <span class="icon"><img
                                                        src="{{ asset('chat_assets/svg/person.svg') }}"
                                                        alt="" class="fn__svg"></span>
                                                <span class="text">Profile</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="user-settings.html">
                                                <span class="icon"><img
                                                        src="{{ asset('chat_assets/svg/setting.svg') }}"
                                                        alt="" class="fn__svg"></span>
                                                <span class="text">Settings</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="user-billing.html">
                                                <span class="icon"><img
                                                        src="{{ asset('chat_assets/svg/billing.svg') }}"
                                                        alt="" class="fn__svg"></span>
                                                <span class="text">Billing</span>
                                            </a>
                                        </li> --}}
                                        <li>
                                            <a id="logout" style="cursor: pointer">
                                                <span class="icon"><img
                                                        src="{{ asset('chat_assets/svg/logout.svg') }}"
                                                        alt="" class="fn__svg"></span>
                                                <span class="text">Log Out</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <!-- !User (bar item) -->


                    </div>
                </div>
                <!-- !Header right: navigation bar -->

            </header>
            <!-- !HEADER -->


            <!-- LEFT PANEL -->
            <div class="techwave_fn_leftpanel">

                <div class="mobile_extra_closer"></div>

                <!-- logo (left panel) -->
                <div class="leftpanel_logo">
                    <a href="/" class="fn_logo">
                        <span class="full_logo">
                            <img src="{{ asset('chat_assets/img/logo-desktop-full.png') }}" alt=""
                                class="desktop_logo">
                            <img src="{{ asset('chat_assets/img/logo-retina-full.png') }}" alt=""
                                class="retina_logo">
                        </span>
                        <span class="short_logo">
                            <img src="{{ asset('chat_assets/img/logo-desktop-mini.png') }}" alt=""
                                class="desktop_logo">
                            <img src="{{ asset('chat_assets/img/logo-retina-mini.png') }}" alt=""
                                class="retina_logo">
                        </span>
                    </a>
                    <a href="#" class="fn__closer fn__icon_button desktop_closer">
                        <img src="{{ asset('chat_assets/svg/arrow.svg') }}" alt="" class="fn__svg">
                    </a>
                    <a href="#" class="fn__closer fn__icon_button mobile_closer">
                        <img src="{{ asset('chat_assets/svg/arrow.svg') }}" alt="" class="fn__svg">
                    </a>
                </div>
                <!-- !logo (left panel) -->

                <!-- content (left panel) -->
                <div class="leftpanel_content">

                    <!-- #1 navigation group -->
                    <div class="nav_group">
                        <h2 class="group__title">Start Here</h2>
                        <ul class="group__list">
                            <li>
                                <a href="/" class="fn__tooltip menu__item" data-position="right"
                                    title="Home">
                                    <span class="icon"><img src="{{ asset('chat_assets/svg/home.svg') }}"
                                            alt="" class="fn__svg"></span>
                                    <span class="text">Home</span>
                                </a>
                            </li>
                            {{-- <li>
                                <a href="community-feed.html" class="fn__tooltip menu__item" data-position="right"
                                    title="Community Feed">
                                    <span class="icon"><img src="{{ asset('chat_assets/svg/community.svg') }}"
                                            alt="" class="fn__svg"></span>
                                    <span class="text">Community Feed</span>
                                </a>
                            </li>
                            <li>
                                <a href="personal-feed.html" class="fn__tooltip menu__item" data-position="right"
                                    title="Personal Feed">
                                    <span class="icon"><img src="{{ asset('chat_assets/svg/person.svg') }}"
                                            alt="" class="fn__svg"></span>
                                    <span class="text">Personal Feed<span class="count">48</span></span>
                                </a>
                            </li>
                            <li>
                                <a href="models.html" class="fn__tooltip menu__item" data-position="right"
                                    title="Finetuned Models">
                                    <span class="icon"><img src="{{ asset('chat_assets/svg/cube.svg') }}"
                                            alt="" class="fn__svg"></span>
                                    <span class="text">Finetuned Models</span>
                                </a>
                            </li> --}}
                        </ul>
                    </div>
                    <!-- !#1 navigation group -->

                    <!-- #2 navigation group -->
                    <div class="nav_group">
                        <h2 class="group__title">User Tools</h2>
                        <ul class="group__list">
                            {{-- <li>
                                <a href="image-generation.html" class="fn__tooltip menu__item" data-position="right"
                                    title="Image Generation">
                                    <span class="icon"><img src="{{ asset('chat_assets/svg/image.svg') }}"
                                            alt="" class="fn__svg"></span>
                                    <span class="text">Image Generation</span>
                                </a>
                            </li> --}}
                            <li>
                                <a href="ai-chat-bot.html" class="fn__tooltip active menu__item"
                                    data-position="right" title="AI Chat Bot">
                                    <span class="icon"><img src="{{ asset('chat_assets/svg/chat.svg') }}"
                                            alt="" class="fn__svg"></span>
                                    <span class="text">AI Chat Bot</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <!-- !#2 navigation group -->

                    <!-- #3 navigation group -->
                    {{-- <div class="nav_group">
                        <h2 class="group__title">Support</h2>
                        <ul class="group__list">
                            <li>
                                <a href="pricing.html" class="fn__tooltip menu__item" data-position="right"
                                    title="Pricing">
                                    <span class="icon"><img src="{{ asset('chat_assets/svg/dollar.svg') }}"
                                            alt="" class="fn__svg"></span>
                                    <span class="text">Pricing</span>
                                </a>
                            </li>
                            <li class="menu-item-has-children">
                                <a href="video-generation.html" class="fn__tooltip menu__item" title="FAQ &amp; Help"
                                    data-position="right">
                                    <span class="icon"><img src="{{ asset('chat_assets/svg/question.svg') }}"
                                            alt="" class="fn__svg"></span>
                                    <span class="text">FAQ &amp; Help</span>
                                    <span class="trigger"><img src="{{ asset('chat_assets/svg/arrow.svg') }}"
                                            alt="" class="fn__svg"></span>
                                </a>
                                <ul class="sub-menu">
                                    <li>
                                        <a href="documentation.html"><span class="text">Documentation</span></a>
                                    </li>
                                    <li>
                                        <a href="faq.html"><span class="text">FAQ</span></a>
                                    </li>
                                    <li>
                                        <a href="changelog.html"><span class="text">Changelog<span
                                                    class="fn__sup">(4.1.2)</span></span></a>
                                    </li>
                                    <li>
                                        <a href="contact.html"><span class="text">Contact Us</span></a>
                                    </li>
                                    <li>
                                        <a href="index-2.html"><span class="text">Home #2</span></a>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <a href="sign-in.html" class="fn__tooltip menu__item" data-position="right"
                                    title="Log Out">
                                    <span class="icon"><img src="{{ asset('chat_assets/svg/logout.svg') }}"
                                            alt="" class="fn__svg"></span>
                                    <span class="text">Log Out</span>
                                </a>
                            </li>
                        </ul>
                    </div> --}}
                    <!-- !#3 navigation group -->


                </div>
                <!-- !content (left panel) -->

            </div>
            <!-- !LEFT PANEL -->


            <!-- CONTENT -->
            <div class="techwave_fn_content">

                <!-- PAGE (all pages go inside this div) -->
                <div class="techwave_fn_page">

                    <!-- AI Chat Bot Page -->
                    <div class="techwave_fn_aichatbot_page fn__chatbot">

                        <div class="chat__page">

                            <div class="font__trigger">
                                <span></span>
                            </div>

                            <div class="fn__title_holder">
                                <div class="container">
                                    <!-- Active chat title -->
                                    {{-- <h1 class="title">Chat Bot Definition</h1> --}}
                                    <!-- !Active chat title -->
                                </div>
                            </div>

                            <div class="container">
                                <div class="chat__list">

                                    <div id="chat0" class="chat__item"></div>

                                    <div class="chat__item active" id="chat1">

                                    </div>

                                    <div class="chat__item" id="chat2"></div>

                                    <div class="chat__item" id="chat3"></div>

                                    <div class="chat__item" id="chat4"></div>

                                </div>
                            </div>


                            <div class="chat__comment">
                                <div class="container">
                                    <div class="fn__chat_comment">
                                        <div class="new__chat">
                                            <p>Ask it questions, engage in discussions, or simply enjoy a friendly chat.
                                            </p>
                                        </div>
                                        <textarea rows="1" class="fn__hidden_textarea" tabindex="-1"></textarea>
                                        <textarea rows="1" placeholder="Send a message..." id="fn__chat_textarea"></textarea>
                                        <input type="file" name="" id="input_file" accept="pdf"
                                            style="display: none; width: 100%">
                                        <button id="generate-btn">
                                            <img src="{{ asset('chat_assets/svg/enter.svg') }}" alt=""
                                                class="fn__svg">&nbsp;&nbsp;Send
                                        </button>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="chat__sidebar">
                            <div class="sidebar_header">
                                <a href="#chat0" class="fn__new_chat_link">
                                    <span class="icon"></span>
                                    <span class="text">New Chat</span>
                                </a>
                            </div>
                            <div class="sidebar_content">
                                {{-- <div class="chat__group new">
                                    <h2 class="group__title">Chats</h2>
                                    <ul class="group__list">
                                        <li class="group__item">
                                            <div class="fn__chat_link active" href="#chat1">
                                                <span class="text">Chat Bot Definition</span>
                                                <input type="text" value="Chat Bot Definition">
                                                <span class="options">
                                                    <button class="trigger"><span></span></button>
                                                    <span class="options__popup">
                                                        <span class="options__list">
                                                            <button class="edit">Edit</button>
                                                            <button class="delete">Delete</button>
                                                        </span>
                                                    </span>
                                                </span>
                                                <span class="save_options">
                                                    <button class="save">
                                                        <img src="{{ asset('chat_assets/svg/check.svg') }}"
                                                            alt="" class="fn__svg">
                                                    </button>
                                                    <button class="cancel">
                                                        <img src="{{ asset('chat_assets/svg/close.svg') }}"
                                                            alt="" class="fn__svg">
                                                    </button>
                                                </span>
                                            </div>
                                        </li>
                                    </ul>
                                </div> --}}
                            </div>
                        </div>

                    </div>
                    <!-- !AI Chat Bot Page -->

                </div>
                <!-- !PAGE (all pages go inside this div) -->


                <!-- FOOTER (inside the content) -->
                {{-- <footer class="techwave_fn_footer">
                    <div class="techwave_fn_footer_content">
                        <div class="copyright">
                            <p>2025© Radar</p>
                        </div>
                        <div class="menu_items">
                            <ul>
                                <li><a href="terms.html">Terms of Service</a></li>
                                <li><a href="privacy.html">Privacy Policy</a></li>
                            </ul>
                        </div>
                    </div>
                </footer> --}}
                <!-- !FOOTER (inside the content) -->

            </div>
            <!-- !CONTENT -->


        </div>
    </div>
    <!-- !MAIN WRAPPER -->



    <!-- Scripts -->
    <!--[if lt IE 10]> <script type="text/javascript" src="js/ie8.js"></script> <![endif]-->
    <script type="text/javascript" src="{{ asset('chat_assets/js/jquery8a54.js?ver=1.0.0') }}"></script>
    <script type="text/javascript" src="{{ asset('chat_assets/js/plugins8a54.js?ver=1.0.0') }}"></script>
    <script type="text/javascript" src="{{ asset('chat_assets/js/init8a54.js?ver=1.0.0') }}"></script>
    <!-- !Scripts -->

    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4="
        crossorigin="anonymous"></script>
    <script>
        // $("#generate-btn").on("click", function(e) {
        //     e.preventDefault();
        // });
    </script>

    <script>
        let ct = $(".techwave_fn_chat-type");

        $("#chat_type__trigger").on("click", function() {
            ct.addClass("opened");
        })

        $(".techwave_fn_chat-type .font__closer_link").on("click", function() {
            ct.removeClass("opened");
        });

        $(".techwave_fn_chat-type .font__closer").on("click", function() {
            ct.removeClass("opened");
        });

        $(".techwave_fn_chat-type .apply").on("click", function(e) {
            let selectedChatType = $("#chat-option").val();
            $("#chat-option").val(selectedChatType);

            $("#chat-type").data("type", selectedChatType);
            $("#chat-type").text(selectedChatType);

            if (selectedChatType === 'Tanya Jawab') {
                $("#fn__chat_textarea").css('display', '');
                $("#input_file").css('display', 'none');
            } else {
                $("#fn__chat_textarea").css('display', 'none');
                $("#input_file").css('display', '');
            }

            ct.removeClass("opened");
        });


        let rn = $(".techwave_fn_rename");

        $(document).on("click", ".edit", function(e) {
            e.stopPropagation();

            rn.addClass("opened");

            // var t = $(this).closest(".fn__chat_link");
            // return t.hasClass("opened") ? t.removeClass("opened") : t.addClass("opened")
        });

        $(".techwave_fn_rename .font__closer_link").on("click", function() {
            rn.removeClass("opened");
        });

        $(".techwave_fn_rename .font__closer").on("click", function() {
            rn.removeClass("opened");
        });

        $(".techwave_fn_rename .apply").on("click", function(e) {
            // let selectedChatType = $("#chat-raname").val();
            // $("#chat-raname").val(selectedChatType);

            // $("#chat-type").data("type", selectedChatType);
            // $("#chat-type").text(selectedChatType);

            // if (selectedChatType === 'Tanya Jawab') {
            //     $("#fn__chat_textarea").css('display', '');
            //     $("#input_file").css('display', 'none');
            // } else {
            //     $("#fn__chat_textarea").css('display', 'none');
            //     $("#input_file").css('display', '');
            // }

            rn.removeClass("opened");
        });

        $("#logout").on("click", function() {
            let token = $("meta[name='csrf-token']").attr('content');
            $.ajax({
                url: '/logout',
                method: 'POST',
                data: {
                    "_token": token
                },
                success: function(response) {
                    location.href = '/login'
                },
                error: function(response) {
                    alert(response.message);
                }
            })
        })
    </script>


    <script>
        // pertama kali user masuk -> check apakah ada group chat terbaru berdasarkan user login
        // ada -> ambil semua chat berdasarkan user login dan grou chat id
        //         ambil semua group chat
        //         isi id group chat di html
        // jika tidak, maka group chat akan dibuat pada saat mengirim pesan
        // pada saat klik new chat, kosongkan id group chat di html
        // pada saat klik kategori chat, kosongkan konten html dan isi dengan chats dari kategori dam isi grup id di html

        $(document).on("click", ".fn__chat_link", function(e) {
            e.preventDefault();

            // 🔄 Pindahkan class active ke item yang diklik
            $(".fn__chat_link").removeClass("active");
            $(this).addClass("active");

            let groupId = $(this).data("id");
            $("#group-chat").val(groupId);

            $(".chat__item.active").empty();

            $.ajax({
                url: '/chat/get-chats',
                method: 'GET',
                data: {
                    group_id: groupId
                },
                success: function(response) {
                    console.log(response);
                    if (response.status) {
                        response.group_chat.chats.forEach(chat => {
                            let bubbleChat = chat.is_user ?
                                `<div class="chat__box your__chat"><div class="author"><span>You</span></div><div class="chat"><p>${chat.message}</p></div></div>` :
                                `<div class="chat__box bot__chat"><div class="author"><span>Radar Bot</span></div><div class="chat">${chat.message}</div></div>`;

                            $(".chat__item.active").append(bubbleChat);
                        });
                    } else {
                        alert('Failed to load chats');
                    }
                },
                error: function(xhr) {
                    alert('Terjadi kesalahan: ' + xhr.statusText);
                }
            });
        });



        $(document).ready(function() {
            $.ajax({
                url: '/chat/get-group-chat',
                method: 'GET',
                success: function(response) {
                    if (response.status) {
                        let html = `
                            <div class="chat__group new">
                                <h2 class="group__title">Chats</h2>
                                <ul class="group__list">
                            `;

                        if (response.kategori.length > 0) {
                            $.each(response.kategori, function(index, chat) {
                                let isActive = (chat.id === response.latest_group_chat.id) ?
                                    'active' : '';
                                let shortTitle = chat.title.length > 23 ? chat.title.substring(
                                    0, 23) + '...' : chat.title;
                                html += `
                                        <li class="group__item">
                                            <div class="fn__chat_link ${isActive}" data-id="${chat.id}">
                                                <span class="text">${shortTitle}</span>
                                                <input type="text" value="${shortTitle}">
                                                <span class="options">
                                                    <button class="trigger"><span></span></button>
                                                    <span class="options__popup">
                                                        <span class="options__list">
                                                            <button class="edit">Edit</button>
                                                            <button class="delete">Delete</button>
                                                        </span>
                                                    </span>
                                                </span>
                                                <span class="save_options">
                                                    <button class="save">
                                                        <img src="/chat_assets/svg/check.svg" alt="" class="fn__svg">
                                                    </button>
                                                    <button class="cancel">
                                                        <img src="/chat_assets/svg/close.svg" alt="" class="fn__svg">
                                                    </button>
                                                </span>
                                            </div>
                                        </li>
                                    `;
                            });

                            let activeChat = $(".chat__item.active");
                            activeChat.empty();

                            response.latest_group_chat.chats.forEach(chat => {
                                let bubbleChat = chat.is_user ?
                                    `<div class="chat__box your__chat"><div class="author"><span>You</span></div><div class="chat"><p>${chat.message}</p></div></div>` :
                                    `<div class="chat__box bot__chat"><div class="author"><span>Radar Bot</span></div><div class="chat">${chat.message}</div></div>`;

                                activeChat.append(bubbleChat);
                            });


                        } else {
                            html += `<li class="group__item"><em>Belum ada chat</em></li>`;
                        }

                        html += `</ul></div>`;

                        // Masukkan ke .sidebar_content
                        $(".sidebar_content").html(html);

                        // Set nilai input hidden group-chat
                        if (response.latest_group_chat) {
                            $("#group-chat").val(response.latest_group_chat.id);
                        } else {
                            $("#group-chat").val('');
                        }
                    } else {
                        alert('Failed to load group chats');
                    }
                },
                error: function(xhr) {
                    alert('Terjadi kesalahan: ' + xhr.statusText);
                }
            });
        });


        $(document).on("click", ".options", function(e) {
            e.stopPropagation();

            var t = $(this).closest(".fn__chat_link");
            return t.hasClass("opened") ? t.removeClass("opened") : t.addClass("opened")
        });
    </script>
</body>

</html>
