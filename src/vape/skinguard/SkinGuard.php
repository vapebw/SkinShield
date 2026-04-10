<?php

declare(strict_types=1);

namespace vape\skinguard;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

final class SkinGuard extends PluginBase {

    private static self $instance;
    private SkinValidator $validator;

    public function onEnable(): void {
        self::$instance = $this;
        $this->saveDefaultConfig();

        $this->validator = new SkinValidator();
        $this->validator->init($this->getConfig()->getAll());

        $this->getServer()->getPluginManager()->registerEvents(new SkinListener($this), $this);

        $this->getLogger()->info(TextFormat::LIGHT_PURPLE . "skinguard enabled - by @sxvape");
    }

    public static function getInstance(): self {
        return self::$instance;
    }

    public function formatMessage(string $message): string {
        $prefix = (string) $this->getConfig()->get("prefix", "§l§dSKIN§fGUARD §r§7» ");
        return TextFormat::colorize(str_replace("{PREFIX}", $prefix, $message));
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
        if ($command->getName() === "skinguard") {
            if (!$sender->hasPermission("skinguard.admin")) {
                $sender->sendMessage(TextFormat::RED . "You dont have permission to use this Command.");
                return true;
            }

            if (isset($args[0]) && strtolower($args[0]) === "reload") {
                $this->reloadConfig();
                $this->validator->init($this->getConfig()->getAll());
                $sender->sendMessage($this->formatMessage("§aConfiguration reloaded succesfully"));
                return true;
            }

            if ($sender instanceof Player) {
                $skin = $sender->getSkin();
                $geoLen = strlen($skin->getGeometryData());
                $texLen = strlen($skin->getSkinData());

                $sender->sendMessage($this->formatMessage("§7Skin Status:"));
                $sender->sendMessage(" §f» §7Geometry: §e" . ($geoLen / 1000) . " KB §8(Limit: " . ($this->validator->getMaxGeometrySize() / 1000) . " KB)");
                $sender->sendMessage(" §f» §7Texture: §b" . ($texLen / 1000) . " KB §8(Limit: " . ($this->validator->getMaxTextureSize() / 1000) . " KB)");
                return true;
            }

            $sender->sendMessage(TextFormat::GRAY . "Usage: /skinguard reload");
        }
        return true;
    }
}
