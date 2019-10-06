<?php
namespace app\forms;

use std, gui, framework, app;

class adding extends AbstractForm {

    /**
     * @event submit.action 
     */
    function doSubmitAction(UXEvent $e = null) {
        switch ($this->type->selected) {
            case 'ip':
                $this->addip($this->ip->text, $this->policy->selected);
            break;
        }
    }

    /**
     * @event type.action 
     */
    function doTypeAction(UXEvent $e = null) {    
        switch ($this->type->selected) {
            case 'ip':
                $this->ip->visible = true;
                $this->portmin->visible = false;
                $this->portmax->visible = false;
            break;
            case 'proxy':
                $this->portmax->visible = true;
                $this->portmin->visible = true;
                $this->ip->visible = false;
            break;
        }
    }

}
