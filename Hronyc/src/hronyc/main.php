<?php

namespace hronyc;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\Server;
use pocketmine\player\Player;
use pocketmine\player\GameMode;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\Config;
use pocketmine\event\player\{
	PlayerDeathEvent,
	PlayerJoinEvent,
	PlayerQuitEvent,
	PlayerChatEvent
};
use pocketmine\event\entity\{
	EntityDamageEvent,
	EntityDamageByEnttyEvent
};
use pocketmine\world\World;
use pocketmine\world\WorldManager;
use pocketmine\math\Vector3;
use pocketmine\item\Item;
use pocketmine\entity\Entity;
use pocketmine\entity\Effect;
use pocketmine\event\player\PlayerGameModeChangeEvent;

class main extends PluginBase implements Listener
{

	public $config;

	public function onEnable() : void{
		if(!is_dir($this->getDataFolder())){
			mkdir($this->getDataFolder());
		}
		$this->saveResource("config.yml");
		$this->config = new Config($this->getDataFolder() ."config.yml", Config::YAML);
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getLogger()->info("§6=|§fХроник§6|= §8Плагин успешно включен :)");
	}
	public function onJoin(PlayerJoinEvent $e){
		$p = $e->getPlayer();
		$n = $e->getPlayer()->getName();
		$e->setJoinMessage(str_replace("{nick}", $n, $this->config->get("public.join.msg")));
		$p->sendMessage(str_replace("{nick}", $n, $this->config->get("private.join.msg")));
	}
	public function onQuit(PlayerQuitEvent $e){
		$p = $e->getPlayer();
		$n = $e->getPlayer()->getName();
		$e->setQuitMessage(str_replace("{nick}", $n, $this->config->get("public.quit.msg")));
	}
    public function onPlayerGameModeChangeEvent(PlayerGameModeChangeEvent $event){
        $event->getPlayer()->getInventory()->clearAll();
    }
	public function onCommand(CommandSender $s, Command $cmd, string $label, array $arg) : bool{

		$n = $s->getName();

        if(! ($s instanceof Player)){
        	$s->sendMessage($this->config->get("console.error"));
        	return true;
        }
        switch($cmd->getName()){

        	case "gm":
        	switch(array_shift($arg)){

        		default:
				$s->sendMessage($this->config->get("gm.error"));
				break;

				case "survival":
				case "s":
				case "0":
				$s->sendMessage($this->config->get("gm.survival"));
				$s->setGamemode(GameMode::SURVIVAL());
				break;  

				case "creative":
				case "c":
				case "1":
				$s->sendMessage($this->config->get("gm.creative"));
				$s->setGamemode(GameMode::CREATIVE());
				break;

				case "adventure":
				case "a":
				case "2":
				$s->sendMessage($this->config->get("gm.adventure"));
				$s->setGamemode(GameMode::ADVENTURE());
				break;

				case "spectactor":
				case "v":
				case "3":
				$s->sendMessage($this->config->get("gm.spectactor"));
				$s->setGamemode(GameMode::SPECTATOR());
				break;
			}
			break;

        	case "player":
        	switch(array_shift($arg)){

        		default:
        		$s->sendMessage($this->config->get("hide.show.error"));
        		break;

				case "hide":
				case "h":
				$s->sendMessage($this->config->get("hide.player"));
				foreach($this->getServer()->getOnlinePlayers() as $online){
					$s->hidePlayer($online);
				}
				break; 

				case "show":
				case "s":
				$s->sendMessage($this->config->get("show.player"));
				foreach($this->getServer()->getOnlinePlayers() as $online){
					$s->showPlayer($online);
				}
				break;
        	}
        	break;

			case "times":
        	switch(array_shift($arg)){
        		default:
				$s->sendMessage($this->config->get("t.error"));
				break;

				case "day":
				case "d":
				$s->sendMessage($this->config->get("t.day"));
				$s->getWorld()->setTime(0);
				break; 

				case "night":
				case "n":
				$s->sendMessage($this->config->get("t.night"));
				$s->getWorld()->setTime(13000);
				break;
			} 
			break;

			case "fly":
			if($s->getAllowFlight() == true){
				$s->setAllowFlight(false);
				$s->sendMessage($this->config->get("fly.off"));
			}
			else{
				$s->setAllowFlight(true);
				$s->sendMessage($this->config->get("fly.on"));
			}
			break;

			case "heal":
			$s->setHealth($s->getMaxHealth());
			$s->sendMessage($this->config->get("heal.cmd"));
			$s->sendTitle($this->config->get("title.heal"));
			$s->setTitleDuration(20, 100, 20);
			break;
		}
        return true;
	}
}
?>