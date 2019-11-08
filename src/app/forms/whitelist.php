<?php
namespace app\forms;

use std, gui, framework, app;

class whitelist extends AbstractForm {

    /**
     * @event deleteweb.action 
     */
    function doDeletewebAction(UXEvent $e = null) {
        if ($this->web->selected) {
            if (uiConfirm('Вы точно хотите это удалить ?)')) {
                $this->web->items->removeByIndex($this->web->selectedIndex);
                $this->whitelist->set('website', $this->web->itemsText, 'categoria');
                $this->doShow();
            }
        }
    }

    /**
     * @event show 
     */
    function doShow(UXWindowEvent $e = null) {
        app()->getForm(whitelist)->web->items->clear();
        $this->vbox->children->clear();
        $this->vboxAlt->children->clear();
        $i = 0;
        foreach (explode("\n", $this->whitelist->get('website', 'categoria')) as $name) {
            if ($name) {
                app()->getForm(whitelist)->web->items->add($name);
                $i++;
                $abox = new UXCheckBox();
                $abox->text = $name;
                $abox->id = 'abox' . $i;
                $abox->selected = true;
                $abox->minHeight = 24;
                $this->vboxAlt->add($abox);
            }
        }
        //Загрузка пред категорий
        foreach ($this->getCategoriaToArray() as $name) {
            if ($name) { 
                $abox = new UXCheckBox();
                $abox->text = $name;
                $abox->on('click', function () use ($abox) {
                    foreach ($this->vboxAlt->children->toArray() as $data) {
                        $info = explode("\n", $this->whitelist->get('id', $abox->text));
                        foreach ($info as $ls) {
                            if ($ls == $data->text) {
                                $data->selected = $abox->selected;
                            }
                        }
                        
                    }
                });
                $abox->selected = true;
                $abox->minHeight = 24;
                $this->vbox->add($abox);
            }
        }
        if ($this->web->selectedIndex == -1) {
            $this->web->selectedIndex = 0;
        }
    }

    /**
     * @event addweb.action 
     */
    function doAddwebAction(UXEvent $e = null) {
        app()->getForm(webadd)->showAndWait();
    }

    /**
     * @event asynxweb.action 
     */
    function doAsynxwebAction(UXEvent $e = null) {    
        $this->asynxweb(app()->getForm(MainForm)->table, $this->vboxAlt->children->toArray());
    }

    /**
     * @event newcategoria.action 
     */
    function doNewcategoriaAction(UXEvent $e = null) {    
        app()->getForm(Categoria)->showAndWait();
    }

    /**
     * @event checkbox.click-Left 
     */
    function doCheckboxClickLeft(UXMouseEvent $e = null) {
        foreach ($this->vboxAlt->children->toArray() as $data) {
            $data->selected = $e->sender->selected;
        }
    }

    /**
     * @event checkboxAlt.click-Left 
     */
    function doCheckboxAltClickLeft(UXMouseEvent $e = null) {    
        foreach ($this->vbox->children->toArray() as $data) {
            $data->selected = $e->sender->selected;
        }
    }


}
