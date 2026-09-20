<!DOCTYPE html>
<html lang="id" class="h-full">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        @yield('title', 'Dashboard') - Admin SmartPath
    </title>


    <link
        rel="preconnect"
        href="https://fonts.googleapis.com"
    >

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap"
        rel="stylesheet"
    >

    <link
    rel="stylesheet"
    href="{{ asset('css/smartpath-admin-pages.css') }}"
    >


    {{-- Tailwind CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>

    <script>

        tailwind.config = {

            darkMode: 'class',

            theme: {

                extend: {

                    fontFamily: {

                        sans: [
                            'Inter',
                            'sans-serif'
                        ],

                    },

                    colors: {

                        emerald: {

                            50: '#ecfdf5',

                            100: '#d1fae5',

                            500: '#10b981',

                            600: '#059669',

                            700: '#047857',

                            800: '#065f46',

                            900: '#064e3b',

                            950: '#022c22',

                        }

                    }

                }

            }

        };

    </script>


    {{-- Stack CSS dari halaman --}}
    @stack('styles')


    <style>

        body {

            font-family:
                'Inter',
                sans-serif;

        }

    </style>

</head>


<body
    class="h-full bg-slate-50 text-slate-900 antialiased"
>

    <div class="flex h-full">


        {{-- SIDEBAR SESUAI ROLE --}}

        @if(auth()->user()->isAdmin())

            @include(
                'partials.sidebar-admin'
            )

        @elseif(auth()->user()->isDinas())

            @include(
                'partials.sidebar-dinas'
            )

        @elseif(auth()->user()->isWarga())

            @include(
                'partials.sidebar-warga'
            )

        @endif


        <div
            class="flex-1 flex flex-col min-w-0"
        >

            {{-- HEADER --}}

           @if(auth()->user()->isAdmin())

    @include('partials.header-admin')

@elseif(auth()->user()->isDinas())

    @include('partials.header-dinas')

@endif
            {{-- MAIN CONTENT --}}

            <main
                id="main-content"
                class="flex-1 p-6 lg:p-8 overflow-y-auto"
                role="main"
            >

                {{-- SUCCESS --}}

                @if(session('sukses'))

                    <div
                        class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-6 py-3 rounded-xl shadow-sm flex items-center gap-2"
                        role="alert"
                        aria-live="polite"
                    >

                        <svg
                            class="w-5 h-5 text-emerald-600 shrink-0"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M5 13l4 4L19 7"
                            />

                        </svg>

                        {{ session('sukses') }}

                    </div>

                @endif


                {{-- ERROR --}}

                @if(session('galat'))

                    <div
                        class="mb-6 bg-red-50 border border-red-200 text-red-800 px-6 py-3 rounded-xl shadow-sm flex items-center gap-2"
                        role="alert"
                        aria-live="polite"
                    >

                        <svg
                            class="w-5 h-5 text-red-600 shrink-0"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            />

                        </svg>

                        {{ session('galat') }}

                    </div>

                @endif


                @yield('content')

            </main>

        </div>

    </div>


    @stack('scripts')

    <script
    src="{{ asset('js/smartpath-admin-pages.js') }}"
    defer
    ></script>

</body>

</html>