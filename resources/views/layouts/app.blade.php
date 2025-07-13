<!DOCTYPE html>
<html class="h-full" data-kt-theme="true" data-kt-theme-mode="light" dir="ltr" lang="en">
<head>
    <title>
        Capten Motors - Dashboard
    </title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="robots" content="follow, index" />
    <meta name="description" content="Capten Motors - Dashboard" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <link rel="canonical" href="{{ url()->current() }}" />
    <link rel="icon" href="{{ asset('assets/media/app/favicon.ico') }}" type="image/x-icon" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/media/app/apple-touch-icon.png') }}" />
    <link rel="icon" sizes="32x32" href="{{ asset('assets/media/app/favicon-32x32.png') }}" type="image/png" />
    <link rel="icon" sizes="16x16" href="{{ asset('assets/media/app/favicon-16x16.png') }}" type="image/png" />

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />

    <!-- Vendor CSS -->
    <link href="@versioned('assets/vendors/apexcharts/apexcharts.css')" rel="stylesheet" />
    <link href="@versioned('assets/vendors/keenicons/styles.bundle.css')" rel="stylesheet" />

    <!-- Main CSS -->
    <link href="@versioned('assets/css/styles.css')" rel="stylesheet" />
    <link href="@versioned('css/pages/cars-form.css')" rel="stylesheet" />

</head>

<body class="antialiased flex h-full text-base text-foreground bg-background demo1 kt-sidebar-fixed kt-header-fixed" data-route="{{ Route::currentRouteName() }}" data-asset-version="{{ config('assets.version', '1.0.0') }}">
    <!-- Theme Mode -->
    <script>
        const defaultThemeMode = 'light'; // light|dark|system
        let themeMode;

        if (document.documentElement) {
            if (localStorage.getItem('kt-theme')) {
                themeMode = localStorage.getItem('kt-theme');
            } else if (
                document.documentElement.hasAttribute('data-kt-theme-mode')
            ) {
                themeMode =
                    document.documentElement.getAttribute('data-kt-theme-mode');
            } else {
                themeMode = defaultThemeMode;
            }

            if (themeMode === 'system') {
                themeMode = window.matchMedia('(prefers-color-scheme: dark)').matches ?
                    'dark' :
                    'light';
            }

            document.documentElement.classList.add(themeMode);
        }
    </script>
    <!-- End of Theme Mode -->
    <!-- Page -->
    <!-- Main -->
    <div class="flex grow">
        @include('components.sidebar')
        <!-- Wrapper -->
        <div class="kt-wrapper flex grow flex-col">
            @include('components.header')
            <!-- Content -->
            <main class="grow pt-5" id="content" role="content">
                <!-- Container -->
                <div class="kt-container-fixed" id="contentContainer">
                </div>
                <!-- End of Container -->
                <!-- Container -->
                <div class="kt-container-fixed">
                    <div class="grid gap-5 lg:gap-7.5">
                        @yield('content')
                        <!-- begin: grid -->
                        {{-- <div class="grid lg:grid-cols-3 gap-5 lg:gap-7.5 items-stretch">
                                <div class="lg:col-span-1">
                                <div class="kt-card h-full">

                                </div>
                                </div>
                                <div class="lg:col-span-2">
                                <div class="grid">
                                <div class="kt-card kt-card-grid h-full min-w-full">
                                    test
                                </div>
                                </div>
                                </div>
                            </div> --}}
                        <!-- end: grid -->
                    </div>
                </div>
                <!-- End of Container -->
            </main>
            <!-- End of Content -->
            @include('components.footer')
        </div>
        <!-- End of Wrapper -->
    </div>
    <!-- End of Main -->
    <!-- End of Page -->
    <!-- Scripts -->
    <!-- Core JS -->
    <script src="{{ asset('assets/js/core.bundle.js') }}"></script>

    <!-- Vendor JS -->
    <script src="{{ asset('assets/vendors/ktui/ktui.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/apexcharts/apexcharts.min.js') }}"></script>

    <!-- Custom Widgets and Layout -->
    <script src="{{ asset('assets/js/widgets/general.js') }}"></script>
    <script src="{{ asset('assets/js/layouts/demo1.js') }}"></script>
    
    <!-- Application JS (Load First) -->
    <script src="@versionedJs('app.js')"></script>
    
    <!-- Loader System (Load Second) -->
    <script src="@versionedJs('config/loader.js')"></script>
    
    <!-- Common Components (Load Third) -->
    <script src="@versionedJs('components/modal.js')"></script>
    
    <!-- Page-specific Scripts (Load Last) -->
    @stack('scripts')
    <!-- End of Scripts -->
</body>

</html>
