<?php

namespace App\Traits;

trait Draftable
{
    public function draft()
    {        
        $this->in_draft = 1;
        $this->silent_save = 0;
        $this->save();

        return $this;
    }

    public function publish()
    {        
        $this->in_draft = 0;
        $this->silent_save = 0;
        $this->save();
        
        return $this;
    }
}