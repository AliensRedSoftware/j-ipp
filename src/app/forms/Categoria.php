<?php
namespace app\forms;

use std, gui, framework, app;

class Categoria extends AbstractForm {

    /**
     * @event show 
     */
    function doShow(UXWindowEvent $e = null) {
        $this->listCategoria->items->clear();
        foreach (explode("\n", $this->whitelist->get('id', 'categoria')) as $categoria) { 
            $this->listCategoria->items->add($categoria);
        }
        if ($this->listCategoria->selectedIndex == -1) {
            $this->listCategoria->selectedIndex = 0;
        }
    }

    /**
     * @event remove.action 
     */
    function doRemoveAction(UXEvent $e = null) {
        if ($this->listCategoria->selectedItem) {
            if (uiConfirm('Вы точно хотите удалить категорию ?')) {
                $this->whitelist->removeSection($this->listCategoria->selectedItem);
                $this->doShow();
                $this->toast('Успешно :)');
            }
        }
    }

    /**
     * @event listCategoria.action 
     */
    function doListCategoriaAction(UXEvent $e = null) {
        $this->index->items->clear();
        $this->add->enabled = true;
        $this->remove->enabled = true;
        $this->index->itemsText = $this->whitelist->get('id', $e->sender->selectedItem);
    }

    /**
     * @event add.action 
     */
    function doAddAction(UXEvent $e = null) {
        foreach ($this->index->items->toArray() as $arr) {
            if ($arr == $this->form('whitelist')->web->selected) {
                return;
            }
        }
        $this->index->items->add($this->form('whitelist')->web->selected); 
        $this->whitelist->set('id', $this->index->itemsText, $this->listCategoria->selectedItem);
        $this->toast('Успешно :)');
    }

    /**
     * @event button.action 
     */
    function doButtonAction(UXEvent $e = null) {
        if (uiConfirm('Вы точно хотите удалить это ?')) {
            $this->index->items->removeByIndex($this->index->selectedIndex);
            $this->whitelist->set('id', $this->index->itemsText, $this->listCategoria->selectedItem);
            $this->toast('Успешно :)');
        }
    }

    /**
     * @event hide 
     */
    function doHide(UXWindowEvent $e = null) {    
        //$this->initializeCategoria(app()->getForm(MainForm)->type->selected);
    }
}
