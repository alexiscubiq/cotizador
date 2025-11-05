<div class="flex items-center gap-2">
    @if($this->isWtsMode())
        {{ ($this->cargarTechpackAction)(['size' => 'sm']) }}
        {{ ($this->nuevaCotizacionAction)(['size' => 'sm']) }}
    @endif

    <x-filament-actions::modals />
</div>
