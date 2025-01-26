<?php

namespace Takielias\Lab\View\Components;

use Illuminate\View\Component;

class Submit extends Component
{
    public string $title;
    public string $class;

    public function __construct(string $title = 'Submit')
    {
        $this->title = $title;
    }

    public function render()
    {
        return view('lab::components.submit');
    }
}
