<?php
 
namespace NamelessLeaf\BanMessage;

use pocketmine\plugin\PluginBase;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerPreLoginEvent;

use pocketmine\utils\Config;

class Main extends PluginBase implements Listener {
	
	public function onEnable(): void{
        $this->getLogger()->info("Enabled");
        $this->getServer()->getPluginManager()->registerEvents($this, $this);

        @mkdir($this->getDataFolder());
        $this->saveDefaultConfig();
        $this->getResource("config.yml");
    }
	
    public function onPreLogin(PlayerPreLoginEvent $event): void {
		$cfg = new Config($this->getDataFolder() . "config.yml", Config::YAML);
	    	
		$player = $event->getPlayer();
		$name = $player->getName();
	    
		if($cfg->get("custom-whitelist") === true){
		if(!$player->isWhitelisted($name)) {
         $whitelistedMessage = str_replace(["{line}", "&"], ["\n", "§"], $cfg->get("whitelist.reason"));
			$whitelistedMessage = str_replace(["{line}", "&", "{reason}"], ["\n", "§", $cfg->get("whitelist.reason")], $cfg->get("whitelist.message"));
          $event->setKickMessage($whitelistedMessage);
          $event->setCancelled(true);
		}
		if($cfg->get("custom-ban") === true){
        $banList = $player->getServer()->getNameBans();
        if($banList->isBanned(strtolower($player->getName()))){
          $banEntry = $banList->getEntries();
          $entry = $banEntry[strtolower($player->getName())];
          $reason = $entry->getReason();
          if($reason != null || $reason != ""){
            $bannedMessage = str_replace(["{line}", "&", "{reason}"], ["\n", "§", $reason], $cfg->get("banned.message")); 
	  } else {
            $bannedMessage = str_replace(["{line}", "&"], ["\n", "§"], $cfg->get("no.banned.reason.message"));
            $event->setKickMessage($bannedMessage);
            $event->setCancelled(true);
                }
	}
		}
		}
    }
}
