<?php

namespace CompassTeleport;

use jojoe77777\FormAPI\FormAPI;
use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase implements Listener{

	public function onEnable() : void{
		$this->saveDefaultConfig();
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	public function onDisable() : void{

	}

	public function onInteract(PlayerInteractEvent $event) : void{
		if(($event->getAction() === PlayerInteractEvent::RIGHT_CLICK_AIR or $event->getAction() === PlayerInteractEvent::RIGHT_CLICK_BLOCK) and in_array($event->getPlayer()->getLevel()->getFolderName(), $this->getConfig()->get("worlds")) and $event->getItem()->getId() === Item::COMPASS){
			$form = $this->getFormAPI()->createSimpleForm(function(Player $player, $sel){
				if($sel === null) return;
				$button = $this->getConfig()->get("buttons");
				$player->teleport($this->getServer()->getLevelByName($button[$sel]["level"])->getSpawnLocation());
			});
			$form->setTitle($this->getConfig()->get("title"));
			$form->setContent($this->getConfig()->get("content"));
			foreach($this->getConfig()->get("buttons") as $button){
				$button["hasimage"] ? $form->addButton($button["text"], $button["imagetype"] === "url" ? 1 : 0, $button["path"]) : $form->addButton($button["text"]);
			}
			$form->sendToPlayer($event->getPlayer());
		}
	}

	public function onJoin(PlayerJoinEvent $event) : void{
		if(in_array($event->getPlayer()->getLevel()->getFolderName(), $this->getConfig()->get("worlds"))){
			$event->getPlayer()->getInventory()->setContents([Item::get(Item::COMPASS, 0, 1)->setCustomName($this->getConfig()->get("item-name"))]);
		}
	}

	public function onRespawn(PlayerRespawnEvent $event) : void{
		if(in_array($event->getPlayer()->getLevel()->getFolderName(), $this->getConfig()->get("worlds"))){
			$event->getPlayer()->getInventory()->setContents([Item::get(Item::COMPASS, 0, 1)->setCustomName($this->getConfig()->get("item-name"))]);
		}
	}

	public function onLevelChange(EntityLevelChangeEvent $event) : void{
		if(($entity = $event->getEntity()) instanceof Player){
			if(in_array($entity->getLevel()->getFolderName(), $this->getConfig()->get("worlds"))){
				$entity->getInventory()->setContents([Item::get(Item::COMPASS, 0, 1)->setCustomName($this->getConfig()->get("item-name"))]);
			}
		}
	}

	public function getFormAPI() : FormAPI{
		return $this->getServer()->getPluginManager()->getPlugin("FormAPI");
	}
}