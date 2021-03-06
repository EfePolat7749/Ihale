<?php

declare(strict_types=1);

namespace iEfePolat\Ihale;

use pocketmine\{
        Player,
        Server,
        plugin\PluginBase,
        command\CommandSender,
        event\Listener,
        command\Command
};
use iEfePolat\Ihale\{
        commands\IhaleCommand
};

class Main extends PluginBase implements Listener{
        
     private static $api;
             
             public function onLoad(){
                     $this->registerMultipleAcces();
             }
             
             public function registerMultipleAcces(){          
                     return static::$api = $this;
               }
             public static function getAPI(): ?self{
                 return self::$api;
               }
               
              public function onEnable(){
                  $this->getServer()->getCommandMap()->register("ihale", new IhaleCommand($this));
               }

}
