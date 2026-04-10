<?php

declare(strict_types=1);

namespace vape\skinguard;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerChangeSkinEvent;
use pocketmine\player\Player;
use pocketmine\entity\Skin;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

final class SkinListener implements Listener {

    private SkinGuard $plugin;
    private SkinValidator $validator;

    public function __construct(SkinGuard $plugin) {
        $this->plugin = $plugin;
        $this->validator = SkinValidator::getInstance();
    }

    /**
     * early incertepcion in login
     * 
     * @priority NORMAL
     * @ignoreCancelled true
     */
    public function onPlayerLogin(PlayerLoginEvent $event): void {
        $player = $event->getPlayer();
        $skin = $player->getSkin();

        if (!$this->validator->isValid($skin, $player)) {
            $this->handleViolation($player, $event);
        }
    }

    /**
     * interception for ingame skin changes
     * 
     * @priority NORMAL
     * @ignoreCancelled true
     */
    public function onPlayerChangeSkin(PlayerChangeSkinEvent $event): void {
        $player = $event->getPlayer();
        $newSkin = $event->getNewSkin();

        if ($player->hasPermission("skinguard.bypass")) {
            return;
        }

        if (!$this->validator->isValid($newSkin, $player)) {
            $event->cancel();
            
            $msg = (string) $this->plugin->getConfig()->getNested("messages.skin-rejected");
            $player->sendMessage($this->plugin->formatMessage($msg));

            if ($this->plugin->getConfig()->get("log-violations", true)) {
                $this->plugin->getLogger()->warning("Blocked mid-game skin change for {$player->getName()} - Geometry size exceeded limits.");
            }
        }
    }

    /**
     * proccesess an skin violation
     * 
     * @param Player $player
     * @param PlayerLoginEvent|null $event Possible to cancel login
     */
    private function handleViolation(Player $player, ?PlayerLoginEvent $event = null): void {
        $action = $this->plugin->getConfig()->get("violation-action", "reset");

        if ($action === "kick") {
            $reason = (string) $this->plugin->getConfig()->getNested("messages.kick-reason");
            if ($event !== null) {
                $event->setKickMessage($reason);
                $event->cancel();
            } else {
                $player->kick($reason);
            }
            return;
        }

        $resetMsg = (string) $this->plugin->getConfig()->getNested("messages.skin-reset");
        $player->sendMessage($this->plugin->formatMessage($resetMsg));

        if ($this->plugin->getConfig()->get("log-violations", true)) {
            $this->plugin->getLogger()->info("Skin of player {$player->getName()} was reset automatically for security (Geometry Limit Exceeded).");
        }
    }
}
