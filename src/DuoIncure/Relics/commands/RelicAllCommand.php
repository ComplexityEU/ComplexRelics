<?php

namespace DuoIncure\Relics\commands;

use pocketmine\command\PluginCommand;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat as TF;
use DuoIncure\Relics\Main;
use function in_array;
use function is_numeric;
use function strtolower;

class RelicAllCommand extends PluginCommand {

	/** @var Main */
	private $plugin;

	/**
	 * RelicAllCommand constructor.
	 * @param Main $plugin
	 */
	public function __construct(Main $plugin)
	{
		$this->plugin = $plugin;
		parent::__construct("relicall", $plugin);
		$this->setPermission("relics.command.relicall");
		$this->setDescription("Give everyone online relics!");
	}

	/**
	 * @param CommandSender $sender
	 * @param string $commandLabel
	 * @param array $args
	 * @return bool|mixed|void
	 */
	public function execute(CommandSender $sender, string $commandLabel, array $args)
	{
		$typesArray = ["common", "rare", "epic", "legendary"];
		if(!$sender->hasPermission("relics.command.relicall")){
			$sender->sendMessage(TF::RED . "You do not have permission to use this command!");
			return;
		}
		if(!isset($args[0])){
			$sender->sendMessage(TF::RED . "You need to provide some arguments!" . TF::EOL . "Usage: /relicall [type] [amount]");
			return;
		} elseif(!in_array(strtolower($args[0]), $typesArray)){
			$sender->sendMessage(TF::RED . "You must enter a valid relic type!" . TF::EOL . "Valid Types: common, rare, epic, legendary");
			return;
		} elseif(!isset($args[1]) || !is_numeric($args[1])){
			$sender->sendMessage(TF::RED . "You must provide a valid amount!");
			return;
		}

		$type = strtolower($args[0]);
		$amount = $args[1];

		foreach ($this->plugin->getServer()->getOnlinePlayers() as $player){
			$this->plugin->getRelicFunctions()->giveRelic($player, $type, $amount);
		}
		$broadcastMessage = "&7[&l&6Relics&r&7] &6Everyone online just got given " . $amount . "x $type relics!";
		$this->plugin->getServer()->broadcastMessage(TF::colorize($broadcastMessage), $this->plugin->getServer()->getOnlinePlayers());
	}
}