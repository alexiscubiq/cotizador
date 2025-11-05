<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class ViewModeToggle extends Component
{
    public $viewMode = 'wts'; // 'wts' or 'supplier'

    public function mount()
    {
        $this->viewMode = session('view_mode', Auth::user()?->user_type === 'supplier' ? 'supplier' : 'wts');
    }

    public function toggleMode($mode)
    {
        $this->viewMode = $mode;
        session(['view_mode' => $mode]);
        $this->dispatch('view-mode-changed', mode: $mode);

        // Refresh the page to apply changes
        return redirect()->to(request()->header('Referer') ?: '/admin');
    }

    public function render()
    {
        return view('livewire.view-mode-toggle');
    }
}
