<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Header extends Component
{
    public $exportUrl, $createUrl, $exportLabel, $createLabel;

    public function __construct(
        $exportUrl = '#',
        $createUrl = '#',
        $exportLabel = 'Export',
        $createLabel = 'New',
    ) {
        $this->exportUrl   = $exportUrl;
        $this->createUrl   = $createUrl;
        $this->exportLabel = $exportLabel;
        $this->createLabel = $createLabel;
    }

    public function render()
    {
        return view('components.header');
    }
}

