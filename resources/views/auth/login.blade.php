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
    <meta name="user-id" content="{{ auth()->id() }}" />
    <meta name="pusher-key" content="{{ config('broadcasting.connections.pusher.key') }}" />
    <meta name="pusher-cluster" content="{{ config('broadcasting.connections.pusher.options.cluster') }}" />

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

    <!-- Vite CSS (load first for custom classes) -->
    
    <!-- Main CSS (load after to override) -->
    <link href="@versioned('assets/css/styles.css')" rel="stylesheet" />
    <link href="@versioned('css/pages/cars-form.css')" rel="stylesheet" />

</head>

<body class="antialiased flex h-full text-base text-foreground bg-background demo1 kt-sidebar-fixed kt-header-fixed" data-route="{{ Route::currentRouteName() }}" data-asset-version="{{ config('assets.version', '1.0.0') }}">
    <div class="flex items-center justify-center grow bg-center bg-no-repeat page-bg min-h-screen">
        <div class="kt-card max-w-[370px] w-full">
            <form action="{{ route('login') }}" class="kt-card-content flex flex-col gap-5 p-10" id="sign_in_form" method="POST">
                @csrf
                <div class="flex flex-col gap-1">
                    <label class="kt-form-label font-normal text-mono" for="email">
                        {{ __('Email') }}
                    </label>
                    <input class="kt-input" id="email" name="email" type="email" placeholder="email@email.com" value="{{ old('email') }}" required autofocus autocomplete="username" />
                    @error('email')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>
                <div class="flex flex-col gap-1">
                    <div class="flex items-center justify-between gap-1">
                        <label class="kt-form-label font-normal text-mono" for="password">
                            {{ __('Password') }}
                        </label>
                    </div>
                    <div class="kt-input" data-kt-toggle-password="true">
                        <input id="password" name="password" placeholder="{{ __('Enter Password') }}" type="password" required autocomplete="current-password" />
                        <button class="kt-btn kt-btn-sm kt-btn-ghost kt-btn-icon bg-transparent! -me-1.5" data-kt-toggle-password-trigger="true" type="button" tabindex="-1">
                            <span class="kt-toggle-password-active:hidden">
                                <i class="ki-filled ki-eye text-muted-foreground"></i>
                            </span>
                            <span class="hidden kt-toggle-password-active:block">
                                <i class="ki-filled ki-eye-slash text-muted-foreground"></i>
                            </span>
                        </button>
                    </div>
                    @error('password')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>
                <label class="kt-label">
                    <input class="kt-checkbox kt-checkbox-sm" id="remember_me" name="remember" type="checkbox" value="1" />
                    <span class="kt-checkbox-label">
                        {{ __('Remember me') }}
                    </span>
                </label>
                <button class="kt-btn kt-btn-primary flex justify-center grow" type="submit">
                    {{ __('Sign In') }}
                </button>
            </form>
        </div>
    </div>
    <!-- Scripts -->
    <!-- Core JS -->
    <script src="{{ asset('assets/js/core.bundle.js') }}"></script>

    <!-- Vendor JS -->
    <script src="{{ asset('assets/vendors/ktui/ktui.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/apexcharts/apexcharts.min.js') }}"></script>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>

</body>

</html>
