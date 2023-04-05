<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased">
    <div class="relative flex justify-center min-h-screen py-4 bg-gray-100 items-top sm:items-center sm:pt-0">
        @if (Route::has('login'))
        <div class="fixed top-0 right-0 hidden px-6 py-4 sm:block">
            @auth
            <a href="{{ url('/dashboard') }}" class="text-sm text-gray-700 underline dark:text-gray-500">Dashboard</a>
            @else
            <a href="{{ route('login') }}" class="text-sm text-gray-700 underline dark:text-gray-500">Log in</a>

            @if (Route::has('register'))
            <a href="{{ route('register') }}"
                class="ml-4 text-sm text-gray-700 underline dark:text-gray-500">Register</a>
            @endif
            @endauth
        </div>
        @endif

        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <section class="h-full gradient-form md:h-screen">
                <div class="container h-full px-6 py-12">
                    <div class="flex flex-wrap items-center justify-center h-full text-gray-800 g-6">
                        <div class="xl:w-10/12">
                            <div class="block bg-white rounded-lg shadow-lg">
                                <div class="lg:flex lg:flex-wrap g-0">
                                    <div class="px-4 lg:w-6/12 md:px-0">
                                        <div class="md:p-12 md:mx-6">
                                            <div class="text-center">
                                                <img class="mx-auto w-52"
                                                    src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-login-form/lotus.webp"
                                                    alt="logo" />
                                                <h4 class="pb-1 mt-1 mb-12 text-xl font-semibold">BPA - Tor J. Rivera
                                                </h4>
                                            </div>
                                            <form method="POST" action="{{ route('login') }}">
                                                @csrf
                                                <p class="mb-4 text-center">Logg inn på din konto</p>
                                                <div class="mb-4">
                                                    <x-text-input type="email" id="email"
                                                        class="form-control block w-full px-3 py-1.5 text-base font-normal text-gray-700 bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none"
                                                        id="exampleFormControlInput1" placeholder="E-post" name="email"
                                                        :value="old('email')" required autofocus />

                                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                                </div>
                                                <div class="mb-4">
                                                    <input type="password" id="password"
                                                        class="form-control block w-full px-3 py-1.5 text-base font-normal text-gray-700 bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none"
                                                        id="exampleFormControlInput1" placeholder="Passord"
                                                        name="password" required autocomplete="current-password" />
                                                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                                </div>

                                                <div class="mb-4">
                                                    <label for="remember_me" class="inline-flex items-center">
                                                        <input id="remember_me" type="checkbox"
                                                            class="text-indigo-600 border-gray-300 rounded shadow-sm focus:ring-indigo-500"
                                                            name="remember">
                                                        <span class="ml-2 text-sm text-gray-600">{{ __('Husk meg')
                                                            }}</span>
                                                    </label>
                                                </div>
                                                <div class="pt-1 pb-1 mb-12 text-center">
                                                    <x-primary-button class="mb-1" data-mdb-ripple="true"
                                                        data-mdb-ripple-color="light"
                                                        style="background: linear-gradient(to right, #ee7724, #d8363a, #dd3675, #b44593);">
                                                        Log in
                                                    </x-primary-button>

                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="flex items-center rounded-b-lg lg:w-6/12 lg:rounded-r-lg lg:rounded-bl-none"
                                        style="background: linear-gradient(to right, #ee7724, #d8363a, #dd3675, #b44593);">
                                        <div class="px-4 py-6 text-white md:p-12 md:mx-6">
                                            <h4 class="mb-6 text-xl font-semibold">Privat bpa side for assistenter</h4>
                                            <p class="text-sm">
                                                Her får du oversikt over tider du har jobbet, kontaktinformasjon og
                                                annen nyttig informasjon i forbindelse med arbeidsforholdet
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </div>
    </div>
</body>

</html>