<?php

namespace App\Livewire;

use Livewire\Component;

class TimeSlots extends Component
{
    public $count = 0;

    public function addCount()
    {
        $this->count++;
    }

    public function render()
    {
        return view('livewire.time-slots');
    }
}
