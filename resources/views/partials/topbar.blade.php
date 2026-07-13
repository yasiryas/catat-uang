<header
    class="
        bg-white

        border-b

        h-16

        flex

        items-center

        justify-between

        px-6
    ">

    <div class="flex items-center gap-4">

        <button @click="sidebar=true" class="md:hidden">

            ☰

        </button>

        <h2 class="text-2xl font-bold">

            @yield('title')

        </h2>

    </div>

    <div class="flex items-center gap-6">

        <span>

            {{ auth()->user()->name }}

        </span>

        <form method="POST" action="{{ route('logout') }}">

            @csrf

            <button class="text-red-600">

                Logout

            </button>

        </form>

    </div>

</header>
