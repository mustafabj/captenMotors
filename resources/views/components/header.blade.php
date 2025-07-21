    <!-- Header -->
    <header class="kt-header fixed top-0 z-10 start-0 end-0 flex items-stretch shrink-0 bg-background"
        data-kt-sticky="true" data-kt-sticky-class="border-b border-border" data-kt-sticky-name="header" id="header">
        <!-- Container -->
        <div class="kt-container-fixed flex justify-between items-stretch lg:gap-4" id="headerContainer">
            <!-- Mobile Logo -->
            <div class="flex gap-2.5 lg:hidden items-center -ms-1">
                <a class="shrink-0" href="html/demo1.html">
                    <img class="max-h-[25px] w-full" src="" />
                </a>
                <div class="flex items-center">
                    <button class="kt-btn kt-btn-icon kt-btn-ghost" data-kt-drawer-toggle="#sidebar">
                        <i class="ki-filled ki-menu">
                        </i>
                    </button>
                </div>
            </div>
            <!-- End of Mobile Logo -->
            <div class="flex [.kt-header_&]:below-lg:hidden items-center gap-1.25 text-xs lg:text-sm font-medium mb-2.5 lg:mb-0 [--kt-reparent-target:#contentContainer] lg:[--kt-reparent-target:#headerContainer] [--kt-reparent-mode:prepend] lg:[--kt-reparent-mode:prepend]"
                data-kt-reparent="true">
                <span class="text-mono font-medium ">
                    Home
                </span>
                {{-- <i class="ki-filled ki-right text-muted-foreground text-[10px]">
            </i>
            <span class="text-secondary-foreground">
             Account Home
            </span>
            <i class="ki-filled ki-right text-muted-foreground text-[10px]">
            </i>
            <span class="text-mono font-medium">
             Get Started
            </span> --}}
            </div>
            <!-- Topbar -->
            <div class="flex items-center gap-2.5">
                <!-- Notifications -->
                <button
                    class="kt-btn kt-btn-ghost kt-btn-icon size-9 rounded-full hover:bg-primary/10 hover:[&_i]:text-primary"
                    data-kt-drawer-toggle="#notifications_drawer">
                    <i class="ki-filled ki-notification-status text-lg">
                    </i>
                </button>
                <!--Notifications Drawer-->
                <div class="hidden kt-drawer kt-drawer-end card flex-col max-w-[90%] w-[450px] top-5 bottom-5 end-5 rounded-xl border border-border"
                    data-kt-drawer="true" data-kt-drawer-container="body" id="notifications_drawer">
                    <div class="flex items-center justify-between gap-2.5 text-sm text-mono font-semibold px-5 py-2.5 border-b border-b-border"
                        id="notifications_header">
                        <div class="flex items-center gap-2">
                            <span>Notifications</span>
                            <span id="notification-count" class="kt-badge kt-badge-sm kt-badge-primary hidden">0</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <button class="kt-btn kt-btn-sm kt-btn-outline" id="mark-all-read-btn">
                                Mark all read
                            </button>
                            <button class="kt-btn kt-btn-sm kt-btn-icon kt-btn-dim shrink-0" data-kt-drawer-dismiss="true">
                                <i class="ki-filled ki-cross"></i>
                            </button>
                        </div>
                    </div>
                    <div class="flex-1  p-4" style="overflow-y: auto;">
                        <div id="notifications-list" class="space-y-3">
                            <!-- Notifications will be loaded here -->
                        </div>
                    </div>
                </div>
                <!--End of Notifications Drawer-->
                <!-- End of Notifications -->
                <!-- User -->
                <div class="shrink-0" data-kt-dropdown="true" data-kt-dropdown-offset="10px, 10px"
                    data-kt-dropdown-offset-rtl="-20px, 10px" data-kt-dropdown-placement="bottom-end"
                    data-kt-dropdown-placement-rtl="bottom-start" data-kt-dropdown-trigger="click">
                    <div class="cursor-pointer shrink-0" data-kt-dropdown-toggle="true">
                        <div
                            class="size-9 rounded-full border-2 border-red-500 shrink-0 bg-blue-100 flex justify-center items-center">
                            <span class="inline-block text-blue-500 font-bold">A</span>
                        </div>
                    </div>
                    <div class="kt-dropdown-menu w-[250px]" data-kt-dropdown-menu="true">
                        <div class="flex items-center justify-between px-2.5 py-1.5 gap-1.5">
                            <div class="flex items-center gap-2">
                                <div
                                    class="size-9 rounded-full border-2 border-red-500 shrink-0 bg-blue-100 flex justify-center items-center">
                                    <span class="inline-block text-blue-500 font-bold">A</span>
                                </div>
                                <div class="flex flex-col gap-1.5">
                                    <span class="text-sm text-foreground font-semibold leading-none">
                                        Admin
                                    </span>
                                    <a class="text-xs text-secondary-foreground hover:text-primary font-medium leading-none"
                                        href="html/demo1/account/home/get-started.html">
                                        admin@captenmotors.com
                                    </a>
                                </div>
                            </div>
                        </div>
                        <ul class="kt-dropdown-menu-sub">
                            <li>
                                <div class="kt-dropdown-menu-separator">
                                </div>
                            </li>
                            <li>
                                <a class="kt-dropdown-menu-link" href="html/demo1/account/home/user-profile.html">
                                    <i class="ki-filled ki-profile-circle">
                                    </i>
                                    My Account
                                </a>
                            </li>
                            <li data-kt-dropdown="true" data-kt-dropdown-placement="right-start"
                                data-kt-dropdown-trigger="hover">
                                <button class="kt-dropdown-menu-toggle py-1" data-kt-dropdown-toggle="true">
                                    <span class="flex items-center gap-2">
                                        <i class="ki-filled ki-icon">
                                        </i>
                                        Language
                                    </span>
                                    <span class="ms-auto kt-badge kt-badge-stroke shrink-0">
                                        English
                                        <img alt="" class="inline-block size-3.5 rounded-full"
                                            src="" />
                                    </span>
                                </button>
                                <div class="kt-dropdown-menu w-[180px]" data-kt-dropdown-menu="true">
                                    <ul class="kt-dropdown-menu-sub">
                                        <li class="active">
                                            <a class="kt-dropdown-menu-link" href="?dir=ltr">
                                                <span class="flex items-center gap-2">
                                                    <img alt="" class="inline-block size-4 rounded-full"
                                                        src="" />
                                                    <span class="kt-menu-title">
                                                        English
                                                    </span>
                                                </span>
                                                <i class="ki-solid ki-check-circle ms-auto text-green-500 text-base">
                                                </i>
                                            </a>
                                        </li>
                                        <li class="">
                                            <a class="kt-dropdown-menu-link" href="?dir=rtl">
                                                <span class="flex items-center gap-2">
                                                    <img alt="" class="inline-block size-4 rounded-full"
                                                        src="" />
                                                    <span class="kt-menu-title">
                                                        Arabic
                                                    </span>
                                                </span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <li>
                                <div class="kt-dropdown-menu-separator">
                                </div>
                            </li>
                        </ul>
                        <div class="px-2.5 pt-1.5 mb-2.5 flex flex-col gap-3.5">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="kt-btn kt-btn-outline justify-center w-full">
                                    Log out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- End of User -->
            </div>
            <!-- End of Topbar -->
        </div>
        <!-- End of Container -->
    </header>
    <!-- End of Header -->
