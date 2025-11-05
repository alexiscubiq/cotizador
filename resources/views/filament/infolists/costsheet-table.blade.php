@php
    $costsheet = $getState();
    $isWts = session('view_mode', 'wts') === 'wts';

    // Mock data si no hay datos reales
    if (!$costsheet || !is_array($costsheet) || (empty($costsheet['materials']) && empty($costsheet['labor']) && empty($costsheet['overhead']))) {
        $costsheet = [
            'materials' => [
                ['item' => 'Tela Principal - Jersey 30/1 (1.2 kg)', 'cost' => 5.40],
                ['item' => 'Tela Secundaria - Rib 1x1 (0.3 kg)', 'cost' => 1.56],
                ['item' => 'Hilo de costura', 'cost' => 0.40],
                ['item' => 'Etiqueta Principal', 'cost' => 0.15],
                ['item' => 'Etiqueta de Cuidado', 'cost' => 0.08],
                ['item' => 'Hangtag', 'cost' => 0.25],
                ['item' => 'Polybag', 'cost' => 0.10],
            ],
            'labor' => [
                ['item' => 'Corte', 'cost' => 0.80],
                ['item' => 'Costura', 'cost' => 1.50],
                ['item' => 'Planchado y Acabado', 'cost' => 0.40],
                ['item' => 'Control de Calidad', 'cost' => 0.30],
                ['item' => 'Screen Print - Front (3 colores)', 'cost' => 0.75],
                ['item' => 'Bordado - Left Chest', 'cost' => 1.20],
            ],
            'overhead' => [
                ['item' => 'Overhead / Gastos Administrativos', 'cost' => 0.80],
                ['item' => 'Embalaje y Cartonaje', 'cost' => 0.35],
                ['item' => 'Testing y Certificaciones', 'cost' => 0.25],
                ['item' => 'Transporte Local', 'cost' => 0.20],
            ],
        ];
    }
@endphp

@if($costsheet && is_array($costsheet))
<div class="fi-ta-ctn divide-y divide-gray-200 overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:divide-white/10 dark:bg-gray-900 dark:ring-white/10">
    <table class="fi-ta-table w-full table-auto divide-y divide-gray-200 text-start dark:divide-white/5">
        <thead class="divide-y divide-gray-200 dark:divide-white/5">
            <tr class="bg-gray-50 dark:bg-white/5">
                <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6 font-semibold text-sm text-gray-950 dark:text-white">Categoría</th>
                <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6 font-semibold text-sm text-gray-950 dark:text-white">Item</th>
                <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6 font-semibold text-sm text-gray-950 dark:text-white text-right">Costo</th>
                @if($isWts)
                <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6 font-semibold text-sm text-gray-950 dark:text-white text-right">Margen %</th>
                @endif
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 whitespace-nowrap dark:divide-white/5">
            @php
                $totalMaterials = 0;
                $totalLabor = 0;
                $totalOverhead = 0;
            @endphp

            @if(isset($costsheet['materials']))
                @foreach($costsheet['materials'] as $material)
                    @php
                        $totalMaterials += $material['cost'];
                        $margin = rand(15, 35); // Mock margin percentage
                    @endphp
                    <tr class="fi-ta-row hover:bg-gray-50 dark:hover:bg-white/5">
                        <td class="fi-ta-cell px-3 py-4 text-sm text-gray-950 dark:text-white">
                            <span class="fi-badge fi-color-info bg-blue-50 text-blue-600 ring-blue-600/10 px-2 py-1 rounded-md text-xs font-medium">Materiales</span>
                        </td>
                        <td class="fi-ta-cell px-3 py-4 text-sm text-gray-950 dark:text-white">{{ $material['item'] }}</td>
                        <td class="fi-ta-cell px-3 py-4 text-sm text-gray-950 dark:text-white text-right font-mono">${{ number_format($material['cost'], 2) }}</td>
                        @if($isWts)
                        <td class="fi-ta-cell px-3 py-4 text-sm text-success-600 dark:text-success-400 text-right font-mono font-semibold">{{ $margin }}%</td>
                        @endif
                    </tr>
                @endforeach
            @endif

            @if(isset($costsheet['labor']))
                @foreach($costsheet['labor'] as $labor)
                    @php
                        $totalLabor += $labor['cost'];
                        $margin = rand(15, 35); // Mock margin percentage
                    @endphp
                    <tr class="fi-ta-row hover:bg-gray-50 dark:hover:bg-white/5">
                        <td class="fi-ta-cell px-3 py-4 text-sm text-gray-950 dark:text-white">
                            <span class="fi-badge fi-color-warning bg-yellow-50 text-yellow-600 ring-yellow-600/10 px-2 py-1 rounded-md text-xs font-medium">Mano de Obra</span>
                        </td>
                        <td class="fi-ta-cell px-3 py-4 text-sm text-gray-950 dark:text-white">{{ $labor['item'] }}</td>
                        <td class="fi-ta-cell px-3 py-4 text-sm text-gray-950 dark:text-white text-right font-mono">${{ number_format($labor['cost'], 2) }}</td>
                        @if($isWts)
                        <td class="fi-ta-cell px-3 py-4 text-sm text-success-600 dark:text-success-400 text-right font-mono font-semibold">{{ $margin }}%</td>
                        @endif
                    </tr>
                @endforeach
            @endif

            @if(isset($costsheet['overhead']))
                @foreach($costsheet['overhead'] as $overhead)
                    @php
                        $totalOverhead += $overhead['cost'];
                        $margin = rand(15, 35); // Mock margin percentage
                    @endphp
                    <tr class="fi-ta-row hover:bg-gray-50 dark:hover:bg-white/5">
                        <td class="fi-ta-cell px-3 py-4 text-sm text-gray-950 dark:text-white">
                            <span class="fi-badge fi-color-gray bg-gray-50 text-gray-600 ring-gray-600/10 px-2 py-1 rounded-md text-xs font-medium">Overhead</span>
                        </td>
                        <td class="fi-ta-cell px-3 py-4 text-sm text-gray-950 dark:text-white">{{ $overhead['item'] }}</td>
                        <td class="fi-ta-cell px-3 py-4 text-sm text-gray-950 dark:text-white text-right font-mono">${{ number_format($overhead['cost'], 2) }}</td>
                        @if($isWts)
                        <td class="fi-ta-cell px-3 py-4 text-sm text-success-600 dark:text-success-400 text-right font-mono font-semibold">{{ $margin }}%</td>
                        @endif
                    </tr>
                @endforeach
            @endif

            @php
                $subtotal = $totalMaterials + $totalLabor + $totalOverhead;
            @endphp

            <tr class="bg-gray-100 dark:bg-gray-800 font-semibold">
                <td colspan="2" class="fi-ta-cell px-3 py-4 text-sm text-gray-950 dark:text-white">COSTO TOTAL (FOB)</td>
                <td class="fi-ta-cell px-3 py-4 text-sm text-gray-950 dark:text-white text-right font-mono text-lg">${{ number_format($subtotal, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="p-4 bg-gray-50 dark:bg-gray-800/50 grid grid-cols-3 gap-4 text-sm">
        <div>
            <p class="text-gray-500 dark:text-gray-400">Materiales</p>
            <p class="font-semibold text-gray-950 dark:text-white">${{ number_format($totalMaterials, 2) }}</p>
        </div>
        <div>
            <p class="text-gray-500 dark:text-gray-400">Mano de Obra</p>
            <p class="font-semibold text-gray-950 dark:text-white">${{ number_format($totalLabor, 2) }}</p>
        </div>
        <div>
            <p class="text-gray-500 dark:text-gray-400">Overhead</p>
            <p class="font-semibold text-gray-950 dark:text-white">${{ number_format($totalOverhead, 2) }}</p>
        </div>
    </div>
</div>
@else
<div class="text-sm text-gray-500 dark:text-gray-400">No hay información de costsheet disponible</div>
@endif
