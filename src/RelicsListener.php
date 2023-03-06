<?php

namespace DuoIncure\Relics;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\item\StringToItemParser;
use function array_map;
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
                case RelicFunctions::TYPE_COMMON:
                    $rFunctions->giveRelicReward($player, $item, RelicFunctions::TYPE_COMMON);
                    break;
                case RelicFunctions::TYPE_RARE:
                    $rFunctions->giveRelicReward($player, $item, RelicFunctions::TYPE_RARE);
                    break;
                case RelicFunctions::TYPE_EPIC:
                    $rFunctions->giveRelicReward($player, $item, RelicFunctions::TYPE_EPIC);
                    break;
                case RelicFunctions::TYPE_LEGENDARY:
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

        $configBlocks = (array)$config->get("block-ids", []);
        $blockNameArr = array_map('strtolower', $configBlocks);
        $blockAliases = StringToItemParser::getInstance()->lookupBlockAliases($ev->getBlock());
        $configWorlds = (array)$config->get("worlds", []);
        $levelName = $player->getWorld()->getDisplayName();
        $rFunctions = $this->plugin->getRelicFunctions();
        if($rFunctions !== null) {
            foreach ($blockNameArr as $blockName) {
                if (in_array($blockName, $blockAliases, true) && ($configWorlds[0] == "*" || in_array($levelName, $configWorlds, true))) {
                    $commonChance = (int)$config->getNested("common.chance", 10);
                    $rareChance = (int)$config->getNested("rare.chance", 5);
                    $epicChance = (int)$config->getNested("epic.chance", 3);
                    $legendaryChance = (int)$config->getNested("legendary.chance", 1);
                    //Credit to @SOF3 and @benda95280.
                    $chance = rand(1, 200);
                    if ($chance <= $commonChance) {
                        $rFunctions->giveRelic($player, RelicFunctions::TYPE_COMMON, 1);
                        return;
                    }
                    $chance -= $commonChance;
                    if ($chance <= $rareChance) {
                        $rFunctions->giveRelic($player, RelicFunctions::TYPE_RARE, 1);
                        return;
                    }
                    $chance -= $rareChance;
                    if ($chance <= $epicChance) {
                        $rFunctions->giveRelic($player, RelicFunctions::TYPE_EPIC, 1);
                        return;
                    }
                    $chance -= $epicChance;
                    if ($chance <= $legendaryChance) {
                        $rFunctions->giveRelic($player, RelicFunctions::TYPE_LEGENDARY, 1);
                        return;
                    }
                    return;
                }
            }
        }
    }
}