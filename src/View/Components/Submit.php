<?php

namespace Takielias\Lab\View\Components;

use Illuminate\View\Component;

class Submit extends Component
{
    public string $title;
    public ?string $askConfirmation;

    /**
     * Create a new component instance.
     *
     * @param string $title The button text
     * @param string|null $askConfirmation Optional confirmation message
     */
    public function __construct(
        string $title = 'Submit',
        string $askConfirmation = null
    )
    {
        $this->title = $title;
        $this->askConfirmation = $askConfirmation;

    }

    public function render()
    {
        return view('lab::components.submit');
    }
}
