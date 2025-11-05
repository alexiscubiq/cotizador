@php
    $placements = $getState();
@endphp

@php
    // Mock data con imágenes si no hay datos reales
    if (!$placements || !is_array($placements) || count($placements) === 0) {
        $placements = [
            [
                'placement' => 'Front',
                'decoration_type' => 'Screen Print',
                'width' => '25',
                'height' => '30',
                'location_reference' => '7 pulgadas desde HPS, centro del pecho',
                'colors_count' => '3',
                'pantone_colors' => 'PMS 186C, PMS 2925C, PMS 877C',
                'ink_type' => 'Plastisol',
                'artwork_image' => null,
                'special_notes' => 'Logo principal con efecto metalizado en el color plateado',
            ],
            [
                'placement' => 'Back',
                'decoration_type' => 'Embroidery',
                'width' => '15',
                'height' => '12',
                'location_reference' => 'Centro espalda, 4 pulgadas debajo del cuello',
                'colors_count' => '2',
                'pantone_colors' => 'PMS Black C, PMS 185C',
                'ink_type' => 'Rayon Thread',
                'artwork_image' => null,
                'special_notes' => 'Bordado 3D con realce',
            ],
            [
                'placement' => 'Left Sleeve',
                'decoration_type' => 'Heat Transfer',
                'width' => '8',
                'height' => '8',
                'location_reference' => '5 cm desde la costura del hombro',
                'colors_count' => '4',
                'pantone_colors' => 'Full Color',
                'ink_type' => 'Vinyl',
                'artwork_image' => null,
                'special_notes' => 'Transfer de alta calidad resistente a 40 lavados',
            ],
        ];
    }
@endphp

@if($placements && is_array($placements) && count($placements) > 0)
<div class="space-y-4">
    @foreach($placements as $index => $placement)
    <div class="fi-ta-ctn divide-y divide-gray-200 overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:divide-white/10 dark:bg-gray-900 dark:ring-white/10">
        <div class="fi-section-header-wrapper p-4 bg-gray-50 dark:bg-white/5">
            <div class="flex items-center gap-3">
                <div class="flex-1">
                    <h3 class="font-semibold text-base text-gray-950 dark:text-white">
                        {{ $placement['placement'] ?? 'Sin ubicación' }} - {{ $placement['decoration_type'] ?? 'Sin tipo' }}
                    </h3>
                </div>
                @if(isset($placement['colors_count']))
                <span class="fi-badge fi-color-info bg-blue-50 text-blue-600 ring-blue-600/10 px-2 py-1 rounded-md text-xs font-medium">
                    {{ $placement['colors_count'] }} colores
                </span>
                @endif
            </div>
        </div>

        <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="space-y-3">
                @if(isset($placement['width']) || isset($placement['height']))
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Dimensiones</p>
                    <p class="text-sm font-medium text-gray-950 dark:text-white">
                        {{ $placement['width'] ?? '-' }} cm × {{ $placement['height'] ?? '-' }} cm
                    </p>
                </div>
                @endif

                @if(isset($placement['location_reference']))
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Referencia de Ubicación</p>
                    <p class="text-sm text-gray-950 dark:text-white">{{ $placement['location_reference'] }}</p>
                </div>
                @endif

                @if(isset($placement['pantone_colors']))
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Colores Pantone</p>
                    <p class="text-sm font-mono text-gray-950 dark:text-white">{{ $placement['pantone_colors'] }}</p>
                </div>
                @endif

                @if(isset($placement['ink_type']))
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Tipo de Tinta/Hilo</p>
                    <span class="fi-badge fi-color-warning bg-yellow-50 text-yellow-600 ring-yellow-600/10 px-2 py-1 rounded-md text-xs font-medium">
                        {{ $placement['ink_type'] }}
                    </span>
                </div>
                @endif

                @if(isset($placement['special_notes']))
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Notas Especiales</p>
                    <p class="text-sm text-gray-600 dark:text-gray-300">{{ $placement['special_notes'] }}</p>
                </div>
                @endif
            </div>

            <div class="flex items-center justify-center bg-gray-100 dark:bg-gray-800 rounded-lg p-4">
                @if(isset($placement['artwork_image']) && $placement['artwork_image'])
                    <img src="{{ \Storage::url($placement['artwork_image']) }}"
                         alt="Arte {{ $placement['placement'] ?? '' }}"
                         class="max-h-48 object-contain rounded-lg">
                @else
                    @php
                        $colors = ['EF4444', '3B82F6', '10B981', 'F59E0B', '8B5CF6', 'EC4899'];
                        $randomColor = $colors[array_rand($colors)];
                        $artType = $placement['decoration_type'] ?? 'ARTWORK';
                        $placementText = strtoupper(str_replace(' ', '+', $placement['placement'] ?? 'DESIGN'));
                    @endphp
                    <img src="https://via.placeholder.com/400x300/{{ $randomColor }}/FFFFFF?text={{ $placementText }}+{{ str_replace(' ', '+', $artType) }}"
                         alt="Arte {{ $placement['placement'] ?? '' }}"
                         class="max-h-48 object-contain rounded-lg shadow-md">
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>
@else
<div class="text-sm text-gray-500 dark:text-gray-400 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
    No hay artes o decoraciones especificadas
</div>
@endif
