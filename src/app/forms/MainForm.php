 <?php
namespace app\forms;

use facade\Json;
use std, gui, framework, app;

class MainForm extends AbstractForm {

    /**
     * @event chain.construct 
     */
    function doChainConstruct (UXEvent $e = null) {
        $this->chain->items->clear();
        foreach ($this->getChainsCacheToArray() as $chain) {
            $this->chain->items->add($chain);
        }
        $this->chain->selectedIndex = 0;
        fs::delete('log');
    }

    /**
     * @event chain.action 
     */
    function doChainAction(UXEvent $e = null) {    
        $this->table->items->clear();
        $this->policy->selected = $this->getPolicyChain($e->sender->selected);
        $this->loaderCategoria();
    }

    /**
     * @event policy.click-Left 
     */
    function doPolicyClickLeft(UXMouseEvent $e = null) {    
        $this->setChain($this->chain->selected, $e->sender->selected);
        $e->sender->selected = $this->getPolicyChain($this->chain->selected);
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

    /**
     * @event type.action 
     */
    function doTypeAction(UXEvent $e = null) {    
        $this->doShow();
        $this->doChainConstruct();
    }

    /**
     * @event delete.action 
     */
    function doDeleteAction(UXEvent $e = null) {
        if ($this->table->selectedItem) {
            $index = $this->table->selectedIndex + 1; 
            if (uiConfirm('Вы точно хотите это удалить ' . $index . ' ?')) {
                $this->deleteRule($this->chain->selected, $index);
                $this->table->items->removeByIndex($this->table->selectedIndex);
                $this->initializeCache();
            }
        }
    }

    /**
     * @event add.action 
     */
    function doAddAction(UXEvent $e = null) {
        $this->form('adding')->showAndWait();
    }

    /**
     * @event down.action 
     */
    function doDownAction(UXEvent $e = null) {    
        
    }

    /**
     * @event up.action 
     */
    function doUpAction(UXEvent $e = null) {    
        if($this->UpChain($this->table)) {
            
        } else {
            $this->toast('Поднять вверх не получается ;)');
        }
    }

    /**
     * @event st.action 
     */
    function doStAction(UXEvent $e = null) {    
        $this->form('st')->showAndWait();
    }

    /**
     * @event show 
     */
    function doShow(UXWindowEvent $e = null) {    
        $this->initializeCache();
    }

    /**
     * @event deleteall.action 
     */
    function doDeleteallAction(UXEvent $e = null) {
        if (uiConfirm('Вы точно хотите всё удалить из цепи ?)')) {
            for ($i = $this->table->items->count; $i >= 0; $i--) {
                $this->deleteRule($this->chain->selected, $data['id']);
            }
            $this->table->items->clear();
            foreach ($this->getChain($this->chain->selected) as $chain) {
                $this->table->items->add([
                    'id' => $chain['id'],
                    'target' => $chain['target'],
                    'prot' => $chain['prot'],
                    'opt' => $chain['opt'],
                    'source' => $chain['source'],
                    'destination' => $chain['destination']
                ]);
            }
            $this->initializeCache();
        }
    }

    /**
     * @event cat.action 
     */
    function doCatAction(UXEvent $e = null) {
        $eva = new UXListView();
        $type = $this->type->selected;
        $chain = $this->chain->selected;
        $this->table->items->clear();
        foreach (Json::fromFile('cache')[$chain]['ALL'] as $chain) {
            $this->table->items->add([
                'id' => $chain['id'],
                'target' => $chain['target'],
                'prot' => $chain['prot'],
                'opt' => $chain['opt'],
                'source' => $chain['source'],
                'destination' => $chain['destination'],
                'protocol' => $chain['protocol'],
                'ssl' => $chain['ssl'],
                'port' => $chain['port']
            ]);
        }
        if ($e->sender->selectedIndex >= 1) {
            $arr = explode("\n", $this->ini->get('id', $e->sender->selected));
            foreach ($arr as $val) {
                foreach ($this->table->items->toArray() as $chain) {
                    if ($chain['id'] == $val) {
                        $eva->items->add([
                            'id' => $chain['id'],
                            'target' => $chain['target'],
                            'prot' => $chain['prot'],
                            'opt' => $chain['opt'],
                            'source' => $chain['source'],
                            'destination' => $chain['destination']
                        ]);
                    }
                }
            }
            $this->table->items->clear();
            foreach ($eva->items->toArray() as $chain) {
                $this->table->items->add([
                    'id' => $chain['id'],
                    'target' => $chain['target'],
                    'prot' => $chain['prot'],
                    'opt' => $chain['opt'],
                    'source' => $chain['source'],
                    'destination' => $chain['destination']
                ]);
            }
        }
    }

    /**
     * @event whitebtn.action 
     */
    function doWhitebtnAction(UXEvent $e = null) {
        app()->getForm(whitelist)->showAndWait();
    }

    /**
     * @event close 
     */
    function doClose(UXWindowEvent $e = null) {    
        fs::delete('cache');
    }

    /**
     * Подгузить категорий
     */
    function loaderCategoria(UXEvent $e = null) {
        $this->cat->items->clear();
        $this->cat->items->add('Всё');
        foreach (explode("\n", $this->whitelist->get('id', 'categoria')) as $data) {
            if ($data) {
                $this->cat->items->add($data);
            }
        }
        if (!$this->cat->selected) {
            $this->cat->selectedIndex = 0;
        }
    }

}
