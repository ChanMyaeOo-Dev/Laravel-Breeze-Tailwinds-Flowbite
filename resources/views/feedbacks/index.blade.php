<x-app-layout title="Menu Categories">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
            <div class="flex flex-col">
                <span class="text-2xl text-brand font-semibold whitespace-nowrap dark:text-white">
                    Customer Feedbacks
                </span>
            </div>
        </div>
    </div>

    @if (session('success'))
    <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400"
        role="alert">
        {{ session('success') }}
    </div>
    @endif

    <div class="relative overflow-x-auto">
        <table id="DataTable" class="w-full text-sm text-left rtl:text-right text-body">
            <thead class="text-sm text-body bg-neutral-secondary-medium border-b border-t border-default-medium">
                <tr>
                    <th scope="col" class="px-6 py-3 font-medium">#</th>
                    <th scope="col" class="px-6 py-3 font-medium">Comment</th>
                    <th scope="col" class="px-6 py-3 font-medium">Date</th>
                    <th scope="col" class="px-6 py-3 font-medium text-right">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($feedbacks as $index=>$feedback)
                <tr class="bg-neutral-brand-soft border-b border-default hover:bg-neutral-secondary-medium">
                    <td class="px-6 py-4 font-medium text-heading whitespace-nowrap">
                        {{ $index+1 }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $feedback->comment ?? '-' }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $feedback->created_at->format('d/m/Y h:i A') ?? '-' }}
                    </td>
                    <td class="px-6 py-4 text-right">
                        Detail
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @include('includes.data-table')
</x-app-layout>