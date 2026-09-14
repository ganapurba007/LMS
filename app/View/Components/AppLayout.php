<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    public bool $suppressGlobalAlerts;

    public function __construct(bool $suppressGlobalAlerts = false)
    {
        $this->suppressGlobalAlerts = $suppressGlobalAlerts;
    }

    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('layouts.app');
    }
}
