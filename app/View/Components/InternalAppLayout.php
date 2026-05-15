<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class InternalAppLayout extends Component
{
    public function __construct(
        public ?string $title = null,
        public ?string $eyebrow = null,
        public ?string $heading = null,
        public ?string $subheading = null,
    ) {}

    public function render(): View|Closure|string
    {
        return view('layouts.internal-app');
    }
}
