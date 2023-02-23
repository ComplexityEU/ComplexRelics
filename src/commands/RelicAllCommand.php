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
	}

	/**
	 * @param CommandSender $sender
	 * @param string $commandLabel
	 * @param array<string> $args
	 * @return void
	 */
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        /** @var Main $plugin */
        $plugin = $this->getOwningPlugin();

		$typesArray = ["common", "rare", "epic", "legendary"];
		if(!$this->testPermission($sender)) {
			$sender->sendMessage(TF::RED . "You do not have permission to use this command!");
			return;
		}
		if(!isset($args[0])) {
			$sender->sendMessage(TF::RED . "You need to provide some arguments!" . TF::EOL . "Usage: /relicall <type> <amount>");
			return;
		} elseif(!in_array(strtolower($args[0]), $typesArray, true)) {
			$sender->sendMessage(TF::RED . "You must enter a valid relic type!" . TF::EOL . "Valid Types: common, rare, epic, legendary");
			return;
		} elseif(!isset($args[1]) || !is_numeric($args[1])) {
			$sender->sendMessage(TF::RED . "You must provide a valid amount!");
			return;
		}

		$type = strtolower($args[0]);
		$amount = (int)$args[1];

		foreach ($plugin->getServer()->getOnlinePlayers() as $player) {
            $plugin->getRelicFunctions()?->giveRelic($player, $type, $amount);
		}
		$broadcastMessage = "&7[&l&6Relics&r&7] &6Everyone online just got given " . $amount . "x $type relics!";
        $plugin->getServer()->broadcastMessage(TF::colorize($broadcastMessage), $plugin->getServer()->getOnlinePlayers());
	}
}