<?php
declare(strict_types=1);

namespace DuoIncure\Relics;

use pocketmine\plugin\PluginBase;
use DuoIncure\Relics\commands\GiveRelicCommand;
use DuoIncure\Relics\commands\RelicAllCommand;

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
	}

	/**
	 * @return RelicFunctions|null
	 */
	public function getRelicFunctions(): ?RelicFunctions {
		return $this->relicFunctions ?? null;
	}
}
