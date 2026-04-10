<?php

declare(strict_types=1);

namespace vape\skinshield;

use pocketmine\entity\Skin;
use pocketmine\player\Player;
use pocketmine\utils\SingletonTrait;
use pocketmine\Server;
use function strlen;

final class SkinValidator
{
    use SingletonTrait;

    private int $maxGeometrySize = 512000;
    private int $maxTextureSize = 70000;
    private bool $debug = false;

    public function __construct()
    {
        self::setInstance($this);
    }

    public function init(array $config): void
    {
        $this->maxGeometrySize = (int) ($config["max-geometry-size"] ?? 512000);
        $this->maxTextureSize = (int) ($config["max-texture-size"] ?? 70000);
        $this->debug = (bool) ($config["debug"] ?? false);
    }

    public function isValid(Skin $skin, ?Player $player = null): bool
    {
        $geometryData = $skin->getGeometryData();
        $textureData = $skin->getSkinData();

        $geoLen = strlen($geometryData);
        $texLen = strlen($textureData);

        if ($this->debug && $player !== null) {
            Server::getInstance()->getLogger()->debug("[SkinGuard] Player {$player->getName()} - Geo: {$geoLen} bytes, Texture: {$texLen} bytes");
        }

        if ($geoLen > $this->maxGeometrySize) {
            return false;
        }

        if ($texLen > $this->maxTextureSize) {
            return false;
        }

        return true;
    }

    public function getMaxGeometrySize(): int
    {
        return $this->maxGeometrySize;
    }

    public function getMaxTextureSize(): int
    {
        return $this->maxTextureSize;
    }
}
