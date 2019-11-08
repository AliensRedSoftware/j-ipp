<?php
namespace app\forms;

use facade\Json;
use std, gui, framework, app;

class st extends AbstractForm {

    /**
     * @event success.action 
     */
    function doSuccessAction(UXEvent $e = null) {
        if (trim($this->name->text)) {
            foreach ($this->getCategoriaToArray() as $section) {
                if ($section == $this->name->text) {
                    return ;
                }
            }
            app()->getForm(Categoria)->listCategoria->itemsText = $this->whitelist->get('id', 'categoria');
            app()->getForm(Categoria)->listCategoria->items->add($this->name->text);
            $this->whitelist->set('id', app()->getForm(Categoria)->listCategoria->itemsText, 'categoria');
            $this->form('MainForm')->toast('Успешно :)');
            $this->form('MainForm')->loaderCategoria();
        }
    }

}
