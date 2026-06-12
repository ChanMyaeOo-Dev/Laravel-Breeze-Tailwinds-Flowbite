@php
    use App\Helpers\MenuHelper;
    $menuGroups = MenuHelper::getMenuGroups();
    $currentPath = request()->path();
@endphp
<aside class="py-4 ps-4 fixed top-0 left-0 w-70 h-screen z-50 transition-transform -translate-x-full md:translate-x-0"
    aria-label="Sidenav" id="drawer-navigation">
    <div class="h-full border border-gray-200 rounded overflow-y-auto py-5 px-3 bg-white dark:bg-gray-800">

        <div class="flex justify-start items-center mb-6">
            <a href="https://flowbite.com" class="flex items-center justify-start mx-1">
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
                    <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z">
                        </path>
                    </svg>
                </div>
                <input type="text" name="search" id="sidebar-search"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-sm focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                    placeholder="Search" />
            </div>
        </form>
        <ul class="space-y-2">
            @foreach ($menuGroups[0]['items'] as $item)
                <li>
                    @if (isset($item['subItems']))
                        <button type="button"
                            class="flex items-center p-2 w-full text-base font-medium text-gray-900 rounded-sm transition duration-75 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700"
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
                                        class="{{ MenuHelper::isActive($currentPath, $subItem['path']) ? 'active' : '' }} flex items-center p-2 pl-11 w-full text-base font-medium text-gray-900 rounded-sm transition duration-75 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">
                                        {{ $subItem['name'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <a href="{{ $item['path'] }}"
                            class="{{ MenuHelper::isActive($currentPath, $item['path']) ? 'active' : '' }} flex items-center p-2 text-base font-medium text-gray-900 rounded-sm transition duration-75 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-white group">
                            {!! MenuHelper::getIconSvg($item['icon']) !!}
                            <span class="ml-3">{{ $item['name'] }}</span>
                        </a>
                    @endif
                </li>
            @endforeach
        </ul>

    </div>
</aside>
