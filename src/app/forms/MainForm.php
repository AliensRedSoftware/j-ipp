 <?php
namespace app\forms;

use std, gui, framework, app;

class MainForm extends AbstractForm {

    /**
     * @event chain.construct 
     */
    function doChainConstruct (UXEvent $e = null) {    
        foreach ($this->getChainsToArray() as $chain) {
            $e->sender->items->add($chain);
        }
        $e->sender->selectedIndex = 0;
    }

    /**
     * @event chain.action 
     */
    function doChainAction(UXEvent $e = null) {    
        $this->policy->selected = $this->getPolicyChain($e->sender->selected);
        $this->getChain($e->sender->selected);
    }

    /**
     * @event policy.click-Left 
     */
    function doPolicyClickLeft(UXMouseEvent $e = null) {    
        $this->setChain($this->chain->selected, $e->sender->selected);
        $e->sender->selected = $this->getPolicyChain($this->chain->selected);
    }

    /**
     * @event delete.action 
     */
    function doDeleteAction(UXEvent $e = null) {
        $index = $this->table->selectedIndex + 1; 
        if (uiConfirm('Вы точно хотите это удалить ' . $index . ' ?')) {
            $this->deleteRule($this->chain->selected, $index);
        }
    
    }

    /**
     * @event add.action 
     */
    function doAddAction(UXEvent $e = null) {    
        $this->form('adding')->showAndWait();
    }

    /**
     * @event save.action 
     */
    function doSaveAction(UXEvent $e = null) {   
        $this->fileChooser->execute();
    }

    /**
     * @event load.action 
     */
    function doLoadAction(UXEvent $e = null) {    
        $this->fileChooserAlt->execute();
    }

}
