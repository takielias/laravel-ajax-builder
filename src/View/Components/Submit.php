<?php

namespace Takielias\Lab\View\Components;

use Illuminate\View\Component;

class Submit extends Component
{
    public string $title;
    public string $class;

    public function __construct(string $title = 'Submit', string $class = '')
    {
        $this->title = $title;
        $this->class = $this->buildClass($class);
    }

    private function buildClass(string $class): string
    {
        $requiredClasses = 'ajax-submit-button has-spinner';
        $baseClasses = 'btn btn-primary';
        return trim($class) ? trim($class) . ' ' . $baseClasses . ' ' . $requiredClasses : $baseClasses . ' ' . $requiredClasses;
    }

    public function render()
    {
        return view('lab::components.submit');
    }
}
