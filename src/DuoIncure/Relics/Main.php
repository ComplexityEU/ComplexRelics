<?php
declare(strict_types=1);

namespace DuoIncure\Relics;

use pocketmine\math\Vector3;
use pocketmine\nbt\tag\StringTag;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\Player;
use pocketmine\item\ItemFactory;
use pocketmine\item\Item;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\level\particle\HugeExplodeSeedParticle;
use pocketmine\level\particle\HappyVillagerParticle;
use pocketmine\utils\TextFormat as TF;
use function mkdir;
use function file_exists;
use function array_rand;
use function str_replace;

class Main extends PluginBase{

	public const VERSION = 1;
	public const RELIC_TAG = "isRelic";
	/** @var Config */
	private $cfg;
	private $relicID;

	public function onEnable()
	{
		if(!file_exists($this->getDataFolder())){
			@mkdir($this->getDataFolder());
		} else if(!file_exists($this->getDataFolder() . "config.yml")){
			$this->getLogger()->info("Config Not Found! Creating new config...");
			$this->saveDefaultConfig();
		}
		$this->cfg = new Config($this->getDataFolder() . "config.yml", Config::YAML);
		$this->cfg = $this->cfg->getAll();
		if($this->cfg["version"] < self::VERSION){
			$this->getLogger()->error("Config Version is outdated! Please delete your current config file!");
			$this->getServer()->getPluginManager()->disablePlugin($this);
		}
		$this->relicID = $this->cfg["relic-id"] ?? 399;
		$this->getServer()->getPluginManager()->registerEvents(new RelicsListener($this), $this);
	}

	/**
	 * @param Player $player
	 */
	public function giveCommonRelic(Player $player){
		$item = ItemFactory::get($this->relicID, 0, 1);
		$name = str_replace("&", "§", $this->cfg["common"]["name"]);
		$item->setCustomName($name);
		$lore = str_replace("&", "§", $this->cfg["common"]["lore"]);
		$item->setLore([$lore]);
		$nbt = $item->getNamedTag();
		$nbt->setTag(new StringTag(self::RELIC_TAG, "common"));

		$x = $player->getX();
		$y = $player->getY();
		$z = $player->getZ();
		$pos = new Vector3($x, $y, $z);

		$msgEnabled = $this->cfg["found-message-enabled"] ?? true;
		if($msgEnabled === true){
			$msgForm = $this->cfg["message-type"] ?? "title";
			switch($msgForm){
				case "title":
					$title = str_replace("&", "§", $this->cfg["common"]["title"]);
					$player->addTitle($title);
					break;
				case "tip":
					$tip = str_replace("&", "§", $this->cfg["common"]["tip"]);
					$player->sendTip($tip);
					break;
				case "message":
					$message = str_replace("&", "§", $this->cfg["common"]["message"]);
					$player->sendMessage($message);
					break;
			}
		}
		$particlesEnabled = $this->cfg["particles-enabled"] ?? true;
		if($particlesEnabled === true){
			$player->getLevel()->addParticle(new HappyVillagerParticle($pos), [$player]);
		}
		if($player->getInventory()->canAddItem($item)){
			$player->getInventory()->addItem($item);$x = $player->getX();
			$y = $player->getY();
			$z = $player->getZ();
			$pos = new Vector3($x, $y, $z);
		} else {
			$player->sendTip(TF::RED . "You found a relic but your inventory was full!");
			$player->getLevel()->dropItem($pos, $item);
		}
	}

	/**
	 * @param Player $player
	 * @param Item $relic
	 */
	public function giveCommonRelicReward(Player $player, Item $relic){
		$rewardArray = $this->cfg["common"]["commands"];
		$chosenReward = $rewardArray[array_rand($rewardArray)];
		$commandToUse = str_replace("{player}", $player->getName(), $chosenReward);
		$x = $player->getX();
		$y = $player->getY();
		$z = $player->getZ();
		$pos = new Vector3($x, $y, $z);
		$relic->setCount($relic->getCount() - 1);
		$player->getInventory()->setItem($player->getInventory()->getHeldItemIndex(), $relic);
		$this->getServer()->dispatchCommand(new ConsoleCommandSender(), $commandToUse);
		$particlesEnabled = $this->cfg["particles-enabled"] ?? true;
		if($particlesEnabled === true){
			$player->getLevel()->addParticle(new HugeExplodeSeedParticle($pos), [$player]);
		}
	}

	/**
	 * @param Player $player
	 */
	public function giveRareRelic(Player $player){
		$item = ItemFactory::get($this->relicID, 0, 1);
		$name = str_replace("&", "§", $this->cfg["rare"]["name"]);
		$item->setCustomName($name);
		$lore = str_replace("&", "§", $this->cfg["rare"]["lore"]);
		$item->setLore([$lore]);
		$nbt = $item->getNamedTag();
		$nbt->setTag(new StringTag(self::RELIC_TAG, "rare"));
		$title = str_replace("&", "§", $this->cfg["rare"]["title"]);
		$x = $player->getX();
		$y = $player->getY();
		$z = $player->getZ();
		$pos = new Vector3($x, $y, $z);

		$msgEnabled = $this->cfg["found-message-enabled"] ?? true;
		if($msgEnabled === true){
			$msgForm = $this->cfg["message-type"] ?? "title";
			switch($msgForm){
				case "title":
					$title = str_replace("&", "§", $this->cfg["rare"]["title"]);
					$player->addTitle($title);
					break;
				case "tip":
					$tip = str_replace("&", "§", $this->cfg["rare"]["tip"]);
					$player->sendTip($tip);
					break;
				case "message":
					$message = str_replace("&", "§", $this->cfg["rare"]["message"]);
					$player->sendMessage($message);
					break;
			}
		}
		$particlesEnabled = $this->cfg["particles-enabled"] ?? true;
		if($particlesEnabled === true){
			$player->getLevel()->addParticle(new HappyVillagerParticle($pos), [$player]);
		}
		if($player->getInventory()->canAddItem($item)){
			$player->getInventory()->addItem($item);$x = $player->getX();
			$y = $player->getY();
			$z = $player->getZ();
			$pos = new Vector3($x, $y, $z);
		} else {
			$player->sendTip(TF::RED . "You found a relic but your inventory was full!");
			$player->getLevel()->dropItem($pos, $item);
		}
	}

	/**
	 * @param Player $player
	 * @param Item $relic
	 */
	public function giveRareRelicReward(Player $player, Item $relic){
		$rewardArray = $this->cfg["rare"]["commands"];
		$chosenReward = $rewardArray[array_rand($rewardArray)];
		$commandToUse = str_replace("{player}", $player->getName(), $chosenReward);
		$x = $player->getX();
		$y = $player->getY();
		$z = $player->getZ();
		$pos = new Vector3($x, $y, $z);
		$relic->setCount($relic->getCount() - 1);
		$player->getInventory()->setItem($player->getInventory()->getHeldItemIndex(), $relic);
		$this->getServer()->dispatchCommand(new ConsoleCommandSender(), $commandToUse);
		$particlesEnabled = $this->cfg["particles-enabled"] ?? true;
		if($particlesEnabled === true){
			$player->getLevel()->addParticle(new HugeExplodeSeedParticle($pos), [$player]);
		}
	}

	/**
	 * @param Player $player
	 */
	public function giveEpicRelic(Player $player){
		$item = ItemFactory::get($this->relicID, 0, 1);
		$name = str_replace("&", "§", $this->cfg["epic"]["name"]);
		$item->setCustomName($name);
		$lore = str_replace("&", "§", $this->cfg["epic"]["lore"]);
		$item->setLore([$lore]);
		$nbt = $item->getNamedTag();
		$nbt->setTag(new StringTag(self::RELIC_TAG, "epic"));
		$title = str_replace("&", "§", $this->cfg["epic"]["title"]);
		$x = $player->getX();
		$y = $player->getY();
		$z = $player->getZ();
		$pos = new Vector3($x, $y, $z);

		$msgEnabled = $this->cfg["found-message-enabled"] ?? true;
		if($msgEnabled === true){
			$msgForm = $this->cfg["message-type"] ?? "title";
			switch($msgForm){
				case "title":
					$title = str_replace("&", "§", $this->cfg["epic"]["title"]);
					$player->addTitle($title);
					break;
				case "tip":
					$tip = str_replace("&", "§", $this->cfg["epic"]["tip"]);
					$player->sendTip($tip);
					break;
				case "message":
					$message = str_replace("&", "§", $this->cfg["epic"]["message"]);
					$player->sendMessage($message);
					break;
			}
		}
		$particlesEnabled = $this->cfg["particles-enabled"] ?? true;
		if($particlesEnabled === true){
			$player->getLevel()->addParticle(new HappyVillagerParticle($pos), [$player]);
		}
		if($player->getInventory()->canAddItem($item)){
			$player->getInventory()->addItem($item);$x = $player->getX();
			$y = $player->getY();
			$z = $player->getZ();
			$pos = new Vector3($x, $y, $z);
		} else {
			$player->sendTip(TF::RED . "You found a relic but your inventory was full!");
			$player->getLevel()->dropItem($pos, $item);
		}
	}

	/**
	 * @param Player $player
	 * @param Item $relic
	 */
	public function giveEpicRelicReward(Player $player, Item $relic){
		$rewardArray = $this->cfg["epic"]["commands"];
		$chosenReward = $rewardArray[array_rand($rewardArray)];
		$commandToUse = str_replace("{player}", $player->getName(), $chosenReward);
		$x = $player->getX();
		$y = $player->getY();
		$z = $player->getZ();
		$pos = new Vector3($x, $y, $z);
		$relic->setCount($relic->getCount() - 1);
		$player->getInventory()->setItem($player->getInventory()->getHeldItemIndex(), $relic);
		$this->getServer()->dispatchCommand(new ConsoleCommandSender(), $commandToUse);
		$particlesEnabled = $this->cfg["particles-enabled"] ?? true;
		if($particlesEnabled === true){
			$player->getLevel()->addParticle(new HugeExplodeSeedParticle($pos), [$player]);
		}
	}

	/**
	 * @param Player $player
	 */
	public function giveLegendaryRelic(Player $player){
		$item = ItemFactory::get($this->relicID, 0, 1);
		$name = str_replace("&", "§", $this->cfg["legendary"]["name"]);
		$item->setCustomName($name);
		$lore = str_replace("&", "§", $this->cfg["legendary"]["lore"]);
		$item->setLore([$lore]);
		$nbt = $item->getNamedTag();
		$nbt->setTag(new StringTag(self::RELIC_TAG, "legendary"));
		$title = str_replace("&", "§", $this->cfg["legendary"]["title"]);
		$x = $player->getX();
		$y = $player->getY();
		$z = $player->getZ();
		$pos = new Vector3($x, $y, $z);

		$msgEnabled = $this->cfg["found-message-enabled"] ?? true;
		if($msgEnabled === true){
			$msgForm = $this->cfg["message-type"] ?? "title";
			switch($msgForm){
				case "title":
					$title = str_replace("&", "§", $this->cfg["legendary"]["title"]);
					$player->addTitle($title);
					break;
				case "tip":
					$tip = str_replace("&", "§", $this->cfg["legendary"]["tip"]);
					$player->sendTip($tip);
					break;
				case "message":
					$message = str_replace("&", "§", $this->cfg["legendary"]["message"]);
					$player->sendMessage($message);
					break;
			}
		}
		$particlesEnabled = $this->cfg["particles-enabled"] ?? true;
		if($particlesEnabled === true){
			$player->getLevel()->addParticle(new HappyVillagerParticle($pos), [$player]);
		}
		if($player->getInventory()->canAddItem($item)){
			$player->getInventory()->addItem($item);
			$x = $player->getX();
			$y = $player->getY();
			$z = $player->getZ();
			$pos = new Vector3($x, $y, $z);
		} else {
			$player->sendTip(TF::RED . "You found a relic but your inventory was full!");
			$player->getLevel()->dropItem($pos, $item);
		}
	}

	/**
	 * @param Player $player
	 * @param Item $relic
	 */
	public function giveLegendaryRelicReward(Player $player, Item $relic){
		$rewardArray = $this->cfg["legendary"]["commands"];
		$chosenReward = $rewardArray[array_rand($rewardArray)];
		$commandToUse = str_replace("{player}", $player->getName(), $chosenReward);
		$x = $player->getX();
		$y = $player->getY();
		$z = $player->getZ();
		$pos = new Vector3($x, $y, $z);
		$relic->setCount($relic->getCount() - 1);
		$player->getInventory()->setItem($player->getInventory()->getHeldItemIndex(), $relic);
		$this->getServer()->dispatchCommand(new ConsoleCommandSender(), $commandToUse);
		$particlesEnabled = $this->cfg["particles-enabled"] ?? true;
		if($particlesEnabled === true){
			$player->getLevel()->addParticle(new HugeExplodeSeedParticle($pos), [$player]);
		}
	}
}
