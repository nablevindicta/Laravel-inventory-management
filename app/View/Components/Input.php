<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Input extends Component
{
    public $title, $name, $type, $placeholder, $value;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($title, $name, $type = 'text', $value = null, $placeholder = null)
    {
        $this->title = $title;
        $this->name = $name;
        $this->type = $type;
        $this->value = $value;
        $this->placeholder = $placeholder ?? "Masukkan {$title}"; // fallback otomatis
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.input');
    }
}