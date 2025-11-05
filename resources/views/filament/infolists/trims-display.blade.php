@php
    $trims = $getState();
@endphp

@php
    // Mock data con imágenes basado en la estructura del Excel
    if (!$trims || !is_array($trims) || count($trims) === 0) {
        $trims = [
            [
                'photo' => null,
                'description' => 'Main Label - Etiqueta Principal Tejida',
                'code' => 'LBL-001',
                'supplier' => 'Premium Labels Co.',
                'origin' => 'China',
                'comments' => 'Approved - Color and size correct',
            ],
            [
                'photo' => null,
                'description' => 'Care Label - Instrucciones de Cuidado',
                'code' => 'LBL-002',
                'supplier' => 'Premium Labels Co.',
                'origin' => 'China',
                'comments' => 'Approved',
            ],
            [
                'photo' => null,
                'description' => 'YKK Zipper #5 Black',
                'code' => 'YKK-5-BLK',
                'supplier' => 'YKK Corporation',
                'origin' => 'Japan',
                'comments' => 'Approved - Standard quality',
            ],
            [
                'photo' => null,
                'description' => 'Metal Buttons with Logo',
                'code' => 'BTN-001',
                'supplier' => 'Metal Buttons Inc.',
                'origin' => 'Italy',
                'comments' => 'Approved - Logo engraving perfect',
            ],
            [
                'photo' => null,
                'description' => 'Hangtag with Barcode',
                'code' => 'HT-2025-001',
                'supplier' => 'Design Tags Ltd.',
                'origin' => 'USA',
                'comments' => 'Approved with minor adjustments',
            ],
            [
                'photo' => null,
                'description' => 'Packaging, Generic Polybag',
                'code' => 'PKG-001',
                'supplier' => 'Eco Pack Solutions',
                'origin' => 'China',
                'comments' => 'Approved - Eco friendly material',
            ],
        ];
    }
@endphp

@if($trims && is_array($trims) && count($trims) > 0)
<div class="space-y-4">
    @foreach($trims as $index => $trim)
    <div class="fi-ta-ctn divide-y divide-gray-200 overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:divide-white/10 dark:bg-gray-900 dark:ring-white/10">
        <div class="fi-section-header-wrapper p-4 bg-gray-50 dark:bg-white/5">
            <div class="flex items-center gap-3">
                <div class="flex-1">
                    <h3 class="font-semibold text-base text-gray-950 dark:text-white">
                        {{ $trim['description'] ?? 'Sin descripción' }}
                    </h3>
                </div>
                @if(isset($trim['code']))
                <span class="fi-badge fi-color-primary bg-blue-50 text-blue-600 ring-blue-600/10 px-2 py-1 rounded-md text-xs font-medium">
                    {{ $trim['code'] }}
                </span>
                @endif
            </div>
        </div>

        <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="space-y-3">
                @if(isset($trim['code']))
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Code (Código)</p>
                    <p class="text-sm font-mono text-gray-950 dark:text-white">{{ $trim['code'] }}</p>
                </div>
                @endif

                @if(isset($trim['supplier']))
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Supplier (Proveedor)</p>
                    <p class="text-sm text-gray-950 dark:text-white">{{ $trim['supplier'] }}</p>
                </div>
                @endif

                @if(isset($trim['origin']))
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Origin (Origen)</p>
                    <span class="fi-badge fi-color-info bg-cyan-50 text-cyan-600 ring-cyan-600/10 px-2 py-1 rounded-md text-xs font-medium">
                        {{ $trim['origin'] }}
                    </span>
                </div>
                @endif

                @if(isset($trim['comments']))
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Comments (Comentarios de Aprobación)</p>
                    <p class="text-sm text-gray-600 dark:text-gray-300">{{ $trim['comments'] }}</p>
                </div>
                @endif
            </div>

            <div class="flex items-center justify-center bg-gray-100 dark:bg-gray-800 rounded-lg p-4">
                @if(isset($trim['photo']) && $trim['photo'])
                    <img src="{{ \Storage::url($trim['photo']) }}"
                         alt="Trim {{ $trim['description'] ?? '' }}"
                         class="max-h-48 object-contain rounded-lg">
                @else
                    @php
                        $colors = ['6366F1', '8B5CF6', 'EC4899', 'F43F5E', '14B8A6', '06B6D4'];
                        $randomColor = $colors[array_rand($colors)];
                        $description = strtoupper(str_replace(' ', '+', substr($trim['description'] ?? 'TRIM', 0, 20)));
                    @endphp
                    <img src="https://via.placeholder.com/300x300/{{ $randomColor }}/FFFFFF?text={{ $description }}"
                         alt="Trim {{ $trim['description'] ?? '' }}"
                         class="max-h-48 object-contain rounded-lg shadow-md">
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>
@else
<div class="text-sm text-gray-500 dark:text-gray-400 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
    No hay trims o avíos especificados
</div>
@endif
