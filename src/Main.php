<?php
declare(strict_types=1);

namespace DuoIncure\Relics;

use pocketmine\plugin\PluginBase;
use DuoIncure\Relics\commands\GiveRelicCommand;
use DuoIncure\Relics\commands\RelicAllCommand;
use function implode;
use function is_numeric;

class Main extends PluginBase {

    private RelicFunctions $relicFunctions;

    protected function onEnable(): void {
        $this->saveDefaultConfig();
        $this->relicFunctions = new RelicFunctions($this);
        $this->getServer()->getPluginManager()->registerEvents(new RelicsListener($this), $this);
        $this->getServer()->getCommandMap()->registerAll("relics", [
            new GiveRelicCommand($this),
            new RelicAllCommand($this)
        ]);

        $blocks = $this->getConfig()->get("block-ids", []);
        $numericBlocks = [];
        foreach($blocks as $blockIdentifier) {
            if(is_numeric($blockIdentifier)) {
                $numericBlocks[] = $blockIdentifier;
            }
        }
        if(count($numericBlocks) !== 0) {
            $numVals = implode("', '", $numericBlocks);
            $this->getLogger()->error("Config found invalid values '{$numVals}' in 'block-ids'. Please use block names instead of ids, such as 'stone', 'dirt', etc.");
        }
    }

    /**
     * @return RelicFunctions|null
     */
    public function getRelicFunctions(): ?RelicFunctions {
        return $this->relicFunctions ?? null;
    }
}
