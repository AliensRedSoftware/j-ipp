<?php
namespace app\modules;

use facade\Json;
use std, gui, framework, app;

class MainModule extends AbstractModule {

    /**
     * @event fileChooser.action 
     */
    function doFileChooserAction(ScriptEvent $e = null) {
        $path = $e->sender->file;
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
        fs::delete('log');
    }
    
    /**
     * Асинхронизация веб-сайта
     */
    function asynxweb (UXTableView $table, $weblist) {
        $type = $this->form('MainForm')->type->selected;
        foreach ($weblist as $val) {
            if ($val->selected) {//10.42.0.1
                if ($table->items->isNotEmpty()) {
                    foreach ($table->items->toArray() as $data) {
                        if ($val->text == $data['source']) {
                            $Создать = false;
                            break;
                        } else {
                            $Создать = true;
                        }
                    }
                } else {
                    $Создать = true;
                }
                if ($Создать) {
                    $this->addip($val->text, 'ACCEPT');
                }
            }
        }
        Logger::info('Успешно :)');
        $this->initializeCache();
        $this->form('MainForm')->table->items->clear();
        $this->form('MainForm')->loaderCategoria();
    }

    /**
     * @event fileChooserAlt.action 
     */
    function doFileChooserAltAction(ScriptEvent $e = null) {    
        $path = $e->sender->file;
        execute("xterm -e sudo iptables-restore $path", true);
        $this->getChain($this->form('MainForm')->chain->selected);
        alert('Успешно');
        fs::delete('log');
    }

    /**
     * Возвращаем цепи
     * @return array 
     */
    public function getChainsToArray () {
        $type = $this->form('MainForm')->type->selected;
        $arr = [];
        execute("xterm -l -lf log -e sudo iptables -t $type -L --line-numbers", true);
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
        fs::delete('log');
        return $arr;
    }
    
    /**
     * Возвращаем цепи индексированные
     * @return array 
     */
    public function getChainsCacheToArray () {
        $type = $this->form('MainForm')->type->selected;
        $arr = [];
        foreach (Json::fromFile('cache') as $key => $chain) {
            array_push($arr, $key);
        }
        return $arr;
    }
    
    /**
     * Возвращаем политику для цепи
     * @return bool 
     */
    public function getPolicyChain ($chain) {
        $type = $this->form('MainForm')->type->selected;
        execute("xterm -l -lf log -e sudo iptables -t $type -L --line-numbers", true);
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
        fs::delete('log');
    }
    
    /**
     * -------------------------------------
     * Возвращаем цепь данные в виде массива
     * chain    -    Название цепи
     * -------------------------------------
     * @return bool 
     */
    public function getChain ($chain) {
        $type = $this->form('MainForm')->type->selected;
        execute("xterm -l -lf log -e sudo iptables -t $type -L --line-numbers", true);
        $лог = explode("\n", file_get_contents('log'));
        $result = [];
        foreach ($лог as $log) {
            $log = trim($log);
            $ls = explode(' ', $log);
            $i = 0;
            unset($protocol);
            unset($ssl);
            unset($port);
            foreach ($ls as $lsm) {
                $arr = [];
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
                            case 7://protocol
                                $protocol = $lsm;
                            break;
                            case 8://protocol
                                $gig = explode(':', $lsm);
                                if ($gig[1]) {
                                    $ssl = $gig[0];
                                    $port = $gig[1];
                                }
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
                $arr['id'] = $id;
                $arr['target'] = $target;
                $arr['prot'] = $prot;
                $arr['opt'] = $opt;
                $arr['source'] = $source;
                $arr['destination'] = $destination;
                $arr['protocol'] = $protocol;
                $arr['ssl'] = $ssl;
                $arr['port'] = $port;
                $result[count($result) + 1] = $arr;
            }
        }
        fs::delete('log');
        return $result;
    }
    
    /**
     * Установить цепь политику 
     */
    public function setChain ($chain, $selected) {
        $type = $this->form('MainForm')->type->selected;
        if ($selected) {
            $selected = 'ACCEPT';
        } else {
            $selected = 'DROP';
        }
        execute("xterm -l -lf log -e sudo iptables -t $type -P $chain $selected", true);
        fs::delete('log');
    }
    
    /**
     * Удалить из таблицы правило
     */
    public function deleteRule ($chain, $num) {
        $type = $this->form('MainForm')->type->selected;
        execute("xterm -l -lf log -e sudo iptables -t $type -D $chain $num", true);
        fs::delete('log');
    }
    
    /**
     * Доступность айпи адрес
     * ----------------------
     * port - порт
     * policy - политика
     * ----------------------
     */
    public function addip ($ip, $policy) {
        if (app()->getForm(adding)->visible) {
            $type = $this->form('adding')->type->selected;
            $chain = $this->form('adding')->chain->selected;
        } else {
            $type = $this->form('MainForm')->type->selected;
            $chain = $this->form('MainForm')->chain->selected;
        }
        execute("xterm -l -lf log -e sudo iptables -t $type -A $chain -s $ip -j $policy", true);
        fs::delete('log');
    }
    
    /**
     * Доступность прокси адрес
     * ------------------------
     * port - порт
     * policy - политика
     * ------------------------
     */
    public function addproxy ($port, $policy) {
        $type = $this->form('adding')->type->selected;
        $chain = $this->form('adding')->chain->selected;
        $protocol = $this->form('adding')->protocol->selected;
        $ssl = $this->form('adding')->ssl->selected;
        if ($ssl) {
            $ssl = '--sport';
        } else {
            $ssl = '--dport';
        }
        execute("xterm -l -lf log -e sudo iptables -t $type -A $chain -p $protocol -m $protocol $ssl $port -j $policy", true);
        fs::delete('log');
    }
    
    /**
     * Доступность прокси адрес дерево
     * ------------------------
     * port - порт
     * policy - политика
     * ------------------------
     */
    public function addForproxy ($policy, $min, $max) {
        $type = $this->form('adding')->type->selected;
        $chain = $this->form('adding')->chain->selected;
        $protocol = $this->form('adding')->protocolproxy->selected;
        $ssl = $this->form('adding')->ssl_for->selected;
        if ($min >= $max)  {
            return false;
        }
        $port = $min . ':' . $max;
        if ($ssl) {
            $ssl = '--sport';
        } else {
            $ssl = '--dport';
        }
        execute("xterm -l -lf log -e sudo iptables -t $type -A $chain -p $protocol -m $protocol $ssl $port -j $policy", true);
        fs::delete('log');
        return true;
    }
    
    /**
     * Поднять на уровень вверх
     */
     public function UpChain (UXTableView $table) {
         $type = $this->form('MainForm')->type->selected;
         $chain = $this->form('MainForm')->chain->selected;
         if ($table->selectedIndex == 0 || $table->selectedIndex == -1 || !$table->selectedItem) {
             return false;
         } else {
             $table->selectedItem['target'];
             pre($table->selectedItem['target']);
             execute("xterm -l -lf log -e sudo iptables -t $type -I $chain -s $ip -j $policy", true);
             fs::delete('log');
             return true;
         }
     }
     
     /**
      * Инитиализация категорий
      */
    public function initializeCache () {
        $arr = [];
        foreach ($this->getChainsToArray() as $chain) {
             $arr += [$chain => ['ALL' => $this->getChain($chain)]];
        }
        Json::toFile('cache', $arr);
    }
     
     /**
      * Возвращаем все категорий в виде массива
      * @return array
      */
    public function getCategoriaToArray () {
        $arr = [];
        foreach (explode("\n", $this->whitelist->get('id', 'categoria')) as $section) {
            array_push($arr, $section);
        }
        return $arr;
    }
}
