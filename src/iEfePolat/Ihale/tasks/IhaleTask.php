<?php

namespace iEfePolat\Ihale\tasks;

use pocketmine\{
        Player,
        Server,
        scheduler\Task,
        item\Item
};
use iEfePolat\Ihale\{
        Main,
        commands\IhaleCommand
};
use onebone\economyapi\EconomyAPI;

class IhaleTask extends Task{
        
        public $pl;
        public $g;
        public $timer;
        public $api;
        
        public function __construct(IhaleCommand $pl, Player $g, $api){
                $this->pl = $pl;
                $this->g = $g;
                $this->timer = $pl->timer[$g->getName()];
                $this->api = $api;
        }
        public function onRun(int $currentTick){
                foreach($this->api->getServer()->getOnlinePlayers() as $s){
                 $s->sendPopUp("$this->timer");
                }
                if($this->timer == 10 or $this->timer == 9 or $this->timer == 8 or $this->timer == 7 or $this->timer == 6 or $this->timer == 6 or $this->timer == 5 or $this->timer == 4 or $this->timer == 3 or $this->timer == 2 or $this->timer == 1){
                        $this->api->getServer()->broadcastMessage("§e> §6İhalenin Bitmesine son §e$this->timer §6saniye");
                }
                if($this->timer == 0){
                        if($this->pl->ihale["Teklif Veren"] == null){
                                $this->api->getServer()->broadcastMessage("§e> §6İhaleye teklif veren olmadı.");
                                $this->api->getScheduler()->cancelTask($this->pl->id[$this->g->getName()]);
                                $item = $this->pl->ihale["Item"];
                                $itemcount = $this->pl->ihale["Item Count"];
                                $this->g->getInventory()->addItem(Item::get($item,0, $itemcount));
                                $this->pl->ihale = ["Starting" => false];
                        }else{
                                $teklifveren = $this->api->getServer()->getPlayer($this->pl->ihale["Teklif Veren"]);
                                $item = $this->pl->ihale["Item"];
                                $itemcount = $this->pl->ihale["Item Count"];
                                $itemn = $this->pl->ihale["Item Name"];
                                if($teklifveren->isOnline()){
                                $teklifveren->getInventory()->addItem(Item::get($item,0, $itemcount));
                                EconomyAPI::getInstance()->addMoney($this->g, $this->pl->ihale["Teklif Fiyati"]);
                                $this->pl->ihale = [
                                "Starting" => false,
                                ];
                                $this->api->getScheduler()->cancelTask($this->pl->id[$this->g->getName()]);
                                $this->api->getServer()->broadcastMessage("§e> §6İhale Sonuçlandı! ".$teklifveren->getName()." §6adlı oyuncu §5".$itemn." §dadlı eşyayı satın aldı!");
                                }else{
                                        $item = $this->pl->ihale["Item"];
                                        $itemcount = $this->pl->ihale["Item Count"];
                                        $tthis->g->getInventory()->addItem(Item::get($item,0, $itemcount));
                                        $this->api->getScheduler()->cancelTask($this->pl->id[$this->g->getName()]);
                                }
                        }
                }
                $this->timer--;
        }
}
