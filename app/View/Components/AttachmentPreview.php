<?php

namespace App\View\Components;

use Illuminate\View\Component;

class AttachmentPreview extends Component
{
    public $attachments;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($attachments)
    {
        $this->attachments = $attachments;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.attachment-preview');
    }
}