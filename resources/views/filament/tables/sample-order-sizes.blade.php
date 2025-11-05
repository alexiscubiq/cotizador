@php
    $record = $getRecord();
    $sizes = $getState();
@endphp

<div>
    @if(!is_array($sizes) || empty($sizes))
        <div class="text-sm text-gray-500 dark:text-gray-400">Sin datos de talles</div>
    @else
        <div class="fi-ta-ctn divide-y divide-gray-200 overflow-x-auto rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:divide-white/10 dark:bg-gray-900 dark:ring-white/10">
            <table class="fi-ta-table w-full table-auto divide-y divide-gray-200 text-start dark:divide-white/5">
                <thead class="divide-y divide-gray-200 dark:divide-white/5">
                    <tr class="bg-gray-50 dark:bg-white/5">
                        <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6 font-semibold text-sm text-gray-950 dark:text-white">Talle</th>
                        <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6 font-semibold text-sm text-gray-950 dark:text-white text-center">Cliente</th>
                        <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6 font-semibold text-sm text-gray-950 dark:text-white text-center">WTS</th>
                        <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6 font-semibold text-sm text-gray-950 dark:text-white text-center">Recibidas</th>
                        <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6 font-semibold text-sm text-gray-950 dark:text-white text-center">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 whitespace-nowrap dark:divide-white/5">
                    @foreach($sizes as $sizeName => $values)
                        @php
                            $client = $values['client'] ?? 0;
                            $wts = $values['wts'] ?? 0;
                            $received = $values['received'] ?? 0;
                            $total = $client + $wts;
                            $pending = max($total - $received, 0);
                            $isComplete = $pending === 0;

                            $statusColor = $isComplete ? 'success' : 'warning';
                            $statusLabel = $isComplete ? '✓ Completo' : "Faltan {$pending}";
                            $bgClass = $isComplete ? 'bg-success-50' : 'bg-warning-50';
                            $textClass = $isComplete ? 'text-success-600' : 'text-warning-600';
                            $ringClass = $isComplete ? 'ring-success-600/10' : 'ring-warning-600/10';
                            $darkBgClass = $isComplete ? 'dark:bg-success-400/10' : 'dark:bg-warning-400/10';
                            $darkTextClass = $isComplete ? 'dark:text-success-400' : 'dark:text-warning-400';
                            $darkRingClass = $isComplete ? 'dark:ring-success-400/30' : 'dark:ring-warning-400/30';
                        @endphp

                        <tr class="fi-ta-row hover:bg-gray-50 dark:hover:bg-white/5">
                            <td class="fi-ta-cell px-3 py-4 text-sm">
                                <span class="font-semibold text-gray-950 dark:text-white">{{ $sizeName }}</span>
                            </td>
                            <td class="fi-ta-cell px-3 py-4 text-sm text-center text-gray-950 dark:text-white">
                                {{ $client }}
                            </td>
                            <td class="fi-ta-cell px-3 py-4 text-sm text-center text-gray-950 dark:text-white">
                                {{ $wts }}
                            </td>
                            <td class="fi-ta-cell px-3 py-4 text-sm text-center text-gray-950 dark:text-white font-semibold">
                                {{ $received }}
                            </td>
                            <td class="fi-ta-cell px-3 py-4 text-center">
                                <span class="fi-badge inline-flex items-center gap-x-1 rounded-md text-xs font-medium ring-1 ring-inset px-2 py-1 {{ $bgClass }} {{ $textClass }} {{ $ringClass }} {{ $darkBgClass }} {{ $darkTextClass }} {{ $darkRingClass }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($record->notes)
            <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                <div class="flex items-start gap-2">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <h4 class="text-sm font-semibold text-blue-900 dark:text-blue-100 mb-1">Notas</h4>
                        <p class="text-sm text-blue-800 dark:text-blue-200">{{ $record->notes }}</p>
                    </div>
                </div>
            </div>
        @endif
    @endif
</div>
