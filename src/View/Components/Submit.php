<?php

namespace Takielias\Lab\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Submit extends Component
{
    /**
     * @param  string  $title  The button text
     * @param  string|null  $askConfirmation  Optional confirmation message
     */
    public function __construct(
        public string $title = 'Submit',
        public ?string $askConfirmation = null,
    ) {}

    public function render(): View
    {
        return view('lab::components.submit');
    }
}
