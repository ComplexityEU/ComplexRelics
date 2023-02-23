<?php

namespace DuoIncure\Relics;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\event\block\BlockBreakEvent;
use function in_array;
use function rand;

class RelicsListener implements Listener {

	private Main $plugin;

	/**
	 * RelicsListener constructor.
	 * @param Main $plugin
	 */
	public function __construct(Main $plugin) {
		$this->plugin = $plugin;
	}

	/**
	 * @param PlayerItemUseEvent $ev
	 */
	public function onInteract(PlayerItemUseEvent $ev): void {
		$player = $ev->getPlayer();
		$item = $ev->getItem();
		$nbt = $item->getNamedTag();
        $rFunctions = $this->plugin->getRelicFunctions();
		if($rFunctions !== null && $nbt->getTag(RelicFunctions::RELIC_TAG) !== null){
		    $relicType = $nbt->getTag(RelicFunctions::RELIC_TAG)->getValue();
		    switch($relicType){
		        case "common":
                    $rFunctions->giveRelicReward($player, $item, RelicFunctions::TYPE_COMMON);
		            break;
		        case "rare":
                    $rFunctions->giveRelicReward($player, $item, RelicFunctions::TYPE_RARE);
		            break;
				case "epic":
                    $rFunctions->giveRelicReward($player, $item, RelicFunctions::TYPE_EPIC);
				    break;
				case "legendary":
                    $rFunctions->giveRelicReward($player, $item, RelicFunctions::TYPE_LEGENDARY);
				    break;
		    }
		}
	}

	/**
	 * @param BlockBreakEvent $ev
	 * @priority MONITOR
	 * @ignoreCancelled true
	 */
	public function onBreak(BlockBreakEvent $ev): void {
		$player = $ev->getPlayer();
		$config = $this->plugin->getConfig();
		$blockID = $ev->getBlock()->getId();
		$configBlocks = (array)$config->get("block-ids", []);
		$configWorlds = (array)$config->get("worlds", []);
		$levelName = $player->getWorld()->getDisplayName();
        $rFunctions = $this->plugin->getRelicFunctions();
        if($rFunctions !== null) {
            if (in_array($blockID, $configBlocks, true) && ($configWorlds[0] == "*" or in_array($levelName, $configWorlds, true))) {
                $commonChance = (int)$config->getNested("common.chance", 10);
                $rareChance = (int)$config->getNested("rare.chance", 5);
                $epicChance = (int)$config->getNested("epic.chance", 3);
                $legendaryChance = (int)$config->getNested("legendary.chance", 1);
                //Credit to @SOF3 and @benda95280 for this chance system.
                $chance = rand(1, 200);
                if ($chance <= $commonChance) {
                    $rFunctions->giveRelic($player, RelicFunctions::TYPE_COMMON, 1);
                } else {
                    $chance -= $commonChance;
                    if ($chance <= $rareChance) {
                        $rFunctions->giveRelic($player, RelicFunctions::TYPE_RARE, 1);
                    } else {
                        $chance -= $rareChance;
                        if ($chance <= $epicChance) {
                            $rFunctions->giveRelic($player, RelicFunctions::TYPE_EPIC, 1);
                        } else {
                            $chance -= $epicChance;
                            if ($chance <= $legendaryChance) {
                                $rFunctions->giveRelic($player, RelicFunctions::TYPE_LEGENDARY, 1);
                            }
                        }
                    }
                }
            }
        }
	}
}