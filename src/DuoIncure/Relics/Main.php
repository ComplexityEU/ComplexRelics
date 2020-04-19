<?php
declare(strict_types=1);

namespace DuoIncure\Relics;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use function mkdir;
use function file_exists;

class Main extends PluginBase{

	public const VERSION = 1;

	/** @var Config */
	private $cfg;
	/** @var RelicFunctions */
	private $relicFunctions;

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
		$this->relicFunctions = new RelicFunctions($this);
		$this->getServer()->getPluginManager()->registerEvents(new RelicsListener($this), $this);
	}

	/**
	 * @return RelicFunctions
	 */
	public function getRelicFunctions(){
		if(!$this->relicFunctions instanceof RelicFunctions){
			throw new \RuntimeException("relicFunctions was not an instanceof RelicFunctions");
		}
		return $this->relicFunctions;
	}

}
