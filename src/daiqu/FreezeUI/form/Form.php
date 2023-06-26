<?php

namespace daiqu\FreezeUI\form;

use daiqu\FreezeUI\Main;
use jojoe77777\FormAPI\CustomForm;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat as TF;

class Form
{

    public function Menu(Player $player)
    {
        $form = new SimpleForm(function (Player $player, $data = null) {
            if ($data === null) {
                return true;
            }
            switch ($data) {
                case 0:
                    $this->FREEZE($player);
                    break;

                case 1:
                    if (!Main::getInstance()->freezeplayers) {
                        $player->sendMessage(Main::getInstance()->prefix . TF::YELLOW . "No players are frozen!");
                    } else {
                        $this->UNFREEZE($player);
                    }
                    break;
            }
        });
        $form->setTitle("FREEZEUI");
        $form->addButton("FREEZE", 0, "textures/ui/icon_winter");
        $form->addButton("UNFREEZE", 0, "textures/ui/redX1");
        $player->sendForm($form);
        return $form;
    }

    public function FREEZE(Player $player)
    {
        $form = new CustomForm(function (Player $player, $data = null) {
            if ($data === null) {
                return true;
            }
            $playerName = [];
            foreach (Server::getInstance()->getOnlinePlayers() as $players) {
                $playerName[] = $players->getName();
            }
            $target = Main::getInstance()->getServer()->getPlayerExact($playerName[$data[0]]);
            $targetName = $target->getName();
            if ($targetName == $player->getName()) {
                $player->sendMessage(Main::getInstance()->prefix . TF::YELLOW . "Unable to frozen yourself!");
                return true;
            }
            if (!in_array($targetName, Main::getInstance()->freezeplayers)) {
                array_push(Main::getInstance()->freezeplayers, $targetName);
                $player->sendMessage(Main::getInstance()->prefix . TF::GREEN . "You have frozen " . TF::YELLOW . $targetName);
                $target->sendMessage(Main::getInstance()->prefix . TF::GREEN . "You are frozen by " . TF::YELLOW . $player->getName());
            } else {
                $player->sendMessage(Main::getInstance()->prefix . TF::RED . $targetName . TF::YELLOW . " is already frozen!");
            }
        });
        $form->setTitle("FREEZE");
        $playerName = [];
        foreach (Server::getInstance()->getOnlinePlayers() as $players) {
            $playerName[] = $players->getName();
        }
        $form->addDropdown("Select Players:", $playerName);
        $player->sendForm($form);
        return $form;
    }

    public function UNFREEZE(Player $player)
    {
        $form = new CustomForm(function (Player $player, $data = null) {
            if ($data === null) {
                return true;
            }
            $playerName = [];
            foreach (Main::getInstance()->freezeplayers as $players) {
                $playerName[] = $players;
            }
            $target = Main::getInstance()->getServer()->getPlayerExact($playerName[$data[0]]);
            if(!$target){
                $player->sendMessage(Main::getInstance()->prefix . TF::RED . "Player not online!");
            }else {
                $targetName = $target->getName();
                array_splice(Main::getInstance()->freezeplayers, array_search($targetName, Main::getInstance()->freezeplayers), 1);
                $player->sendMessage(Main::getInstance()->prefix . TF::YELLOW . $targetName . TF::GREEN . " is no longer frozen!");
                $target->sendMessage(Main::getInstance()->prefix . TF::GREEN . "Unfrozen by " . TF::YELLOW . $player->getName());
            }
        });
        $form->setTitle("UNFREEZE");
        $form->addDropdown("Select Players:", Main::getInstance()->freezeplayers);
        $player->sendForm($form);
        return $form;
    }
}