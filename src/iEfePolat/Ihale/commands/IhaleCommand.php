<?php

namespace iEfePolat\Ihale\commands;

use pocketmine\{
        Player,
        Server,
        command\Command,
        command\CommandSender,
        item\Item,
        scheduler\Task,
        block\Block
};
use iEfePolat\Ihale\{
        Main,
        tasks\IhaleTask
};
use jojoe77777\FormAPI\{
        SimpleForm,
        CustomForm
};
use onebone\economyapi\EconomyAPI;

class IhaleCommand extends Command{

        public $ihale = ["Starting" => false];
        public $timer;
        public $id = [];
        public $g;
        public $item;
        public $count;
        public $dataOne;
        public $dataTwo;
        public $name;
        public $nick;

        public function __construct(){
                parent::__construct("ihale", "Ihale ana komutu.", "/ihale");
        }
        
        public function execute(CommandSender $g, string $label, array $args){
                if($args == null){
                        $g->sendMessage("§e---- §6İhale §e-----\n§b/ihale baslat §6Ihale Başlatır\n§b/ihale yatir §6Mevcut İhaleye Para Yatırır");
                }elseif($args[0] == "baslat"){
                if(!$g->getInventory()->getItemInHand()->getId() == 0){
                        $this->ihaleForm($g);
                }
                }elseif($args[0] == "yatir"){
                        if($this->ihale["Starting"] != false){
                        $this->yatirForm($g);
                        }
                }
        }
        public function ihaleForm($g){
                $f = new CustomForm(function(Player $g, $args){
                        if($args === null) return true;
                        if($this->ihale["Starting"] == false){
                                        if(is_numeric($args[1])){
                                                if(is_numeric($args[2])){
                                                if($args[2] > $args[1]){
                                                        if($args[1] > 5000 and $args[2] > 5000){
                                        $m = $args[3] * 60;
                                        $this->ihale = [
                                                "Starting" => true,
                                                "Min Price" => $args[1],
                                                "Auto Sell" => $args[2],
                                                "Teklif Veren" => null,
                                                "Name" => $g->getName(),
                                                "Item" => $g->getInventory()->getItemInHand()->getId(),
                                                "Item Count" => $g->getInventory()->getItemInHand()->getCount(),
                                                "Item Name" => $g->getInventory()->getItemInHand()->getName(),
                                                "Teklif Fiyati" => null
                                                ];
                                                $this->item = $g->getInventory()->getItemInHand()->getId();
                                                $this->count = $g->getInventory()->getItemInHand()->getCount();
                                                $this->name = $g->getInventory()->getItemInHand()->getName();
                                                $this->dataOne = $args[1];
                                                $this->dataTwo = $args[2];
                                                $this->timer[$g->getName()] = $m;
                                                $this->nick = $g->getName();
                                                $api = Main::getAPI();
                                                $this->g = $g;
                                        Main::getAPI()->getScheduler()->scheduleRepeatingTask($task = new IhaleTask($this, $g, $api), 20*1);
                                        $this->id[$g->getName()] = $task->getTaskId();
                                        Main::getAPI()->getServer()->broadcastMessage("§e------ §6İhale §e------\n§dİhaleyi Başlatan: §5 ".$g->getName()."\n§dEşya: §5 ".$g->getInventory()->getItemInHand()->getName()."\n§dBaşlangıç Fiyatı: §5: $args[1]\n§5$args[2] §dTL Gelirse Anında Satılacak!\n§dİhale Süresi: §5 $args[3] §dDakika\n§e------ §6İhale §e------");
                                        $name = $g->getInventory()->getItemInHand()->getName();
                                        $g->getInventory()->removeItem($g->getInventory()->getItemInHand());
                                }else{
                                        $g->sendMessage("§8[§c!§8] §c5000TLden yüksek değer girmelisin!");
                                }
                        }else{
                                $g->sendMessage("§8[§c!§8] §cAnında Satış Fiyatı Minimum Fiyatdan yüksek olmak zorunda!");
                        }
                                }else{
                                        $g->sendMessage("§8[§c!§8] §cGirdiğin değer sayısal olmalı!");
                                }
                        }else{
                                $g->sendMessage("§cDeğer Sayısal Olmalı!");
                        }
                        }else{
                                $g->sendMessage("§cBaşlamış İhale Var!");
                        }
                });
                $count = 0;
        $itemName = $g->getInventory()->getItemInHand()->getName();
        $count = $g->getInventory()->getItemInHand()->getCount();
        $f->setTitle("§8İhale");
        $f->addLabel("§6Ihelaya koyacağın Eşya:§a $itemName\n\n§6Elindeki Eşyanın Miktarı:§a $count");
        $f->addInput("§3Ihalenin Başlangıç Fiyatı:", "§3Örn; §b30000");
        $f->addInput("§3Kaç TL Teklif Gelirse Anında Satılsın?", "§3Örn; §b60000");
        $f->addSlider("§3Ihale Süresi Dakika", 1,10);
        $f->sendToPlayer($g);
        }
        public function yatirForm($g){
                $f = new CustomForm(function(Player $g, $args){
                        if($args === null) return true;
                        if($this->ihale["Starting"] == true){
                                if(EconomyAPI::getInstance()->myMoney($g) >= $args[1]){
                                if($args[1] >= $this->ihale["Auto Sell"]){
                                        if($g->getName() == $this->nick){
                                                $g->sendMessage("§cKendi İhalene para yatıramazsın!");
                                        }
                                        Main::getAPI()->getScheduler()->cancelTask($this->id[$this->g->getName()]);
                                        $this->ihale = [
                                                "Starting" => false
                                                ];
                                        Main::getAPI()->getServer()->broadcastMessage("§e> §6 §e".$g->getName()."§6adlı oyuncu ihaleyi $args[1] TL vererek bitirdi!");
                                        EconomyAPI::getInstance()->reduceMoney($this->g, $args[1]);
                                        EconomyAPI::getInstance()->addMoney($this->g, $args[1]);
                                       // $item = $this->ihale["Item"];
                                $itemcount = $this->ihale["Item Count"];
                                        $g->getInventory()->addItem(Item::get($this->item, 0, $itemcount));
                                }
                                if($args[1] >= $this->ihale["Min Price"]){
                                        if($this->ihale["Teklif Fiyati"] != null){
                                                if($args[1] > $this->ihale["Teklif Fiyati"]){
                                Main::getAPI()->getServer()->broadcastMessage("§e> §6 §e".$g->getName()." §6adlı oyuncu ihaleye $args[1] TL, yatırdı!");
                                EconomyAPI::getInstance()->reduceMoney($g, $args[1]);
                                $this->ihale = [
                                        "Starting" => true,
                                        "Teklif Veren" => $g->getName(),
                                        "Teklif Fiyati" => $args[1],
                                        "Item" => $this->item,
                                        "Item Count" => $this->count,
                                        "Item Name" => $this->name,
                                        "Min Price" => $this->dataOne,
                                        "Auto Sell" => $this->dataTwo
                                        ];
                                                }
                                        }else{
                                        Main::getAPI()->getServer()->broadcastMessage("§e> §6 §e".$g->getName()." §6adlı oyuncu ihaleye $args[1] TL yatırdı!");
                                        EconomyAPI::getInstance()->reduceMoney($g, $args[1]);
                                                                        $this->ihale = [
                                                                                "Starting" => true,
                                                                                "Teklif Veren" => $g->getName(),
                                                                                "Teklif Fiyati" => $args[1],
                                                                                "Item" => $this->item,
                                                                                "Item Count" => $this->count,
                                                                                "Item Name" => $this->name,
                                                                                 "Min Price" => $this->dataOne,
                                                                                "Auto Sell" => $this->dataTwo
                                                                                ];
                                        }
                                }
                                }
                                } 
                });
                $f->setTitle("§8İhale");
                $f->addLabel("§aİhale` Deki Eşya: §5 ".$this->ihale["Item Name"]."\n§dMinimum Fiyat :§5 ".$this->ihale["Min Price"]."\n§dŞu Kadar Yatırırsan Anında Sana Satılacak: §5".$this->ihale["Auto Sell"]."\n\n\n\n§3İhaleye Verilen Teklif: §5 ".$this->ihale["Teklif Fiyati"]."\n§3İhaleye Teklif Veren Oyuncu: §5 ".$this->ihale["Teklif Veren"]);
                $f->addInput("§cŞuanki Teklifden  yüksek olmalı ve  minimum fiyatdanda yüksek olmalı", "§3Örn; §b3000TL");
                $f->sendToPlayer($g);
        }
        
}
