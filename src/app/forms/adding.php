<?php
namespace app\forms;

use std, gui, framework, app;

class adding extends AbstractForm {

    /**
     * @event submit.action 
     */
    function doSubmitAction(UXEvent $e = null) {
        switch ($this->selectedip->selected) {
            case 'ip':
                $this->addip($this->ip->text, $this->policy->selected);
            break;
            case 'proxy':
                $this->addproxy($this->port->value, $this->policy->selected);
            break;
            case 'Диапазон портов':
                $result = $this->addForproxy($this->policy->selected, $this->portmin->value, $this->portmax->value);
                if (!$result) {
                    UXDialog::showAndWait('Минимальный порт не может быть больше чем максимальный порт', 'ERROR');
                }
            break;
        }
    }

    /**
     * @event selectedip.action 
     */
    function doSelectedipAction(UXEvent $e = null) {
        switch ($e->sender->selected) {
            case 'ip':
                $this->addpanelip->visible = true;
                //proxy
                $this->addpanelproxy->visible = false;
                //proxy_for
                $this->addpanelforproxy->visible = false;
            break;
            case 'proxy':
                $this->addpanelip->visible = false;
                //proxy
                $this->addpanelproxy->visible = true;
                //proxy_for
                $this->addpanelforproxy->visible = false;
            break;
            case 'Диапазон портов':
                $this->addpanelip->visible = false;
                //proxy
                $this->addpanelproxy->visible = false;
                //proxy_for
                $this->addpanelforproxy->visible = true;
            break;
        }
    }

    /**
     * @event show 
     */
    function doShow(UXWindowEvent $e = null) {
        $this->chain->items = app()->getForm(MainForm)->chain->items;
        $this->type->selected = app()->getForm(MainForm)->type->selected;
        $this->chain->selected = app()->getForm(MainForm)->chain->selected;
    }

}
