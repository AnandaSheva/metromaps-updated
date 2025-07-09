<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ListContainer extends Component
{
    public $id;
    public $position;
    public $theme;
    public $title;
    public $icon;
    public $count;
    public $items;
    public $itemType;

    /**
     * Create a new component instance.
     *
     * @param string $id
     * @param string $position
     * @param string $theme
     * @param string $title
     * @param string $icon
     * @param int $count
     * @param array $items
     * @param string $itemType
     */
    public function __construct(
        $id = '',
        $position = 'primary',
        $theme = 'red',
        $title = '',
        $icon = 'list',
        $count = 0,
        $items = [],
        $itemType = 'default'
    ) {
        $this->id = $id;
        $this->position = $position;
        $this->theme = $theme;
        $this->title = $title;
        $this->icon = $icon;
        $this->count = $count;
        $this->items = $items;
        $this->itemType = $itemType;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.list-container');
    }
}
