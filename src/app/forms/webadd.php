<?php
namespace app\forms;

use std, gui, framework, app;

class webadd extends AbstractForm {

    /**
     * @event create.action 
     */
    function doCreateAction(UXEvent $e = null) {
        if (trim($this->web->text)) {
            foreach (explode("\n", $this->whitelist->get('website', 'categoria')) as $section) {
                if ($section == $this->web->text) {
                    return ;
                }
            }
            app()->getForm(whitelist)->web->items->add($this->web->text);
            $this->whitelist->set('website', app()->getForm(whitelist)->web->itemsText, 'categoria');
            $this->form('MainForm')->toast('Успешно :)');
            $this->hide();
        }
    }

    /**
     * @event hide 
     */
    function doHide(UXWindowEvent $e = null) {
        app()->getForm(whitelist)->web->items->clear();
        foreach (explode("\n", $this->whitelist->get('website', 'categoria')) as $section) {
            app()->getForm(whitelist)->web->items->add($section);
        }
    }

}
