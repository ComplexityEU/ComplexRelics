<?php

namespace DuoIncure\Relics\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginOwned;
use pocketmine\plugin\PluginOwnedTrait;
use pocketmine\utils\TextFormat as TF;
use DuoIncure\Relics\Main;
use function in_array;
use function is_numeric;
use function strtolower;

class RelicAllCommand extends Command implements PluginOwned {

    use PluginOwnedTrait;

	public function __construct(Main $plugin) {
		$this->owningPlugin = $plugin;
		parent::__construct("relicall", "Give everyone online relics!", "/relicall <type> <amount>");
		$this->setPermission("relics.command.relicall");
		$this->setDescription("Give everyone online relics!");
	}

	/**
	 * @param CommandSender $sender
	 * @param string $commandLabel
	 * @param array $args
	 * @return void
	 */
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		$typesArray = ["common", "rare", "epic", "legendary"];
		if(!$this->testPermission($sender)) {
			$sender->sendMessage(TF::RED . "You do not have permission to use this command!");
			return;
		}
		if(!isset($args[0])) {
			$sender->sendMessage(TF::RED . "You need to provide some arguments!" . TF::EOL . "Usage: /relicall <type> <amount>");
			return;
		} elseif(!in_array(strtolower($args[0]), $typesArray)) {
			$sender->sendMessage(TF::RED . "You must enter a valid relic type!" . TF::EOL . "Valid Types: common, rare, epic, legendary");
			return;
		} elseif(!isset($args[1]) || !is_numeric($args[1])) {
			$sender->sendMessage(TF::RED . "You must provide a valid amount!");
			return;
		}

		$type = strtolower($args[0]);
		$amount = (int)$args[1];

		foreach ($this->getOwningPlugin()->getServer()->getOnlinePlayers() as $player) {
			$this->getOwningPlugin()->getRelicFunctions()->giveRelic($player, $type, $amount);
		}
		$broadcastMessage = "&7[&l&6Relics&r&7] &6Everyone online just got given " . $amount . "x $type relics!";
		$this->getOwningPlugin()->getServer()->broadcastMessage(TF::colorize($broadcastMessage), $this->getOwningPlugin()->getServer()->getOnlinePlayers());
	}
}