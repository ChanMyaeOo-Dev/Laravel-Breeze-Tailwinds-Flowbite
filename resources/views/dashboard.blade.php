<x-app-layout>
    {{-- <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot> --}}

    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
            {{-- <button type="button" class="btn-sm">
                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                    fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="m17 16-4-4 4-4m-6 8-4-4 4-4" />
                </svg>
            </button> --}}
            <div class="flex flex-col">
                <span class="text-2xl font-semibold whitespace-nowrap dark:text-white">
                    Dashboard
                </span>
            </div>

        </div>
        <button type="button" class="btn-primary">
            Create New
        </button>
    </div>

    <div class="relative overflow-x-auto">
        <div class="ps-0.5 py-4 flex items-center gap-3">

            <div>
                <label for="input-group-1" class="sr-only">Search</label>
                <div class="relative">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                        <svg class="w-4 h-4 text-body" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                                d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
                        </svg>
                    </div>
                    <input type="text" id="input-group-1"
                        class="block w-full max-w-96 ps-9 pe-3 h-10 bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded focus:ring-brand focus:border-brand px-3 py-2.5 placeholder:text-body"
                        placeholder="Search">
                </div>
            </div>

            <button id="dropdownDefaultButton" data-dropdown-toggle="filter_dropdown" class="drop-down" type="button">
                Filter By
                <svg class="w-4 h-4 ms-1.5 -me-0.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                    height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="m19 9-7 7-7-7" />
                </svg>
            </button>

            <button id="dropdownDefaultButton" data-dropdown-toggle="sort_dropdown" class="drop-down" type="button">
                Sort By
                <svg class="w-4 h-4 ms-1.5 -me-0.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                    height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="m19 9-7 7-7-7" />
                </svg>
            </button>

            <div id="filter_dropdown"
                class="z-10 hidden bg-neutral-primary-medium border border-default-medium rounded shadow-lg w-44">
                <ul class="p-2 text-sm text-body font-medium" aria-labelledby="dropdownDefaultButton">
                    <li>
                        <a href="#"
                            class="inline-flex items-center w-full p-2 hover:bg-neutral-tertiary-medium hover:text-heading rounded">Dashboard</a>
                    </li>
                    <li>
                        <a href="#"
                            class="inline-flex items-center w-full p-2 hover:bg-neutral-tertiary-medium hover:text-heading rounded">Settings</a>
                    </li>
                    <li>
                        <a href="#"
                            class="inline-flex items-center w-full p-2 hover:bg-neutral-tertiary-medium hover:text-heading rounded">Earnings</a>
                    </li>
                    <li>
                        <a href="#"
                            class="inline-flex items-center w-full p-2 hover:bg-neutral-tertiary-medium hover:text-heading rounded">Sign
                            out</a>
                    </li>
                </ul>
            </div>

            <div id="sort_dropdown"
                class="z-10 hidden bg-neutral-primary-medium border border-default-medium rounded shadow-lg w-44">
                <ul class="p-2 text-sm text-body font-medium" aria-labelledby="dropdownDefaultButton">
                    <li>
                        <a href="#"
                            class="inline-flex items-center w-full p-2 hover:bg-neutral-tertiary-medium hover:text-heading rounded">Dashboard</a>
                    </li>
                    <li>
                        <a href="#"
                            class="inline-flex items-center w-full p-2 hover:bg-neutral-tertiary-medium hover:text-heading rounded">Settings</a>
                    </li>
                    <li>
                        <a href="#"
                            class="inline-flex items-center w-full p-2 hover:bg-neutral-tertiary-medium hover:text-heading rounded">Earnings</a>
                    </li>
                    <li>
                        <a href="#"
                            class="inline-flex items-center w-full p-2 hover:bg-neutral-tertiary-medium hover:text-heading rounded">Sign
                            out</a>
                    </li>
                </ul>
            </div>

        </div>
        <table class="w-full text-sm text-left rtl:text-right text-body">
            <thead class="text-sm text-body bg-neutral-secondary-medium border-b border-t border-default-medium">
                <tr>
                    <th scope="col" class="p-4">
                        <div class="flex items-center">
                            <input id="table-checkbox-12" type="checkbox" value=""
                                class="w-4 h-4 border border-default-medium rounded-xs bg-neutral-secondary-medium focus:ring-2 focus:ring-brand-soft">
                            <label for="table-checkbox-12" class="sr-only">Table checkbox</label>
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3 font-medium">
                        Product name
                    </th>
                    <th scope="col" class="px-6 py-3 font-medium">
                        Color
                    </th>
                    <th scope="col" class="px-6 py-3 font-medium">
                        Category
                    </th>
                    <th scope="col" class="px-6 py-3 font-medium">
                        Price
                    </th>
                    <th scope="col" class="px-6 py-3 font-medium">
                        Action
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr class="bg-neutral-primary-soft border-b border-default hover:bg-neutral-secondary-medium">
                    <td class="w-4 p-4">
                        <div class="flex items-center">
                            <input id="table-checkbox-13" type="checkbox" value=""
                                class="w-4 h-4 border border-default-medium rounded-xs bg-neutral-secondary-medium focus:ring-2 focus:ring-brand-soft">
                            <label for="table-checkbox-13" class="sr-only">Table checkbox</label>
                        </div>
                    </td>
                    <th scope="row" class="px-6 py-4 font-medium text-heading whitespace-nowrap">
                        Apple MacBook Pro 17"
                    </th>
                    <td class="px-6 py-4">
                        Silver
                    </td>
                    <td class="px-6 py-4">
                        Laptop
                    </td>
                    <td class="px-6 py-4">
                        $2999
                    </td>
                    <td class="px-6 py-4">
                        <a href="#" class="font-medium text-fg-brand hover:underline">Edit</a>
                    </td>
                </tr>
                <tr class="bg-neutral-primary-soft border-b border-default hover:bg-neutral-secondary-medium">
                    <td class="w-4 p-4">
                        <div class="flex items-center">
                            <input id="table-checkbox-14" type="checkbox" value=""
                                class="w-4 h-4 border border-default-medium rounded-xs bg-neutral-secondary-medium focus:ring-2 focus:ring-brand-soft">
                            <label for="table-checkbox-14" class="sr-only">Table checkbox</label>
                        </div>
                    </td>
                    <th scope="row" class="px-6 py-4 font-medium text-heading whitespace-nowrap">
                        Microsoft Surface Pro
                    </th>
                    <td class="px-6 py-4">
                        White
                    </td>
                    <td class="px-6 py-4">
                        Laptop PC
                    </td>
                    <td class="px-6 py-4">
                        $1999
                    </td>
                    <td class="px-6 py-4">
                        <a href="#" class="font-medium text-fg-brand hover:underline">Edit</a>
                    </td>
                </tr>
                <tr class="bg-neutral-primary-soft border-b border-default hover:bg-neutral-secondary-medium">
                    <td class="w-4 p-4">
                        <div class="flex items-center">
                            <input id="table-checkbox-15" type="checkbox" value=""
                                class="w-4 h-4 border border-default-medium rounded-xs bg-neutral-secondary-medium focus:ring-2 focus:ring-brand-soft">
                            <label for="table-checkbox-15" class="sr-only">Table checkbox</label>
                        </div>
                    </td>
                    <th scope="row" class="px-6 py-4 font-medium text-heading whitespace-nowrap">
                        Magic Mouse 2
                    </th>
                    <td class="px-6 py-4">
                        Black
                    </td>
                    <td class="px-6 py-4">
                        Accessories
                    </td>
                    <td class="px-6 py-4">
                        $99
                    </td>
                    <td class="px-6 py-4">
                        <a href="#" class="font-medium text-fg-brand hover:underline">Edit</a>
                    </td>
                </tr>
                <tr class="bg-neutral-primary-soft border-b border-default hover:bg-neutral-secondary-medium">
                    <td class="w-4 p-4">
                        <div class="flex items-center">
                            <input id="table-checkbox-16" type="checkbox" value=""
                                class="w-4 h-4 border border-default-medium rounded-xs bg-neutral-secondary-medium focus:ring-2 focus:ring-brand-soft">
                            <label for="table-checkbox-16" class="sr-only">Table checkbox</label>
                        </div>
                    </td>
                    <th scope="row" class="px-6 py-4 font-medium text-heading whitespace-nowrap">
                        Apple Watch
                    </th>
                    <td class="px-6 py-4">
                        Silver
                    </td>
                    <td class="px-6 py-4">
                        Accessories
                    </td>
                    <td class="px-6 py-4">
                        $179
                    </td>
                    <td class="px-6 py-4">
                        <a href="#" class="font-medium text-fg-brand hover:underline">Edit</a>
                    </td>
                </tr>
                <tr class="bg-neutral-primary-soft border-b border-default hover:bg-neutral-secondary-medium">
                    <td class="w-4 p-4">
                        <div class="flex items-center">
                            <input id="table-checkbox-17" type="checkbox" value=""
                                class="w-4 h-4 border border-default-medium rounded-xs bg-neutral-secondary-medium focus:ring-2 focus:ring-brand-soft">
                            <label for="table-checkbox-17" class="sr-only">Table checkbox</label>
                        </div>
                    </td>
                    <th scope="row" class="px-6 py-4 font-medium text-heading whitespace-nowrap">
                        iPad
                    </th>
                    <td class="px-6 py-4">
                        Gold
                    </td>
                    <td class="px-6 py-4">
                        Tablet
                    </td>
                    <td class="px-6 py-4">
                        $699
                    </td>
                    <td class="px-6 py-4">
                        <a href="#" class="font-medium text-fg-brand hover:underline">Edit</a>
                    </td>
                </tr>
                <tr class="bg-neutral-primary-soft hover:bg-neutral-secondary-medium">
                    <td class="w-4 p-4">
                        <div class="flex items-center">
                            <input id="table-checkbox-18" type="checkbox" value=""
                                class="w-4 h-4 border border-default-medium rounded-xs bg-neutral-secondary-medium focus:ring-2 focus:ring-brand-soft">
                            <label for="table-checkbox-18" class="sr-only">Table checkbox</label>
                        </div>
                    </td>
                    <th scope="row" class="px-6 py-4 font-medium text-heading whitespace-nowrap">
                        Apple iMac 27"
                    </th>
                    <td class="px-6 py-4">
                        Silver
                    </td>
                    <td class="px-6 py-4">
                        PC Desktop
                    </td>
                    <td class="px-6 py-4">
                        $3999
                    </td>
                    <td class="px-6 py-4">
                        <a href="#" class="font-medium text-fg-brand hover:underline">Edit</a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="w-full flex items-end justify-end mt-4">
        <nav aria-label="Page navigation example">
            <ul class="flex -space-x-px text-sm">
                <li>
                    <a href="#"
                        class="flex items-center justify-center text-body bg-neutral-secondary-medium box-border border border-default-medium hover:bg-neutral-tertiary-medium hover:text-heading font-medium rounded-s-base text-sm px-3 h-9 focus:outline-none">Previous</a>
                </li>
                <li>
                    <a href="#"
                        class="flex items-center justify-center text-body bg-neutral-secondary-medium box-border border border-default-medium hover:bg-neutral-tertiary-medium hover:text-heading font-medium text-sm w-9 h-9 focus:outline-none">1</a>
                </li>
                <li>
                    <a href="#"
                        class="flex items-center justify-center text-body bg-neutral-secondary-medium box-border border border-default-medium hover:bg-neutral-tertiary-medium hover:text-heading font-medium text-sm w-9 h-9 focus:outline-none">2</a>
                </li>
                <li>
                    <a href="#" aria-current="page"
                        class="flex items-center justify-center text-fg-brand bg-neutral-tertiary-medium box-border border border-default-medium hover:text-fg-brand font-medium text-sm w-9 h-9 focus:outline-none">3</a>
                </li>
                <li>
                    <a href="#"
                        class="flex items-center justify-center text-body bg-neutral-secondary-medium box-border border border-default-medium hover:bg-neutral-tertiary-medium hover:text-heading font-medium text-sm w-9 h-9 focus:outline-none">4</a>
                </li>
                <li>
                    <a href="#"
                        class="flex items-center justify-center text-body bg-neutral-secondary-medium box-border border border-default-medium hover:bg-neutral-tertiary-medium hover:text-heading font-medium text-sm w-9 h-9 focus:outline-none">5</a>
                </li>
                <li>
                    <a href="#"
                        class="flex items-center justify-center text-body bg-neutral-secondary-medium box-border border border-default-medium hover:bg-neutral-tertiary-medium hover:text-heading font-medium rounded-e-base text-sm px-3 h-9 focus:outline-none">Next</a>
                </li>
            </ul>
        </nav>
    </div>


</x-app-layout>
