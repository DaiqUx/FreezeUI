<?php

namespace daiqu\FreezeUI\listener;

use daiqu\FreezeUI\Main;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat as TF;
use pocketmine\world\Position;

class EventListener implements Listener
{

    public function onMove(PlayerMoveEvent $event)
    {
        $playerName = $event->getPlayer()->getName();
        if (in_array($playerName, Main::getInstance()->freezeplayers)) {
            $event->cancel();
        }
    }

    public function onHit(EntityDamageByEntityEvent $event)
    {
        $damager = $event->getDamager();
        $player = $event->getEntity();
        if ($player instanceof Player && $damager instanceof Player) {
            if (in_array($player->getName(), Main::getInstance()->freezeplayers)) {
                $event->cancel();
                $damager->sendMessage(Main::getInstance()->prefix . TF::YELLOW . "You can't hit players while frozen!");
            }
        }
    }

    public function onJoin(PlayerJoinEvent $event)
    {
        $playerName = $event->getPlayer()->getName();
        if (in_array($playerName, Main::getInstance()->freezeplayers)) {
            $this->check($event->getPlayer());
        }
    }

    public function check(Player $player)
    {
        $pos = $player->getPosition();
        $world = $player->getWorld();
        if (!$player->isOnGround()) {
            $newY = $world->getHighestBlockAt($pos->getFloorX(), $pos->getFloorZ());
            $player->teleport(new Position($pos->getFloorX(), $newY + 1, $pos->getFloorZ(), $world));
        }

        if ($player->isUnderwater()) {
            $newY = $player->getWorld()->getHighestBlockAt($pos->getFloorX(), $pos->getFloorZ());
            $player->teleport(new Position($pos->getFloorX(), $newY + 1, $pos->getFloorZ(), $world));
        }
    }
}