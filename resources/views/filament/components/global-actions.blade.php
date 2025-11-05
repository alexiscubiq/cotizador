<div class="flex items-center gap-2">
    {{-- Botón Cargar techpack --}}
    <x-filament::button
        wire:click="$dispatch('open-modal', { id: 'cargar-techpack' })"
        outlined
        color="warning"
        icon="heroicon-o-plus"
        size="sm"
    >
        Cargar techpack
    </x-filament::button>

    {{-- Botón Nueva cotización --}}
    <x-filament::button
        :href="route('filament.admin.resources.quotes.create')"
        color="warning"
        icon="heroicon-o-plus"
        size="sm"
    >
        Nueva cotización
    </x-filament::button>
</div>
