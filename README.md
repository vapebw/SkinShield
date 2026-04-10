# SkinShield

SkinShield is an advanced, high-perfomance security plugin for PocketMine-MP 5 designed to protect your server from malicious, oversized, or unoptimized skin data. It prevents main-thread hangs and crashes by validating geometry and texture data in real-time.

## Key Features

* Geometry Guard: Blocks skins with oversized geometry strings (defaults to 512KB) before they can lag the server.
* Texture Validation: Ensures skin textures stay within safe limits (e.g., 64x64 or 128x128).
* Real-time Interception: Validates skins during login and mid-game changes.
* Customizable Actions: Choose between reseting the violator's skin to a standard Steve/Alex model or kicking them from the server.
* Perfomance First: Uses lightweight string-length validation to ensure zero impact on ticks.
* Multiversion Ready: Fully compatible with multiprotocol forks.

## Configuration

The plugin is fully customizable via config.yml:

```yaml
# Maximum size in bytes for geometry data (Default: 512KB)
max-geometry-size: 512000

# Maximum size in bytes for skin texture (Default: 70KB)
max-texture-size: 70000

# Action to take: "reset" (safe skin) or "kick"
violation-action: "reset"

messages:
  skin-rejected: "{PREFIX}§cYour skin has been rejected for exceeding stabilty limits."
```

## Commands and Permissions

| Command | Permission | Description |
|---------|------------|-------------|
| /skinshield reload | skinshield.admin | Reloads the configuration. |
| /skinshield status | skinshield.admin | Shows your current skin data stats. |

* skinshield.bypass: Allows players to use any skin size (not recomended for public use).

## Installation

1. Download the latest release.
2. Drop the SkinShield folder or .phar into your server's plugins/ directory.
3. Restart your server.
4. Customize the config.yml to your liking.

---
Developed with perfomance in mind for high-traffic servers.
