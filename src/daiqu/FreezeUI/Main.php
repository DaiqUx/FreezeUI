<?php

namespace daiqu\FreezeUI;

use daiqu\FreezeUI\form\Form;
use pocketmine\plugin\PluginBase;
use daiqu\FreezeUI\listener\EventListener;
use daiqu\FreezeUI\command\FreezeCommand;
use pocketmine\utils\TextFormat as TF;
use jojoe77777\FormAPI\Form as FormAPI;

class Main extends PluginBase
{

    public static $instance;

    private $form;

    public $freezeplayers = [];

    public $prefix = TF::BOLD . TF::DARK_GRAY ."[".TF::AQUA ."FREEZEUI".TF::DARK_GRAY ."] ". TF::RESET;

    public static function getInstance(): self
    {
        return self::$instance;
    }

    public function onEnable(): void
    {
        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
        $this->getServer()->getCommandMap()->register($this->getName(), new FreezeCommand());
        $this->form = new Form();

        if(!class_exists(FormAPI::class)){
            $this->getLogger()->error("Libraries FormAPI not found, Please download this plugin..");
            $this->getServer()->getPluginManager()->disablePlugin($this);
        }
    }

    public function onLoad(): void
    {
        self::$instance = $this;
    }

    public function getForm(): Form
    {
        return $this->form;
    }
}