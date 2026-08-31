<x-app-layout title="Customer Feedbacks">
    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
                Alpine.store('feedback', {
                    selected: null
                });
            });
    </script>
    @endpush

    <div class="flex items-center justify-between mb-4">
        <div class="flex flex-col">
            <span class="text-2xl text-brand font-semibold whitespace-nowrap dark:text-white">
                Customer Feedbacks
            </span>
            <span class="text-sm text-body mt-1">
                AI-powered sentiment analysis insights
            </span>
        </div>
    </div>

    @if (session('success'))
    <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400"
        role="alert">
        {{ session('success') }}
    </div>
    @endif

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 border border-default rounded-lg p-4">
            <div class="text-sm text-body font-medium">Total Feedbacks</div>
            <div class="text-2xl font-bold text-brand dark:text-white mt-1">{{ $stats['total'] }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 border border-default rounded-lg p-4">
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-green-500"></span>
                <span class="text-sm text-body font-medium">Positive</span>
            </div>
            <div class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">{{ $stats['positive'] }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 border border-default rounded-lg p-4">
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-yellow-400"></span>
                <span class="text-sm text-body font-medium">Neutral</span>
            </div>
            <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400 mt-1">{{ $stats['neutral'] }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 border border-default rounded-lg p-4">
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-red-500"></span>
                <span class="text-sm text-body font-medium">Negative</span>
            </div>
            <div class="text-2xl font-bold text-red-600 dark:text-red-400 mt-1">{{ $stats['negative'] }}</div>
        </div>
    </div>

    {{-- AI Summary Dashboard --}}
    <div class="bg-white dark:bg-gray-800 border border-default rounded-lg p-6 mb-6">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-lg bg-brand/10 flex items-center justify-center">
                <svg class="w-5 h-5 text-brand dark:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-brand dark:text-white">AI Feedback Summary</h3>
                <p class="text-xs text-body">Automated insights from all customer feedback</p>
            </div>
        </div>

        <div class="space-y-3 text-body leading-relaxed">
            @foreach (explode("\n\n", $summary) as $paragraph)
            <p>{{ $paragraph }}</p>
            @endforeach
        </div>

        @if ($stats['analyzed'] > 0)
        <div class="mt-5 pt-4 border-t border-default">
            <div class="flex items-center justify-between text-xs text-body mb-2">
                <span>Sentiment Distribution ({{ $stats['analyzed'] }} analyzed)</span>
                <div class="flex items-center gap-4">
                    <span class="flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-green-500"></span>
                        {{ $stats['positive'] }} positive
                    </span>
                    <span class="flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-yellow-400"></span>
                        {{ $stats['neutral'] }} neutral
                    </span>
                    <span class="flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-red-500"></span>
                        {{ $stats['negative'] }} negative
                    </span>
                </div>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700 flex overflow-hidden">
                @if ($stats['positive'] > 0)
                <div class="bg-green-500 h-2" style="width: {{ ($stats['positive'] / $stats['analyzed']) * 100 }}%">
                </div>
                @endif
                @if ($stats['neutral'] > 0)
                <div class="bg-yellow-400 h-2" style="width: {{ ($stats['neutral'] / $stats['analyzed']) * 100 }}%">
                </div>
                @endif
                @if ($stats['negative'] > 0)
                <div class="bg-red-500 h-2" style="width: {{ ($stats['negative'] / $stats['analyzed']) * 100 }}%"></div>
                @endif
            </div>
        </div>
        @endif
    </div>

    {{-- Feedback Table --}}
    <div class="relative overflow-x-auto">
        <table id="DataTable" class="w-full text-sm text-left rtl:text-right text-body">
            <thead class="text-sm text-body bg-neutral-secondary-medium border-b border-t border-default-medium">
                <tr>
                    <th scope="col" class="px-6 py-3 font-medium">#</th>
                    <th scope="col" class="px-6 py-3 font-medium">Rating</th>
                    <th scope="col" class="px-6 py-3 font-medium">Comment</th>
                    <th scope="col" class="px-6 py-3 font-medium">Sentiment</th>
                    <th scope="col" class="px-6 py-3 font-medium">Confidence</th>
                    <th scope="col" class="px-6 py-3 font-medium">Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($feedbacks as $index => $feedback)
                <tr class="bg-neutral-brand-soft border-b border-default hover:bg-neutral-secondary-medium">
                    <td class="px-6 py-4 font-medium text-heading whitespace-nowrap">
                        {{ $index + 1 }}
                    </td>

                    {{-- Rating --}}
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center gap-1">
                            @for ($i = 1; $i <= 5; $i++) @if ($i <=$feedback->rating)
                                <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                                @else
                                <svg class="w-4 h-4 text-gray-300 dark:text-gray-600" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                                @endif
                                @endfor
                        </div>
                    </td>

                    {{-- Comment --}}
                    <td class="px-6 py-4 max-w-xs">
                        <span class="line-clamp-2" title="{{ $feedback->comment }}">
                            {{ $feedback->comment ?? '-' }}
                        </span>
                    </td>

                    {{-- Sentiment Badge --}}
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if ($feedback->analysis)
                        @php
                        $sentimentColors = [
                        'positive' =>
                        'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                        'neutral' =>
                        'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                        'negative' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                        ];
                        @endphp
                        <span
                            class="{{ $sentimentColors[$feedback->analysis->sentiment] ?? '' }} text-xs font-medium px-2.5 py-0.5 rounded">
                            {{ ucfirst($feedback->analysis->sentiment) }}
                        </span>
                        @else
                        <span
                            class="bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400 text-xs font-medium px-2.5 py-0.5 rounded">
                            Not analyzed
                        </span>
                        @endif
                    </td>

                    <td class="px-6 py-4 whitespace-nowrap">
                        {{ $feedback->analysis?->confidence ? number_format($feedback->analysis->confidence * 100, 2) .
                        '%' : '-' }}
                    </td>

                    {{-- Date --}}
                    <td class="px-6 py-4 whitespace-nowrap">
                        {{ $feedback->created_at->format('d/m/Y h:i A') ?? '-' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @include('includes.data-table')
</x-app-layout>