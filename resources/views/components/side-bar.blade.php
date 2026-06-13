@php
    use App\Helpers\MenuHelper;
    $menuGroups = MenuHelper::getMenuGroups();
    $currentPath = request()->path();
@endphp
<aside class="py-4 ps-4 fixed top-0 left-0 w-64 h-screen z-[60] transition-transform -translate-x-full md:translate-x-0"
    aria-label="Sidenav" id="drawer-navigation">
    <div
        class="relative h-full border border-neutral-300 rounded overflow-y-auto py-5 px-3 bg-white dark:bg-neutral-800 scrollbar-thin scrollbar-thumb-neutral-300 scrollbar-track-neutral-100">

        <div class="flex justify-start items-center mb-6">
            <a href="{{ route('/') }}" class="flex items-center justify-start mx-1">
                <img src="{{ asset('assets/images/smart_serve_logo.png') }}" class="mr-3 h-8" alt="Flowbite Logo" />
                <span class="self-center text-xl font-semibold whitespace-nowrap dark:text-white">
                    SmartServe
                </span>
            </a>
        </div>

        <form action="#" method="GET" class="md:hidden mb-2">
            <label for="sidebar-search" class="sr-only">Search</label>
            <div class="relative">
                <div class="flex absolute inset-y-0 left-0 items-center pl-3 pointer-events-none">
                    <svg class="w-5 h-5 text-neutral-500 dark:text-neutral-400" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z">
                        </path>
                    </svg>
                </div>
                <input type="text" name="search" id="sidebar-search"
                    class="bg-neutral-50 border border-neutral-300 text-neutral-900 text-sm rounded-sm focus:ring-brand-soft0 focus:border-brand-soft0 block w-full pl-10 p-2 dark:bg-neutral-700 dark:border-neutral-600 dark:placeholder-neutral-400 dark:text-white dark:focus:ring-brand-soft0 dark:focus:border-brand-soft0"
                    placeholder="Search" />
            </div>
        </form>
        <ul class="space-y-2">
            @foreach ($menuGroups[0]['items'] as $item)
                <li>
                    @if (isset($item['subItems']))
                        <button type="button"
                            class="flex items-center px-1 py-1.5 w-full text-base font-medium text-neutral-900 rounded-sm transition duration-75 group hover:bg-neutral-100 dark:text-white dark:hover:bg-neutral-700"
                            aria-controls="dropdown-{{ $item['name'] }}"
                            data-collapse-toggle="dropdown-{{ $item['name'] }}">
                            {!! MenuHelper::getIconSvg($item['icon']) !!}
                            <span class="flex-1 ml-3 text-left whitespace-nowrap">{{ $item['name'] }}</span>
                            <svg aria-hidden="true" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </button>
                        @php
                            $isActive = false;
                            foreach ($item['subItems'] as $subItem) {
                                if (MenuHelper::isActive($currentPath, $subItem['path'])) {
                                    $isActive = true;
                                }
                            }
                        @endphp
                        <ul id="dropdown-{{ $item['name'] }}" class="{{ $isActive ? '' : 'hidden' }} py-2 space-y-2">
                            @foreach ($item['subItems'] as $subItem)
                                <li>
                                    <a href="{{ route($subItem['path']) }}"
                                        class="{{ MenuHelper::isActive($currentPath, $subItem['path']) ? 'active' : '' }} flex items-center px-1 py-1.5 pl-11 w-full text-base font-medium text-neutral-900 rounded-sm transition duration-75 group hover:bg-neutral-100 dark:text-white dark:hover:bg-neutral-700">
                                        {{ $subItem['name'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <a href="{{ $item['path'] }}"
                            class="{{ MenuHelper::isActive($currentPath, $item['path']) ? 'active' : '' }} flex items-center px-1 py-1.5 text-base font-medium text-neutral-900 rounded-sm transition duration-75 hover:bg-neutral-100 dark:hover:bg-neutral-700 dark:text-white group">
                            {!! MenuHelper::getIconSvg($item['icon']) !!}
                            <span class="ml-3">{{ $item['name'] }}</span>
                        </a>
                    @endif
                </li>
            @endforeach
        </ul>

        <div class="absolute bottom-0 start-0 w-full p-3 text-center text-white">
            <button type="button"
                class="flex text-sm bg-neutral-100 rounded w-full focus:ring-1 focus:ring-neutral-300 dark:focus:ring-neutral-600"
                id="user-menu-button" aria-expanded="false" data-dropdown-toggle="dropdown_profile">
                <span class="sr-only">Open user menu</span>
                <div class="flex items-center justify-between p-2 w-full">
                    <img class="w-9 h-9 rounded-full"
                        src="https://flowbite.s3.amazonaws.com/blocks/marketing-ui/avatars/michael-gough.png"
                        alt="user photo" />
                    <div class="flex flex-col items-start ms-3 w-full">
                        <span class="block text-sm font-semibold text-neutral-900 dark:text-white">
                            {{ auth()->user()->name }}
                        </span>
                        <span class="block text-xs text-neutral-900 truncate dark:text-white">
                            {{ auth()->user()->email }}
                        </span>
                    </div>
                    <svg class="w-7 h-7 text-dark" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                        height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m10 16 4-4-4-4" />
                    </svg>
                </div>
            </button>
            <!-- Dropdown menu -->
            <div class="hidden border border-dark z-50 my-4 w-56 text-base list-none bg-white rounded divide-y divide-neutral-100 shadow dark:bg-neutral-700 dark:divide-neutral-600 rounded-xl"
                id="dropdown_profile">
                <div class="py-3 px-4">
                    <span class="block text-sm font-semibold text-neutral-900 dark:text-white">
                        {{ auth()->user()->name }}
                    </span>
                    <span class="block text-sm text-neutral-900 truncate dark:text-white">
                        {{ auth()->user()->email }}
                    </span>
                </div>
                <ul class="py-1 text-neutral-700 dark:text-neutral-300" aria-labelledby="dropdown">
                    <li>
                        <a href="#"
                            class="block py-2 px-4 text-sm hover:bg-neutral-100 dark:hover:bg-neutral-600 dark:text-neutral-400 dark:hover:text-white">My
                            profile</a>
                    </li>
                    <li>
                        <a href="#"
                            class="block py-2 px-4 text-sm hover:bg-neutral-100 dark:hover:bg-neutral-600 dark:text-neutral-400 dark:hover:text-white">Account
                            settings</a>
                    </li>
                </ul>
                <ul class="py-1 text-neutral-700 dark:text-neutral-300" aria-labelledby="dropdown">
                    <li>
                        <a href="#"
                            class="block py-2 px-4 text-sm hover:bg-neutral-100 dark:hover:bg-neutral-600 dark:hover:text-white">Sign
                            out</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</aside>
