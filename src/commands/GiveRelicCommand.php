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

class GiveRelicCommand extends Command implements PluginOwned {

    use PluginOwnedTrait;

	public function __construct(Main $plugin) {
		$this->owningPlugin = $plugin;
		parent::__construct("giverelic", "Give someone relics!", "/giverelic <name> <type> <amount>");
		$this->setPermission("relics.command.giverelic");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        /** @var Main $plugin */
        $plugin = $this->getOwningPlugin();

		$typesArray = ["common", "rare", "epic", "legendary"];
		if(!$this->testPermission($sender)){
			$sender->sendMessage(TF::RED . "You do not have permission to use this command!");
			return false;
		}
		if(!isset($args[0])) {
			$sender->sendMessage(TF::RED . "You must provide some arguments!" . TF::EOL . "Usage: /giverelic <name> <type> <amount>");
			return false;
		} elseif(($player = $plugin->getServer()->getPlayerExact($args[0])) === null) {
			$sender->sendMessage(TF::RED . "You must provide a valid player!");
			return false;
		} elseif(!isset($args[1]) || !in_array(strtolower($args[1]), $typesArray, true)) {
			$sender->sendMessage(TF::RED . "You must enter a valid relic type!" . TF::EOL . "Valid Types: common, rare, epic, legendary");
			return false;
		} elseif(!isset($args[2]) || !is_numeric($args[2])) {
			$sender->sendMessage(TF::RED . "You must provide a valid amount!");
			return false;
		}

		$type = strtolower($args[1]);
		$amount = (int)$args[2];

        $plugin->getRelicFunctions()?->giveRelic($player, $type, $amount);
		return true;
	}
}