<?php
namespace DoubleAttackTracker;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\scheduler\PluginTask;

class MainClass extends PluginBase implements Listener{

	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getLogger()->info("§a二重攻撃対策Pluginを読み込みました§b By Rasuku");
		$this->getLogger()->info("§c二重攻撃対策Pluginを二次配布するのは禁止です");
	}

	public function Join(PlayerJoinEvent $event) {
		$player = $event->getPlayer();
		$name = $player->getName();
		$this->DamageCount[$name] = "false";
	}

  public function onEntityDamageByEntity(EntityDamageEvent $event){
    if($event instanceof EntityDamageByEntityEvent){
      $damager = $event->getDamager();
      $player = $event->getEntity();
      if($player instanceof Player and $damager instanceof Player){
        $task = new AttackTask($this, $damager);
        $this->AttackTask = $this->getServer()->getScheduler()->scheduleDelayedTask($task, 10)->getTaskId();
        if($this->DamageCount[$damager->getName()] == "true"){
          $event->setCancelled();
          $damager->sendPopUp("§cもう少しゆっくり攻撃しましょう");
        }
      }
    }
  }
}
class AttackTask extends PluginTask{
 public function __construct(PluginBase $owner, Player $damager) {
  parent::__construct($owner);
  $this->damager = $damager;
}

public function onRun($currentTick){
 $damager = $this->damager;
 $this->DamageCount[$damager->getName()] = "false";
}
}