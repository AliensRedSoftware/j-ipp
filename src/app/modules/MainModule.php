<?php
namespace app\modules;

use std, gui, framework, app;

class MainModule extends AbstractModule {

    /**
     * @event fileChooser.action 
     */
    function doFileChooserAction(ScriptEvent $e = null) {
        $path = $e->sender->file;
        fs::delete('log');
        execute("xterm -l -lf log -e sudo iptables-save", true);
        $лог = explode("\n", file_get_contents('log'));
        foreach ($лог as $log) {
            if (str::startsWith($log, '#')) {
                $идет = true;
            }
            if ($идет) {
                $сбор .= $log;
            }
        }
        file_put_contents($path, $сбор);
    }

    /**
     * @event fileChooserAlt.action 
     */
    function doFileChooserAltAction(ScriptEvent $e = null) {    
        $path = $e->sender->file;
        fs::delete('log');
        execute("xterm -e sudo iptables-restore $path", true);
        $this->getChain($this->form('MainForm')->chain->selected);
        alert('Успешно');
    }
    
    /**
     * Возвращаем цепи
     * @return array 
     */
    public function getChainsToArray () {
        $arr = [];
        fs::delete('log');
        execute('xterm -l -lf log -e sudo iptables -L --line-numbers', true);
        $лог = explode("\n", file_get_contents('log'));
        foreach ($лог as $log) {
            $log = trim($log);
            $ls = explode(' ', $log);
            foreach ($ls as $lsm) {
                $lsm = trim($lsm);
                if ($ИскатьЦепочки) {
                    unset($ИскатьЦепочки);
                    array_push($arr, $lsm);
                }
                if ($lsm == 'Chain') {
                    $ИскатьЦепочки = true;
                } 
            }
        }
        return $arr;
    }
    
    /**
     * Возвращаем политику для цепи
     * @return bool 
     */
    public function getPolicyChain ($chain) {
        fs::delete('log');
        execute('xterm -l -lf log -e sudo iptables -L --line-numbers', true);
        $лог = explode("\n", file_get_contents('log'));
        foreach ($лог as $log) {
            $log = trim($log);
            $ls = explode(' ', $log);
            foreach ($ls as $lsm) {
                $lsm = trim($lsm);
                if ($ИскатьЦепь) {
                    $цепь = $lsm;
                    unset($ИскатьЦепь);
                }
                if ($lsm == 'Chain') {
                    $ИскатьЦепь = true;
                }
                if ($цепь == $chain) {
                    if ($policy) {
                        if ($lsm == 'ACCEPT)') {
                            return true;
                        } elseif ($lsm == 'DROP)') {
                            return false;
                        }
                        unset($policy);
                    }
                    if ($lsm == '(policy') {
                        $policy = true;
                    }
                }
            }
        }
    }
    
    /**
     * Возвращаем цепь данные
     * @return bool 
     */
    public function getChain ($chain) {
        $this->form('MainForm')->table->items->clear();
        fs::delete('log');
        execute('xterm -l -lf log -e sudo iptables -L --line-numbers', true);
        $лог = explode("\n", file_get_contents('log'));
        foreach ($лог as $log) {
            $log = trim($log);
            $ls = explode(' ', $log);
            $i = 0;
            foreach ($ls as $lsm) {
                $lsm = trim($lsm);
                if ($ИскатьЦепь) {
                    $цепь = $lsm;
                    unset($ИскатьЦепь);
                    break;
                }
                if ($lsm == 'Chain') {
                    $ИскатьЦепь = true;
                    unset($tuk);
                }
                if ($tuk) {
                    if ($lsm) {
                        $tableWrite = true;
                        $i++;
                        switch ($i) {
                            case 1://id
                                $id = $lsm;
                            break;
                            case 2://target
                                $target = $lsm;
                            break;
                            case 3://prot
                                $prot = $lsm;
                            break;
                            case 4://opt
                                $opt = $lsm;
                            break;
                            case 5://source
                                $source = $lsm;
                            break;
                            case 6://destination
                                $destination = $lsm;
                            break;
                        }
                    } else {
                        unset($tableWrite);
                    }
                }
                if ($цепь == $chain) {
                    $tuk = true;
                    unset($цепь);
                    break;
                }
            }
            if ($tuk && $tableWrite) {
                $this->form('MainForm')->table->items->add([
                    'id' => $id,
                    'target' => $target,
                    'prot' => $prot,
                    'opt' => $opt,
                    'source' => $source,
                    'destination' => $destination
                ]);
            }
        }
    }
    
    /**
     * Установить цепь политику 
     */
    public function setChain ($chain, $selected) {
        if ($selected) {
            $selected = 'ACCEPT';
        } else {
            $selected = 'DROP';
        }
        execute("xterm -l -lf log -e sudo iptables -P $chain $selected", true);
    }
    
    /**
     * Удалить из таблицы правило
     */
    public function deleteRule ($chain, $num) {
        fs::delete('log');
        execute("xterm -l -lf log -e sudo iptables -D $chain $num", true);
        $this->getChain($chain);
    }
    
    /**
     * Доступность ip адрес 
     */
    public function addip ($ip, $policy) {
        $chain = $this->form('MainForm')->chain->selected;
        fs::delete('log');
        execute("xterm -l -lf log -e sudo iptables -A $chain -s $ip -j $policy", true);
        $this->getChain($chain);
    }
}
