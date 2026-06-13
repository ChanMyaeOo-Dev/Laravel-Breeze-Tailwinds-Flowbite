<nav class="py-2 mb-4">
    <div class="flex flex-wrap justify-between items-center">
        <div class="flex justify-between items-center flex-1">
            <button data-drawer-target="drawer-navigation" data-drawer-toggle="drawer-navigation"
                aria-controls="drawer-navigation"
                class="p-2 mr-2 text-neutral-600 rounded-lg cursor-pointer md:hidden hover:text-neutral-900 hover:bg-neutral-100 focus:bg-neutral-100 dark:focus:bg-neutral-700 focus:ring-2 focus:ring-neutral-100 dark:focus:ring-neutral-700 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-white">
                <svg aria-hidden="true" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"
                    xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd"
                        d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h6a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                        clip-rule="evenodd"></path>
                </svg>
                <svg aria-hidden="true" class="hidden w-6 h-6" fill="currentColor" viewBox="0 0 20 20"
                    xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd"
                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                        clip-rule="evenodd"></path>
                </svg>
                <span class="sr-only">Toggle sidebar</span>
            </button>
            <form action="#" method="GET" class="hidden md:block">
                <label for="topbar-search" class="sr-only">Search</label>
                <div class="relative md:w-64 md:w-96">
                    <div class="flex absolute inset-y-0 left-0 items-center pl-3 pointer-events-none">
                        <svg class="w-5 h-5 text-neutral-500 dark:text-neutral-400" fill="currentColor"
                            viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z">
                            </path>
                        </svg>
                    </div>
                    <input type="text" name="email" id="topbar-search"
                        class="bg-neutral-50 border border-neutral-300 text-neutral-900 text-sm rounded focus:ring-brand-soft0 focus:border-brand-soft0 block w-full pl-10 p-2.5 dark:bg-neutral-700 dark:border-neutral-600 dark:placeholder-neutral-400 dark:text-white dark:focus:ring-brand-soft0 dark:focus:border-brand-soft0"
                        placeholder="Search" />
                </div>
            </form>
        </div>
        <div class="flex items-center lg:order-2">
            <button type="button" data-drawer-toggle="drawer-navigation" aria-controls="drawer-navigation"
                class="p-2 mr-1 text-neutral-500 rounded-lg md:hidden hover:text-neutral-900 hover:bg-neutral-100 dark:text-neutral-400 dark:hover:text-white dark:hover:bg-neutral-700 focus:ring-4 focus:ring-neutral-300 dark:focus:ring-neutral-600">
                <span class="sr-only">Toggle search</span>
                <svg aria-hidden="true" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"
                    xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path clip-rule="evenodd" fill-rule="evenodd"
                        d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z">
                    </path>
                </svg>
            </button>
            <button id="theme-toggle" type="button"
                class="hidden text-neutral-500 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-700 focus:outline-none focus:ring-4 focus:ring-neutral-200 dark:focus:ring-neutral-700 rounded-lg text-sm p-2.5">
                <svg id="theme-toggle-dark-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                    xmlns="http://www.w3.org/2000/svg">
                    <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                </svg>
                <svg id="theme-toggle-light-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"
                        fill-rule="evenodd" clip-rule="evenodd"></path>
                </svg>
            </button>
            <!-- Notifications -->
            <button type="button" data-dropdown-toggle="notification-dropdown"
                class="p-2 mr-1 text-neutral-500 rounded-lg hover:text-neutral-900 hover:bg-neutral-100 dark:text-neutral-400 dark:hover:text-white dark:hover:bg-neutral-700 focus:ring-4 focus:ring-neutral-300 dark:focus:ring-neutral-600">
                <span class="sr-only">View notifications</span>
                <!-- Bell icon -->
                <svg aria-hidden="true" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z">
                    </path>
                </svg>
            </button>
            <!-- Dropdown menu -->
            <div class="hidden overflow-hidden z-50 my-4 max-w-sm text-base list-none bg-white rounded divide-y divide-neutral-100 shadow-lg dark:divide-neutral-600 dark:bg-neutral-700 rounded-xl"
                id="notification-dropdown">
                <div
                    class="block py-2 px-4 text-base font-medium text-center text-neutral-700 bg-neutral-50 dark:bg-neutral-600 dark:text-neutral-300">
                    Notifications
                </div>
                <div>
                    @for ($i = 0; $i < 4; $i++)
                        <a href="#"
                            class="flex py-3 px-4 border-b hover:bg-neutral-100 dark:hover:bg-neutral-600 dark:border-neutral-600">
                            <div class="flex-shrink-0">
                                <img class="w-11 h-11 rounded-full"
                                    src="https://flowbite.s3.amazonaws.com/blocks/marketing-ui/avatars/bonnie-green.png"
                                    alt="Bonnie Green avatar" />
                                <div
                                    class="flex absolute justify-center items-center ml-6 -mt-5 w-5 h-5 rounded-full border border-white bg-brand-700 dark:border-neutral-700">
                                    <svg aria-hidden="true" class="w-3 h-3 text-white" fill="currentColor"
                                        viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M8.707 7.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l2-2a1 1 0 00-1.414-1.414L11 7.586V3a1 1 0 10-2 0v4.586l-.293-.293z">
                                        </path>
                                        <path
                                            d="M3 5a2 2 0 012-2h1a1 1 0 010 2H5v7h2l1 2h4l1-2h2V5h-1a1 1 0 110-2h1a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V5z">
                                        </path>
                                    </svg>
                                </div>
                            </div>
                            <div class="pl-3 w-full">
                                <div class="text-neutral-500 font-normal text-sm mb-1.5 dark:text-neutral-400">
                                    New message from
                                    <span class="font-semibold text-neutral-900 dark:text-white">Bonnie Green</span>:
                                    "Hey,
                                    what's up? All set for the presentation?"
                                </div>
                                <div class="text-xs font-medium text-brand-600 dark:text-brand-soft0">
                                    a few moments ago
                                </div>
                            </div>
                        </a>
                    @endfor
                </div>
                <a href="#"
                    class="block py-2 text-md font-medium text-center text-neutral-900 bg-neutral-50 hover:bg-neutral-100 dark:bg-neutral-600 dark:text-white dark:hover:underline">
                    <div class="inline-flex items-center">
                        <svg aria-hidden="true" class="mr-2 w-4 h-4 text-neutral-500 dark:text-neutral-400"
                            fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                            <path fill-rule="evenodd"
                                d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                clip-rule="evenodd"></path>
                        </svg>
                        View all
                    </div>
                </a>
            </div>
            <button type="button"
                class="flex mx-3 text-sm bg-neutral-800 rounded-full md:mr-0 focus:ring-4 focus:ring-neutral-300 dark:focus:ring-neutral-600"
                id="user-menu-button" aria-expanded="false" data-dropdown-toggle="dropdown">
                <span class="sr-only">Open user menu</span>
                <img class="w-8 h-8 rounded-full"
                    src="https://flowbite.s3.amazonaws.com/blocks/marketing-ui/avatars/michael-gough.png"
                    alt="user photo" />
            </button>
            <!-- Dropdown menu -->
            <div class="hidden border border-brand z-50 my-4 w-56 text-base list-none bg-white rounded divide-y divide-neutral-100 shadow dark:bg-neutral-700 dark:divide-neutral-600 rounded-xl"
                id="dropdown">
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
</nav>
