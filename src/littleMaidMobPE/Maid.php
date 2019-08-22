<?php

namespace littleMaidMobPE;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\command\CommandExecutor;
use pocketmine\scheduler\Task;
use pocketmine\block\Air;
use pocketmine\block\Block;
use pocketmine\block\Fire;
use pocketmine\block\PressurePlate;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\entity\Entity;
use pocketmine\entity\Attribute;
use pocketmine\entity\Effect;
use pocketmine\entity\Zombie;
use pocketmine\entity\Skeleton;
use pocketmine\entity\Enderman;
use pocketmine\entity\Villager;
use pocketmine\entity\PigZombie;
use pocketmine\entity\Creeper;
use pocketmine\entity\Spider;
use pocketmine\entity\Witch;
use pocketmine\entity\IronGolem;
use pocketmine\entity\Blaze;
use pocketmine\entity\Slime;
use pocketmine\entity\WitherSkeleton;
use pocketmine\entity\Horse;
use pocketmine\entity\Donkey;
use pocketmine\entity\Mule;
use pocketmine\entity\SkeletonHorse;
use pocketmine\entity\ZombieHorse;
use pocketmine\entity\Stray;
use pocketmine\entity\Husk;
use pocketmine\entity\Mooshroom;
use pocketmine\entity\FallingSand;
use pocketmine\entity\Item as DroppedItem;
use pocketmine\entity\Human;
use pocketmine\entity\Skin;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\block\ItemFrameDropItemEvent;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\event\entity\EntityCombustByEntityEvent;
use pocketmine\event\entity\EntityDamageByBlockEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\event\entity\EntityRegainHealthEvent;
use pocketmine\event\entity\EntityShootBowEvent;
use pocketmine\event\entity\EntitySpawnEvent;
use pocketmine\event\entity\ProjectileLaunchEvent;
use pocketmine\event\inventory\CraftItemEvent;
use pocketmine\event\inventory\InventoryCloseEvent;
use pocketmine\event\inventory\InventoryPickupArrowEvent;
use pocketmine\event\inventory\InventoryPickupItemEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerTextPreSendEvent;
use pocketmine\event\player\PlayerAchievementAwardedEvent;
use pocketmine\event\player\PlayerAnimationEvent;
use pocketmine\event\player\PlayerBedEnterEvent;
use pocketmine\event\player\PlayerBedLeaveEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerGameModeChangeEvent;
use pocketmine\event\player\PlayerHungerChangeEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerKickEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\player\PlayerToggleFlightEvent;
use pocketmine\event\player\PlayerToggleSneakEvent;
use pocketmine\event\player\PlayerToggleSprintEvent;
use pocketmine\event\player\PlayerUseFishingRodEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\event\TextContainer;
use pocketmine\event\Timings;
use pocketmine\event\TranslationContainer;
use pocketmine\inventory\AnvilInventory;
use pocketmine\inventory\BaseTransaction;
use pocketmine\inventory\BigShapedRecipe;
use pocketmine\inventory\BigShapelessRecipe;
use pocketmine\inventory\CraftingManager;
use pocketmine\inventory\DropItemTransaction;
use pocketmine\inventory\EnchantInventory;
use pocketmine\inventory\FurnaceInventory;
use pocketmine\inventory\Inventory;
use pocketmine\inventory\InventoryHolder;
use pocketmine\inventory\PlayerInventory;
use pocketmine\inventory\ShapedRecipe;
use pocketmine\inventory\ShapelessRecipe;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\FoodSource;
use pocketmine\item\Item;
use pocketmine\item\Potion;
use pocketmine\level\ChunkLoader;
use pocketmine\level\format\FullChunk;
use pocketmine\level\Level;
use pocketmine\level\Location;
use pocketmine\level\Position;
use pocketmine\level\sound\LaunchSound;
use pocketmine\level\particle\FloatingTextParticle;
use pocketmine\level\WeakPosition;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector2;
use pocketmine\math\Vector3;
use pocketmine\metadata\MetadataValue;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\LongTag;
use pocketmine\nbt\tag\ShortTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\network\Network;
use pocketmine\network\mcpe\protocol\ActorEventPacket;
use pocketmine\network\mcpe\protocol\AddEntityPacket;
use pocketmine\network\mcpe\protocol\AddHangingEntityPacket;
use pocketmine\network\mcpe\protocol\AddItemEntityPacket;
use pocketmine\network\mcpe\protocol\AddItemPacket;
use pocketmine\network\mcpe\protocol\AddPaintingPacket;
use pocketmine\network\mcpe\protocol\AddPlayerPacket;
use pocketmine\network\mcpe\protocol\AdventureSettingsPacket;
use pocketmine\network\mcpe\protocol\AnimatePacket;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\network\mcpe\protocol\BatchPacket;
use pocketmine\network\mcpe\protocol\BlockPetdataPacket;
use pocketmine\network\mcpe\protocol\BlockEventPacket;
use pocketmine\network\mcpe\protocol\BlockPickRequestPacket;
use pocketmine\network\mcpe\protocol\BossEventPacket;
use pocketmine\network\mcpe\protocol\ChangeDimensionPacket;
use pocketmine\network\mcpe\protocol\ChunkRadiusUpdatedPacket;
use pocketmine\network\mcpe\protocol\ClientboundMapItemDataPacket;
use pocketmine\network\mcpe\protocol\ClientToServerHandshakePacket;
use pocketmine\network\mcpe\protocol\CommandBlockUpdatePacket;
use pocketmine\network\mcpe\protocol\CommandStepPacket;
use pocketmine\network\mcpe\protocol\ContainerClosePacket;
use pocketmine\network\mcpe\protocol\ContainerOpenPacket;
use pocketmine\network\mcpe\protocol\ContainerSetContentPacket;
use pocketmine\network\mcpe\protocol\ContainerSetDataPacket;
use pocketmine\network\mcpe\protocol\ContainerSetSlotPacket;
use pocketmine\network\mcpe\protocol\CraftingDataPacket;
use pocketmine\network\mcpe\protocol\CraftingEventPacket;
use pocketmine\network\mcpe\protocol\DataPacket;
use pocketmine\network\mcpe\protocol\DisconnectPacket;
use pocketmine\network\mcpe\protocol\DropItemPacket;
use pocketmine\network\mcpe\protocol\EventPacket;
use pocketmine\network\mcpe\protocol\ExplodePacket;
use pocketmine\network\mcpe\protocol\FullChunkDataPacket;
use pocketmine\network\mcpe\protocol\HurtArmorPacket;
use pocketmine\network\mcpe\protocol\InteractPacket;
use pocketmine\network\mcpe\protocol\InventoryActionPacket;
use pocketmine\network\mcpe\protocol\ItemFrameDropItemPacket;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\LoginPacket;
use pocketmine\network\mcpe\protocol\MapInfoRequestPacket;
use pocketmine\network\mcpe\protocol\MobArmorEquipmentPacket;
use pocketmine\network\mcpe\protocol\MobEquipmentPacket;
use pocketmine\network\mcpe\protocol\MoveEntityPacket;
use pocketmine\network\mcpe\protocol\MovePlayerPacket;
use pocketmine\network\mcpe\protocol\PlayerActionPacket;
use pocketmine\network\mcpe\protocol\EntityFallPacket;
use pocketmine\network\mcpe\protocol\PlayerInputPacket;
use pocketmine\network\mcpe\protocol\PlayerListPacket;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\network\mcpe\protocol\PlayStatusPacket;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\network\mcpe\protocol\RemoveBlockPacket;
use pocketmine\network\mcpe\protocol\RemoveActorPacket;
use pocketmine\network\mcpe\protocol\RemoveEntityPacket;
use pocketmine\network\mcpe\protocol\ReplaceItemInSlotPacket;
use pocketmine\network\mcpe\protocol\RequestChunkRadiusPacket;
use pocketmine\network\mcpe\protocol\ResourcePackChunkDataPacket;
use pocketmine\network\mcpe\protocol\ResourcePackChunkRequestPacket;
use pocketmine\network\mcpe\protocol\ResourcePackClientResponsePacket;
use pocketmine\network\mcpe\protocol\ResourcePackDataInfoPacket;
use pocketmine\network\mcpe\protocol\ResourcePacksInfoPacket;
use pocketmine\network\mcpe\protocol\RespawnPacket;
use pocketmine\network\mcpe\protocol\RiderJumpPacket;
use pocketmine\network\mcpe\protocol\ServerToClientHandshakePacket;
use pocketmine\network\mcpe\protocol\SetCommandsEnabledPacket;
use pocketmine\network\mcpe\protocol\SetDifficultyPacket;
use pocketmine\network\mcpe\protocol\SetPetdataPacket;
use pocketmine\network\mcpe\protocol\SetEntityLinkPacket;
use pocketmine\network\mcpe\protocol\SetEntityMotionPacket;
use pocketmine\network\mcpe\protocol\SetHealthPacket;
use pocketmine\network\mcpe\protocol\SetPlayerGameTypePacket;
use pocketmine\network\mcpe\protocol\SetSpawnPositionPacket;
use pocketmine\network\mcpe\protocol\SetTimePacket;
use pocketmine\network\mcpe\protocol\SetTitlePacket;
use pocketmine\network\mcpe\protocol\ShowCreditsPacket;
use pocketmine\network\mcpe\protocol\SpawnExperienceOrbPacket;
use pocketmine\network\mcpe\protocol\StartGamePacket;
use pocketmine\network\mcpe\protocol\StopSoundPacket;
use pocketmine\network\mcpe\protocol\TakeItemEntityPacket;
use pocketmine\network\mcpe\protocol\TextPacket;
use pocketmine\network\mcpe\protocol\TransferPacket;
use pocketmine\network\mcpe\protocol\UnknownPacket;
use pocketmine\network\mcpe\protocol\UpdateAttributesPacket;
use pocketmine\network\mcpe\protocol\UpdateBlockPacket;
use pocketmine\network\mcpe\protocol\UpdateTradePacket;
use pocketmine\network\mcpe\protocol\UseItemPacket;
use pocketmine\network\SourceInterface;
use pocketmine\permission\PermissibleBase;
use pocketmine\permission\PermissionAttachment;
use pocketmine\plugin\Plugin;
use pocketmine\tile\ItemFrame;
use pocketmine\tile\Sign;
use pocketmine\tile\Spawnable;
use pocketmine\tile\Tile;
use pocketmine\utils\Binary;
use pocketmine\utils\Config;
use pocketmine\utils\Color;
use pocketmine\utils\TextFormat;
use pocketmine\utils\UUID;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\level\particle\HeartParticle;

class Maid extends PluginBase implements Listener{

	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->Maidspeed = 2;
		if(!file_exists($this->getDataFolder())){
			mkdir($this->getDataFolder(), 0744, true);
		}
		$this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML, array());
		if (!$this->config->exists("SpawneggID")){
			$this->config->set("SpawneggID", 383);
			$this->config->save();
		}
		if (!$this->config->exists("SpawneggDAMAGE")){
			$this->config->set("SpawneggDAMAGE", 151);
			$this->config->save();
		}
		if (!$this->config->exists("Contract")){
			$this->config->set("Contract", 354);
			$this->config->save();
		}
		if (!$this->config->exists("Control")){
			$this->config->set("Control", 353);
			$this->config->save();
		}
		if (!$this->config->exists("Instruction")){
			$this->config->set("Instruction", 288);
			$this->config->save();
		}
		if (!$this->config->exists("MaidSkinid")){
			$this->config->set("MaidSkinid", "YzE4ZTY1YWEtN2IyMS00NjM3LTliNjMtOGFkNjM2MjJlZjAxX0N1c3RvbVNsaW0=");
			$this->config->save();
		}
		if (!$this->config->exists("MaidSkindata")){
			$this->config->set("MaidSkindata", "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");
			$this->config->save();
		}
		if (!$this->config->exists("MaidCapedata")){
			$this->config->set("MaidCapedata", "");
			$this->config->save();
		}
		if (!$this->config->exists("MaidGeometryname")){
			$this->config->set("MaidGeometryname", "Z2VvbWV0cnkuaHVtYW5vaWQuY3VzdG9tU2xpbQ==");
			$this->config->save();
		}
		if (!$this->config->exists("MaidGeometrydata")){
			$this->config->set("MaidGeometrydata", "eyJnZW9tZXRyeS5odW1hbm9pZCI6eyJib25lcyI6W3sibmFtZSI6ImJvZHkiLCJwaXZvdCI6WzAsMjQsMF0sImN1YmVzIjpbeyJvcmlnaW4iOlstNCwxMiwtMl0sInNpemUiOls4LDEyLDRdLCJ1diI6WzE2LDE2XX1dfSx7Im5hbWUiOiJ3YWlzdCIsIm5ldmVyUmVuZGVyIjp0cnVlLCJwaXZvdCI6WzAsMTIsMF19LHsibmFtZSI6ImhlYWQiLCJwaXZvdCI6WzAsMjQsMF0sImN1YmVzIjpbeyJvcmlnaW4iOlstNCwyNCwtNF0sInNpemUiOls4LDgsOF0sInV2IjpbMCwwXX1dfSx7Im5hbWUiOiJoYXQiLCJwaXZvdCI6WzAsMjQsMF0sImN1YmVzIjpbeyJvcmlnaW4iOlstNCwyNCwtNF0sInNpemUiOls4LDgsOF0sInV2IjpbMzIsMF0sImluZmxhdGUiOjAuNX1dLCJuZXZlclJlbmRlciI6dHJ1ZX0seyJuYW1lIjoicmlnaHRBcm0iLCJwaXZvdCI6Wy01LDIyLDBdLCJjdWJlcyI6W3sib3JpZ2luIjpbLTgsMTIsLTJdLCJzaXplIjpbNCwxMiw0XSwidXYiOls0MCwxNl19XX0seyJuYW1lIjoibGVmdEFybSIsInBpdm90IjpbNSwyMiwwXSwiY3ViZXMiOlt7Im9yaWdpbiI6WzQsMTIsLTJdLCJzaXplIjpbNCwxMiw0XSwidXYiOls0MCwxNl19XSwibWlycm9yIjp0cnVlfSx7Im5hbWUiOiJyaWdodExlZyIsInBpdm90IjpbLTEuOSwxMiwwXSwiY3ViZXMiOlt7Im9yaWdpbiI6Wy0zLjksMCwtMl0sInNpemUiOls0LDEyLDRdLCJ1diI6WzAsMTZdfV19LHsibmFtZSI6ImxlZnRMZWciLCJwaXZvdCI6WzEuOSwxMiwwXSwiY3ViZXMiOlt7Im9yaWdpbiI6Wy0wLjEsMCwtMl0sInNpemUiOls0LDEyLDRdLCJ1diI6WzAsMTZdfV0sIm1pcnJvciI6dHJ1ZX1dfSwiZ2VvbWV0cnkuY2FwZSI6eyJ0ZXh0dXJld2lkdGgiOjY0LCJ0ZXh0dXJlaGVpZ2h0IjozMiwiYm9uZXMiOlt7Im5hbWUiOiJjYXBlIiwicGl2b3QiOlswLDI0LC0zXSwiY3ViZXMiOlt7Im9yaWdpbiI6Wy01LDgsLTNdLCJzaXplIjpbMTAsMTYsMV0sInV2IjpbMCwwXX1dLCJtYXRlcmlhbCI6ImFscGhhIn1dfSwiZ2VvbWV0cnkuaHVtYW5vaWQuY3VzdG9tOmdlb21ldHJ5Lmh1bWFub2lkIjp7ImJvbmVzIjpbeyJuYW1lIjoiaGF0IiwibmV2ZXJSZW5kZXIiOmZhbHNlLCJtYXRlcmlhbCI6ImFscGhhIiwicGl2b3QiOlswLDI0LDBdfSx7Im5hbWUiOiJsZWZ0QXJtIiwicmVzZXQiOnRydWUsIm1pcnJvciI6ZmFsc2UsInBpdm90IjpbNSwyMiwwXSwiY3ViZXMiOlt7Im9yaWdpbiI6WzQsMTIsLTJdLCJzaXplIjpbNCwxMiw0XSwidXYiOlszMiw0OF19XX0seyJuYW1lIjoicmlnaHRBcm0iLCJyZXNldCI6dHJ1ZSwicGl2b3QiOlstNSwyMiwwXSwiY3ViZXMiOlt7Im9yaWdpbiI6Wy04LDEyLC0yXSwic2l6ZSI6WzQsMTIsNF0sInV2IjpbNDAsMTZdfV19LHsibmFtZSI6InJpZ2h0SXRlbSIsInBpdm90IjpbLTYsMTUsMV0sIm5ldmVyUmVuZGVyIjp0cnVlLCJwYXJlbnQiOiJyaWdodEFybSJ9LHsibmFtZSI6ImxlZnRTbGVldmUiLCJwaXZvdCI6WzUsMjIsMF0sImN1YmVzIjpbeyJvcmlnaW4iOls0LDEyLC0yXSwic2l6ZSI6WzQsMTIsNF0sInV2IjpbNDgsNDhdLCJpbmZsYXRlIjowLjI1fV0sIm1hdGVyaWFsIjoiYWxwaGEifSx7Im5hbWUiOiJyaWdodFNsZWV2ZSIsInBpdm90IjpbLTUsMjIsMF0sImN1YmVzIjpbeyJvcmlnaW4iOlstOCwxMiwtMl0sInNpemUiOls0LDEyLDRdLCJ1diI6WzQwLDMyXSwiaW5mbGF0ZSI6MC4yNX1dLCJtYXRlcmlhbCI6ImFscGhhIn0seyJuYW1lIjoibGVmdExlZyIsInJlc2V0Ijp0cnVlLCJtaXJyb3IiOmZhbHNlLCJwaXZvdCI6WzEuOSwxMiwwXSwiY3ViZXMiOlt7Im9yaWdpbiI6Wy0wLjEsMCwtMl0sInNpemUiOls0LDEyLDRdLCJ1diI6WzE2LDQ4XX1dfSx7Im5hbWUiOiJsZWZ0UGFudHMiLCJwaXZvdCI6WzEuOSwxMiwwXSwiY3ViZXMiOlt7Im9yaWdpbiI6Wy0wLjEsMCwtMl0sInNpemUiOls0LDEyLDRdLCJ1diI6WzAsNDhdLCJpbmZsYXRlIjowLjI1fV0sInBvcyI6WzEuOSwxMiwwXSwibWF0ZXJpYWwiOiJhbHBoYSJ9LHsibmFtZSI6InJpZ2h0UGFudHMiLCJwaXZvdCI6Wy0xLjksMTIsMF0sImN1YmVzIjpbeyJvcmlnaW4iOlstMy45LDAsLTJdLCJzaXplIjpbNCwxMiw0XSwidXYiOlswLDMyXSwiaW5mbGF0ZSI6MC4yNX1dLCJwb3MiOlstMS45LDEyLDBdLCJtYXRlcmlhbCI6ImFscGhhIn0seyJuYW1lIjoiamFja2V0IiwicGl2b3QiOlswLDI0LDBdLCJjdWJlcyI6W3sib3JpZ2luIjpbLTQsMTIsLTJdLCJzaXplIjpbOCwxMiw0XSwidXYiOlsxNiwzMl0sImluZmxhdGUiOjAuMjV9XSwibWF0ZXJpYWwiOiJhbHBoYSJ9XX0sImdlb21ldHJ5Lmh1bWFub2lkLmN1c3RvbVNsaW06Z2VvbWV0cnkuaHVtYW5vaWQiOnsiYm9uZXMiOlt7Im5hbWUiOiJoYXQiLCJuZXZlclJlbmRlciI6ZmFsc2UsIm1hdGVyaWFsIjoiYWxwaGEifSx7Im5hbWUiOiJsZWZ0QXJtIiwicmVzZXQiOnRydWUsIm1pcnJvciI6ZmFsc2UsInBpdm90IjpbNSwyMS41LDBdLCJjdWJlcyI6W3sib3JpZ2luIjpbNCwxMS41LC0yXSwic2l6ZSI6WzMsMTIsNF0sInV2IjpbMzIsNDhdfV19LHsibmFtZSI6InJpZ2h0QXJtIiwicmVzZXQiOnRydWUsInBpdm90IjpbLTUsMjEuNSwwXSwiY3ViZXMiOlt7Im9yaWdpbiI6Wy03LDExLjUsLTJdLCJzaXplIjpbMywxMiw0XSwidXYiOls0MCwxNl19XX0seyJwaXZvdCI6Wy02LDE0LjUsMV0sIm5ldmVyUmVuZGVyIjp0cnVlLCJuYW1lIjoicmlnaHRJdGVtIiwicGFyZW50IjoicmlnaHRBcm0ifSx7Im5hbWUiOiJsZWZ0U2xlZXZlIiwicGl2b3QiOls1LDIxLjUsMF0sImN1YmVzIjpbeyJvcmlnaW4iOls0LDExLjUsLTJdLCJzaXplIjpbMywxMiw0XSwidXYiOls0OCw0OF0sImluZmxhdGUiOjAuMjV9XSwibWF0ZXJpYWwiOiJhbHBoYSJ9LHsibmFtZSI6InJpZ2h0U2xlZXZlIiwicGl2b3QiOlstNSwyMS41LDBdLCJjdWJlcyI6W3sib3JpZ2luIjpbLTcsMTEuNSwtMl0sInNpemUiOlszLDEyLDRdLCJ1diI6WzQwLDMyXSwiaW5mbGF0ZSI6MC4yNX1dLCJtYXRlcmlhbCI6ImFscGhhIn0seyJuYW1lIjoibGVmdExlZyIsInJlc2V0Ijp0cnVlLCJtaXJyb3IiOmZhbHNlLCJwaXZvdCI6WzEuOSwxMiwwXSwiY3ViZXMiOlt7Im9yaWdpbiI6Wy0wLjEsMCwtMl0sInNpemUiOls0LDEyLDRdLCJ1diI6WzE2LDQ4XX1dfSx7Im5hbWUiOiJsZWZ0UGFudHMiLCJwaXZvdCI6WzEuOSwxMiwwXSwiY3ViZXMiOlt7Im9yaWdpbiI6Wy0wLjEsMCwtMl0sInNpemUiOls0LDEyLDRdLCJ1diI6WzAsNDhdLCJpbmZsYXRlIjowLjI1fV0sIm1hdGVyaWFsIjoiYWxwaGEifSx7Im5hbWUiOiJyaWdodFBhbnRzIiwicGl2b3QiOlstMS45LDEyLDBdLCJjdWJlcyI6W3sib3JpZ2luIjpbLTMuOSwwLC0yXSwic2l6ZSI6WzQsMTIsNF0sInV2IjpbMCwzMl0sImluZmxhdGUiOjAuMjV9XSwibWF0ZXJpYWwiOiJhbHBoYSJ9LHsibmFtZSI6ImphY2tldCIsInBpdm90IjpbMCwyNCwwXSwiY3ViZXMiOlt7Im9yaWdpbiI6Wy00LDEyLC0yXSwic2l6ZSI6WzgsMTIsNF0sInV2IjpbMTYsMzJdLCJpbmZsYXRlIjowLjI1fV0sIm1hdGVyaWFsIjoiYWxwaGEifV19fQ==");
			$this->config->save();
		}
		$id = $this->config->get("SpawneggID");
		$damage = $this->config->get("SpawneggDAMAGE");
		$spawnegg = Item::get($id, $damage, 1)->setCustomName("リトルメイドを出現させる");
		Item::addCreativeItem($spawnegg);
	}

	public function onReceive(DataPacketReceiveEvent $event){
		$pk = $event->getPacket();
		$p = $event->getPlayer();
		$level = $p->getLevel();
		$name = $p->getName();
		if($pk instanceof InventoryTransactionPacket){
			if(InventoryTransactionPacket::TYPE_USE_ITEM_ON_ENTITY){
				$eid = $pk->trData->entityRuntimeId ?? null;
				if($eid === null){
					return false;
				}
				if(isset($this->Maiddata[$eid])){
					$item = $p->getInventory()->getItemInHand();
					$contract = $this->config->get("Contract");
					$control = $this->config->get("Control");
					$instruction = $this->config->get("Instruction");
					if($item->getid() === $contract){
						if($this->Maiddata[$eid]["playername"] === ""){
							$this->Maiddata[$eid]["playername"] = $name;
							$this->Maiddata[$eid]["target"] = $p->getid();
							$this->Maiddata[$eid]["mode"] = 1;
							$this->Maiddata[$eid]["time"] = 1200 * 20;
							$item->setCount($item->getCount() - 1);
							$p->getInventory()->setIteminhand($item);
							$particle = new HeartParticle(new Vector3($this->Maiddata[$eid]["x"],$this->Maiddata[$eid]["y"] + 1.5, $this->Maiddata[$eid]["z"]));
							$level->addParticle($particle);
						}
					}elseif($item->getid() === $control and $this->Maiddata[$eid]["playername"] === $name){
						$item->setCount($item->getCount() - 1);
						$p->getInventory()->setIteminhand($item);
						$time = $this->Maiddata[$eid]["time"];
						$this->Maiddata[$eid]["time"] += 1200 * 20;
						if($time >= 1200 * 7 * 20){
							$this->Maiddata[$eid]["time"] = 1200 * 7 * 20;
						}
						if($this->Maiddata[$eid]["speed"] > 0){
							$this->Maiddata[$eid]["speed"] = 0;
							$this->Maiddata[$eid]["target"] = $p->getid();
						}else{
							$this->Maiddata[$eid]["speed"] = $this->Maidspeed;
						}
						if($this->Maiddata[$eid]["maxhp"] > $this->Maiddata[$eid]["hp"]){
							$this->Maiddata[$eid]["hp"] = $this->Maiddata[$eid]["hp"] + 1;
						}
					}elseif($item->getId() === $instruction and $this->Maiddata[$eid]["playername"] === $name){
						$item->setCount($item->getCount() - 1);
						$p->getInventory()->setIteminhand($item);
						$mode = $this->Maiddata[$eid]["mode"];
						if($mode === 0){
							$this->Maiddata[$eid]["mode"] = 1;
						}else{
							$this->Maiddata[$eid]["mode"] = 0;
							$this->Maiddata[$eid]["speed"] = $this->Maidspeed;
						}
					}elseif($item->getId() === 421 and $this->Maiddata[$eid]["playername"] === $name){
						if($item->getCustomName() !== ""){
							$this->Maiddata[$eid]["name"] = $item->getCustomName();
							$item->setCount($item->getCount() - 1);
							$p->getInventory()->setIteminhand($item);
							$this->Redisplay($eid);
						}
					}else{
						$damageTable = [
							Item::WOODEN_SWORD => 4,
							Item::GOLD_SWORD => 4,
							Item::STONE_SWORD => 5,
							Item::IRON_SWORD => 6,
							Item::DIAMOND_SWORD => 7,

							Item::WOODEN_AXE => 3,
							Item::GOLD_AXE => 3,
							Item::STONE_AXE => 3,
							Item::IRON_AXE => 5,
							Item::DIAMOND_AXE => 6,

							Item::WOODEN_PICKAXE => 2,
							Item::GOLD_PICKAXE => 2,
							Item::STONE_PICKAXE => 3,
							Item::IRON_PICKAXE => 4,
							Item::DIAMOND_PICKAXE => 5,

							Item::WOODEN_SHOVEL => 1,
							Item::GOLD_SHOVEL => 1,
							Item::STONE_SHOVEL => 2,
							Item::IRON_SHOVEL => 3,
							Item::DIAMOND_SHOVEL => 4,
						];
						if($this->Maiddata[$eid]["playername"] === $name){
							$maiditem = $this->Maiddata[$eid]["item"];
							$p->getInventory()->addItem($maiditem);
							$item->setCount(1);
							$p->getInventory()->removeItem($item);
							$this->Maiddata[$eid]["item"] = $item;
							$this->Maiddata[$eid]["atk"] = $damageTable[$item->getId()] ?? 1;
							$pk = new MobEquipmentPacket();
							$pk->entityRuntimeId = $eid;
							$pk->item = $item;
							$pk->inventorySlot = 0;
							$pk->hotbarSlot = 0;
							foreach($this->getServer()->getOnlinePlayers() as $players){
								$players->dataPacket($pk);
							}
						}else{
							$damage = $damageTable[$item->getId()] ?? 1;
							if($this->Maiddata[$eid]["hp"] > 0){
								$this->MaidDamage($p, $eid, $damage);
							}
						}
					}
				}
			}
		}
	}

	public function onJoin(PlayerJoinEvent $event){
		$player = $event->getPlayer();
		$name = $player->getName();
		if(isset($this->eid)){
			foreach($this->eid as $eid){
				$maidname = $this->Maiddata[$eid]["name"];
				$x = $this->Maiddata[$eid]["x"];
				$y = $this->Maiddata[$eid]["y"];
				$z = $this->Maiddata[$eid]["z"];
				$yaw = $this->Maiddata[$eid]["yaw"];
				$pitch = $this->Maiddata[$eid]["pitch"];
				$item = $this->Maiddata[$eid]["item"];
				$pk = new AddPlayerPacket();
				$pk->entityRuntimeId = $eid;
				$pk->uuid = UUID::fromRandom();
				$pk->username = $maidname;
				$pk->position = new Vector3($x, $y, $z);
			       	$pk->yaw = $yaw;
			       	$pk->pitch = $pitch;
		        	$pk->item = $item;
				@$flags |= 0 << Entity::DATA_FLAG_INVISIBLE;
				@$flags |= 1 << Entity::DATA_FLAG_CAN_SHOW_NAMETAG;
				@$flags |= 1 << Entity::DATA_FLAG_ALWAYS_SHOW_NAMETAG;
				@$flags |= 0 << Entity::DATA_FLAG_IMMOBILE;
			       	$pk->metadata = [
					Entity::DATA_FLAGS => [Entity::DATA_TYPE_LONG, $flags],
						Entity::DATA_NAMETAG => [Entity::DATA_TYPE_STRING, $maidname],
					  	Entity::DATA_FLAG_NO_AI => [Entity::DATA_TYPE_BYTE, 1],
					  	Entity::DATA_LEAD_HOLDER_EID => [Entity::DATA_TYPE_LONG, -1],
						Entity::DATA_SCALE => [Entity::DATA_TYPE_FLOAT, $this->Maiddata[$eid]["size"]],//大きさ
					  	];
			 	$skin = $this->Maiddata[$eid]["skin"];
				$this->getServer()->updatePlayerListData($pk->uuid, $pk->entityRuntimeId, $maidname, $skin, "", $this->getServer()->getOnlinePlayers());
				$player->dataPacket($pk);
				$pk2 = new MobEquipmentPacket();
				$pk2->entityRuntimeId = $eid;
				$pk2->item = $item;
				$pk2->inventorySlot = 0;
				$pk2->hotbarSlot = 0;
				$player->dataPacket($pk2);
				$playername = $this->Maiddata[$eid]["playername"];
				if($name === $playername){
					$this->Maiddata[$eid]["speed"] = $this->Maidspeed;
				}
			}
		}
	}

	public function onTouch(PlayerInteractEvent $event){
		$player = $event->getPlayer();
		$name = $player->getName();
		$block = $event->getBlock();
		$pitem = $player->getInventory()->getIteminhand();
		if($pitem->getId() === 383 and $pitem->getDamage() === 151){
			$pitem->setCount($pitem->getCount() - 1);
			$player->getInventory()->setIteminhand($pitem);
			$eid = mt_rand(100000, 10000000);
			$item = Item::get(0, 0, 1);
			$size = 0.65;
			$pk = new AddPlayerPacket();
			$pk->entityRuntimeId = $eid;
			$pk->username = "";
			$pk->uuid = UUID::fromRandom();
			$pk->position = new Vector3($block->x, $block->y + 1, $block->z);
			$pk->yaw = 0;
			$pk->pitch = 0;
			$pk->item = $item;
			@$flags |= 0 << Entity::DATA_FLAG_INVISIBLE;
			@$flags |= 1 << Entity::DATA_FLAG_CAN_SHOW_NAMETAG;
			@$flags |= 1 << Entity::DATA_FLAG_ALWAYS_SHOW_NAMETAG;
			@$flags |= 0 << Entity::DATA_FLAG_IMMOBILE;
			$pk->metadata = [
				Entity::DATA_FLAGS => [Entity::DATA_TYPE_LONG, $flags],
				Entity::DATA_NAMETAG => [Entity::DATA_TYPE_STRING, ""],
				Entity::DATA_LEAD_HOLDER_EID => [Entity::DATA_TYPE_LONG, -1],
	 			Entity::DATA_SCALE => [Entity::DATA_TYPE_FLOAT, $size],//大きさ
				  	];
			$player->dataPacket($pk);
			$geometryJsonEncoded = base64_decode($this->config->get("MaidGeometrydata"));
			if($geometryJsonEncoded !== ""){
				$geometryJsonEncoded = \json_encode(\json_decode($geometryJsonEncoded));
			}
		 	$skin = new Skin(base64_decode($this->config->get("MaidSkinid")), base64_decode($this->config->get("MaidSkindata")), base64_decode($this->config->get("MaidCapedata")), base64_decode($this->config->get("MaidGeometryname")), $geometryJsonEncoded);
			$this->getServer()->updatePlayerListData($pk->uuid, $pk->entityRuntimeId, "", $skin, "", $this->getServer()->getOnlinePlayers());
			$pk2 = new MobEquipmentPacket();
			$pk2->entityRuntimeId = $eid;
			$pk2->item = $item;
			$pk2->inventorySlot = 0;
			$pk2->hotbarSlot = 0;
			foreach($this->getServer()->getOnlinePlayers() as $player){
				$player->dataPacket($pk);
				$player->dataPacket($pk2);
			}
			$this->Maiddata[$eid]["name"] = "";
			$this->Maiddata[$eid]["playername"] = "";
			$this->Maiddata[$eid]["item"] = $item;
			$this->Maiddata[$eid]["itemdamage"] = 0;
			$this->Maiddata[$eid]["itemcount"] = 1;
			$this->Maiddata[$eid]["maxhp"] = 20;
			$this->Maiddata[$eid]["hp"] = $this->Maiddata[$eid]["maxhp"];
			$this->Maiddata[$eid]["atk"] = 1;
			$this->Maiddata[$eid]["def"] = 0;
			$this->Maiddata[$eid]["atkrange"] = 2; //攻撃範囲(リーチ)
			$this->Maiddata[$eid]["atktime"] = 0;
			$this->Maiddata[$eid]["time"] = 0; //雇用期間
			$this->Maiddata[$eid]["reatk"] = 20; //再攻撃までの時間
			$this->Maiddata[$eid]["speed"] = $this->Maidspeed;
			$this->Maiddata[$eid]["level"] = $player->getLevel();
			$this->Maiddata[$eid]["x"] = $block->x;
			$this->Maiddata[$eid]["y"] = $block->y + 1;
			$this->Maiddata[$eid]["z"] = $block->z;
			$this->Maiddata[$eid]["yaw"] = 0;
			$this->Maiddata[$eid]["pitch"] = 0;
			$this->Maiddata[$eid]["move"] = 0;
			$this->Maiddata[$eid]["uuid"] = $pk->uuid;
			$this->Maiddata[$eid]["skin"] = $skin;
			$this->Maiddata[$eid]["size"] = $size;
			$this->Maiddata[$eid]["playerdistance"] = 3;
			$this->Maiddata[$eid]["enemydistance"] = $this->Maiddata[$eid]["atkrange"];
			$this->Maiddata[$eid]["searchdistance"] = 20;
			$this->Maiddata[$eid]["walkcount"] = 0;
			$this->Maiddata[$eid]["randomwalk"] = mt_rand(1,6);
			$this->Maiddata[$eid]["mode"] = 0;
			$this->Maiddata[$eid]["target"] = "";
			$x = $this->Maiddata[$eid]["x"];
			$y = $this->Maiddata[$eid]["y"];
			$z = $this->Maiddata[$eid]["z"];
			$yaw = $this->Maiddata[$eid]["yaw"];
			$pitch = $this->Maiddata[$eid]["pitch"];
			$target = $this->Maiddata[$eid]["target"];
			$this->eid[$eid] = $eid;
			$this->getScheduler()->scheduleDelayedTask(new MaidMove($this, $eid, $x, $y, $z, $yaw, $pitch, $target), 1);
		}
	}

	public function onEntityDamage(EntityDamageEvent $event){
		if ($event instanceof EntityDamageByEntityEvent) {
			$player = $event->getDamager();
			$entity = $event->getEntity();
			if ($player instanceof Player){
				$name = $player->getName();
				if(isset($this->eid)){
					foreach($this->eid as $eid){
						$maidplayername = $this->Maiddata[$eid]["playername"];
						$maidspeed = $this->Maiddata[$eid]["speed"];
						if($maidplayername === $name and $maidspeed !== 0){
							$this->Maiddata[$eid]["target"] = $entity->getid();
						}
					}
				}
			}
		}
	}

	public function MaidATK($eid,$target){ //メイドがダメージを与える
		$def = 0;
		$atk = $this->Maiddata[$eid]["atk"];
		$atkrange = $this->Maiddata[$eid]["atkrange"];
		$x = $this->Maiddata[$eid]["x"];
		$y = $this->Maiddata[$eid]["y"];
		$z = $this->Maiddata[$eid]["z"];
		$pos = new Vector3($x, $y, $z);
		$damage = $atk * (1 - ($def*0.04));
		if($damage > 1){
			$d = $damage;
		}else{
			$d = 1;
		}
		$ev = new EntityDamageEvent($target, EntityDamageEvent::CAUSE_CUSTOM, $d);
		$target->attack($ev);
		$this->Maiddata[$eid]["atktime"] = 0;
		if($target->x - $x >= 0){
			$motionx = 1;
		}else{
			$motionx = -1;
		}
		if($target->z - $z >= 0){
			$motionz = 1;
		}else{
			$motionz = -1;
		}
		$pk = new ActorEventPacket();
		$pk->entityRuntimeId = $eid;
		$pk->event = 4;
		foreach($this->getServer()->getOnlinePlayers() as $players){
			$players->dataPacket($pk);
		}
		if(!$ev->isCancelled()){
			$motion = (new Vector3($motionx, 0.6, $motionz))->normalize();//ノックバック
			$target->setmotion($motion);
		}
	}

	public function MaidDamage($player, $eid, $damage){ //メイドがダメージを食らう
		if(isset($this->Maiddata[$eid])){
			$pk = new ActorEventPacket();
			$pk->entityRuntimeId = $eid;
			$pk->event = 2;
			foreach($this->getServer()->getOnlinePlayers() as $players){
				$players->dataPacket($pk);
			}
			$def = $this->Maiddata[$eid]["def"];
			$this->Maiddata[$eid]["hp"] = $this->Maiddata[$eid]["hp"] - ($damage * (1 - ($def*0.04)));
			$hp = $this->Maiddata[$eid]["hp"];
			if($hp < 1){
				$this->MaidDeath($eid);
			}else{
				if($this->Maiddata[$eid]["playername"] === ""){
					if($this->Maiddata[$eid]["speed"] < $this->Maidspeed * 2){
						$this->Maiddata[$eid]["speed"] = $this->Maiddata[$eid]["speed"] * 2;
					}
				}else{
					$this->Maiddata[$eid]["target"] = $player->getid();
					if($this->Maiddata[$eid]["speed"] === 0){
						$this->Maiddata[$eid]["speed"] = $this->Maidspeed;
					}
				}
			}
		}
	}

	public function MaidDeath($eid){
		if(isset($this->Maiddata[$eid])){
			$level = $this->Maiddata[$eid]["level"];
			$pos = new Vector3($this->Maiddata[$eid]["x"], $this->Maiddata[$eid]["y"], $this->Maiddata[$eid]["z"]);
			$item = $this->Maiddata[$eid]["item"];
			$y = -sin(deg2rad($this->Maiddata[$eid]["pitch"]));
			$xz = cos(deg2rad($this->Maiddata[$eid]["pitch"]));
			$x = -$xz * sin(deg2rad($this->Maiddata[$eid]["yaw"]));
			$z = $xz * cos(deg2rad($this->Maiddata[$eid]["yaw"]));
			$motion = new Vector3($x,$y - 0.4,$z);
			$level->dropItem($pos, $item, $motion);
			$pk = new ActorEventPacket();
			$pk->entityRuntimeId = $eid;
			$pk->event = 3;
			foreach($this->getServer()->getOnlinePlayers() as $players){
				$players->dataPacket($pk);
			}
			$this->getScheduler()->scheduleDelayedTask(new RemoveMaid($this, $eid), 10);
		}
	}

	public function MaidReset($eid){
		if(isset($this->Maiddata[$eid])){
			$this->Maiddata[$eid]["target"] = "";
			$this->Maiddata[$eid]["playername"] = "";
			$this->Maiddata[$eid]["mode"] = 0;
			$this->Maiddata[$eid]["time"] = 0;
			$this->Maiddata[$eid]["speed"] = $this->Maidspeed;
		}
	}

	public function Redisplay($eid){
		$pk = new RemoveEntityPacket();
		$pk->entityUniqueId = $eid;
		$this->getServer()->removePlayerListData($this->Maiddata[$eid]["uuid"], $this->getServer()->getOnlinePlayers());
		foreach($this->getServer()->getOnlinePlayers() as $players){
			$players->dataPacket($pk);
		}
		$maidname = $this->Maiddata[$eid]["name"];
		$x = $this->Maiddata[$eid]["x"];
		$y = $this->Maiddata[$eid]["y"];
		$z = $this->Maiddata[$eid]["z"];
		$yaw = $this->Maiddata[$eid]["yaw"];
		$pitch = $this->Maiddata[$eid]["pitch"];
		$item = $this->Maiddata[$eid]["item"];
		$pk = new AddPlayerPacket();
		$pk->entityRuntimeId = $eid;
		$pk->uuid = UUID::fromRandom();
		$pk->username = $maidname;
		$pk->position = new Vector3($x, $y, $z);
	   	$pk->yaw = $yaw;
		$pk->pitch = $pitch;
		$pk->item = $item;
		@$flags |= 0 << Entity::DATA_FLAG_INVISIBLE;
		@$flags |= 1 << Entity::DATA_FLAG_CAN_SHOW_NAMETAG;
		@$flags |= 1 << Entity::DATA_FLAG_ALWAYS_SHOW_NAMETAG;
		@$flags |= 0 << Entity::DATA_FLAG_IMMOBILE;
	 	$pk->metadata = [
			Entity::DATA_FLAGS => [Entity::DATA_TYPE_LONG, $flags],
				Entity::DATA_NAMETAG => [Entity::DATA_TYPE_STRING, $maidname],
			  	Entity::DATA_FLAG_NO_AI => [Entity::DATA_TYPE_BYTE, 1],
			  	Entity::DATA_LEAD_HOLDER_EID => [Entity::DATA_TYPE_LONG, -1],
				Entity::DATA_SCALE => [Entity::DATA_TYPE_FLOAT, $this->Maiddata[$eid]["size"]],//大きさ
				];
		$skin = $this->Maiddata[$eid]["skin"];
		$this->getServer()->updatePlayerListData($pk->uuid, $pk->entityRuntimeId, $maidname, $skin, "", $this->getServer()->getOnlinePlayers());
		$pk2 = new MobEquipmentPacket();
		$pk2->entityRuntimeId = $eid;
		$pk2->item = $item;
		$pk2->inventorySlot = 0;
		$pk2->hotbarSlot = 0;
		foreach($this->getServer()->getOnlinePlayers() as $players){
			$players->dataPacket($pk);
			$players->dataPacket($pk2);
		}
	}
}

class RemoveMaid extends Task{
	private $owner;

	function __construct(Plugin $owner, $eid){
		$this->owner = $owner;
		$this->eid = $eid;
	}

	function getOwner(): Plugin{
		return $this->owner;
	}

	function onRun(int $currentTick){
		$eid = $this->eid;
		$pk = new RemoveActorPacket();
		$pk->entityUniqueId = $eid;
		foreach($this->getOwner()->getServer()->getOnlinePlayers() as $players){
			$players->dataPacket($pk);
		}
		$this->getOwner()->getServer()->removePlayerListData($this->getOwner()->Maiddata[$eid]["uuid"], $this->getOwner()->getServer()->getOnlinePlayers());
		unset($this->getOwner()->Maiddata[$eid]);
		unset($this->getOwner()->eid[$eid]);
	}
}

class MaidMove extends Task{
	private $owner;

	function __construct(Plugin $owner, $eid, $x, $y, $z, $yaw, $pitch, $target){
		$this->owner = $owner;
		$this->eid = $eid;
		$this->x = $x;
		$this->y = $y;
		$this->z = $z;
		$this->yaw = $yaw;
		$this->pitch = $pitch;
		$this->target = $target;
	}

	function getOwner(): Plugin{
		return $this->owner;
	}

	function onRun(int $currentTick){
		if(isset($this->getOwner()->Maiddata[$this->eid])){
			$eid = $this->eid;
			$level = $this->getOwner()->Maiddata[$eid]["level"];
			$x = $this->x;
			$y = $this->y;
			$z = $this->z;
			$yaw = $this->yaw;
			$pitch = $this->pitch;
			$target = $this->target;
			$playername = $this->getOwner()->Maiddata[$eid]["playername"];
			$mode = $this->getOwner()->Maiddata[$eid]["mode"];
			$speed = $this->getOwner()->Maiddata[$eid]["speed"] / 20;
			if($y <= 0){
				$this->getOwner()->MaidDamage("VOID", $eid, 10);
			}
			if($target === "" or $mode === 0){//RandomWalk
				if($this->getOwner()->Maiddata[$eid]["walkcount"] >= 30){
					$this->getOwner()->Maiddata[$eid]["randomwalk"] = mt_rand(1,10);
					$this->getOwner()->Maiddata[$eid]["walkcount"] = 0;
					if($this->getOwner()->Maiddata[$eid]["speed"] > $this->getOwner()->Maidspeed){
						$this->getOwner()->Maiddata[$eid]["speed"] = $this->getOwner()->Maidspeed;
					}
				}
				$randomwalk = $this->getOwner()->Maiddata[$eid]["randomwalk"];
				if($randomwalk === 1){
					$x = $x + $speed;
					$yaw = 270;
				}elseif($randomwalk === 2){
					$x = $x - $speed;
					$yaw = 90;
				}elseif($randomwalk === 3){
					$z = $z + $speed;
					$yaw = 360;
				}elseif($randomwalk === 4){
					$z = $z - $speed;
					$yaw = 180;
				}elseif($randomwalk === 5){
					$x = $x + $speed;
					$z = $z + $speed;
					$yaw = 315;
				}elseif($randomwalk === 6){
					$x = $x + $speed;
					$z = $z - $speed;
					$yaw = 225;
				}elseif($randomwalk === 7){
					$x = $x - $speed;
					$z = $z + $speed;
					$yaw = 45;
				}elseif($randomwalk === 8){
					$x = $x - $speed;
					$z = $z - $speed;
					$yaw = 135;
				}else{
					$yaw = $yaw;
				}
				$this->getOwner()->Maiddata[$eid]["walkcount"]++;
				$yy = $y - 1;
				$blockid = $level->getBlock(new Vector3($x, $y, $z))->getID();
				$blockid2 = $level->getBlock(new Vector3($x, $yy, $z))->getID();
				$blockid3 = $level->getBlock(new Vector3($x, $y+1, $z))->getID();
				if($blockid !== 0 and $blockid !== 6 and $blockid !== 8 and $blockid !== 9 and $blockid !== 10 and $blockid !== 11 and $blockid !== 27 and $blockid !== 28 and $blockid !== 30 and $blockid !== 31 and $blockid !== 32 and $blockid !== 37 and $blockid !== 38 and $blockid !== 39 and $blockid !== 40 and $blockid !== 50 and $blockid !== 51 and $blockid !== 55 and $blockid !== 59 and $blockid !== 63 and $blockid !== 68 and $blockid !== 70 and $blockid !== 72 and $blockid !== 75 and $blockid !== 76 and $blockid !== 78 and $blockid !== 83 and $blockid !== 90 and $blockid !== 104 and $blockid !== 105 and $blockid !== 106 and $blockid !== 115 and $blockid !== 119 and $blockid !== 126 and $blockid !== 132 and $blockid !== 141 and $blockid !== 142 and $blockid !== 147 and $blockid !== 148 and $blockid !== 171 and $blockid !== 175  and $blockid !== 199 and $blockid !== 244){
					$y++;
				}elseif($blockid2 === 0 or $blockid2 === 6 or $blockid2 === 8 or $blockid2 === 9 or $blockid2 === 10 or $blockid2 === 11 or $blockid2 === 27 or $blockid2 === 28 or $blockid2 === 30 or $blockid2 === 31 or $blockid2 === 32 or $blockid2 === 37 or $blockid2 === 38 or $blockid2 === 39 or $blockid2 === 40 or $blockid2 === 50 or $blockid2 === 51 or $blockid2 === 55 or $blockid2 === 59 or $blockid2 === 63 or $blockid2 === 68 or $blockid2 === 70 or $blockid2 === 72 or $blockid2 === 75 or $blockid2 === 76 or $blockid2 === 78 or $blockid2 === 83 or $blockid2 === 90 or $blockid2 === 104 or $blockid2 === 105 or $blockid2 === 106 or $blockid2 === 115 or $blockid2 === 119 or $blockid2 === 126 or $blockid2 === 132 or $blockid2 === 141 or $blockid2 === 142 or $blockid2 === 147 or $blockid2 === 148 or $blockid2 === 171 or $blockid2 === 175 or $blockid2 === 199 or $blockid2 === 244){
					$y--;
				}elseif($blockid3 !== 0 and $blockid3 !== 8 and $blockid3 !== 9 and $blockid3 !== 10 and $blockid3 !== 11){
					$y++;
				}
				$pk = new MovePlayerPacket();
				$pk->entityRuntimeId = $eid;
				$pk->position = new Vector3($x, $y + 1.62, $z);
				$pk->pitch = $pitch;
				$pk->yaw = $yaw;
				$pk->headYaw = $yaw;
				foreach($this->getOwner()->getServer()->getOnlinePlayers() as $players){
					$players->dataPacket($pk);
				}
				$this->getOwner()->Maiddata[$eid]["x"] = $x;
				$this->getOwner()->Maiddata[$eid]["y"] = $y;
				$this->getOwner()->Maiddata[$eid]["z"] = $z;
				$this->getOwner()->Maiddata[$eid]["yaw"] = $yaw;
				$this->getOwner()->Maiddata[$eid]["pitch"] = $pitch;
				$target = $this->getOwner()->Maiddata[$eid]["target"];
				$this->getOwner()->getScheduler()->scheduleDelayedTask(new MaidMove($this->getOwner(), $eid, $x, $y, $z, $yaw, $pitch, $target), 1);//ループ
			}else{
				if($this->getOwner()->Maiddata[$eid]["speed"] > $this->getOwner()->Maidspeed){
					$this->getOwner()->Maiddata[$eid]["speed"] = $this->getOwner()->Maidspeed;
				}
				$targetentity = $level->getEntity($target);
				$player = $this->getOwner()->getServer()->getPlayer($playername);
				if(!$player instanceof Player){
					$this->getOwner()->MaidReset($eid);
					$this->getOwner()->getScheduler()->scheduleDelayedTask(new MaidMove($this->getOwner(), $eid, $x, $y, $z, $yaw, $pitch, ""), 1);
				}else{
					$pos = new Vector3($x,$y + 1.62,$z);
					$speed = $this->getOwner()->Maiddata[$eid]["speed"] / 20;
					if($targetentity != null){
						$px = $targetentity->x;
						$py = $targetentity->y;
						$pz = $targetentity->z;
						$level = $targetentity->getLevel();
					}else{
						$px = $player->x;
						$py = $player->y;
						$pz = $player->z;
						$level = $player->getLevel();
						$this->getOwner()->Maiddata[$eid]["target"] = $player->getid();
						$targetentity = $level->getEntity($this->getOwner()->Maiddata[$eid]["target"]);
					}
					$targetpos = new Vector3($px, $py, $pz);
					$epx = $px - $x;
					$epy = $py - $y;
					$epz = $pz - $z;
					$playerdistance = $this->getOwner()->Maiddata[$eid]["playerdistance"];
					$enemydistance = $this->getOwner()->Maiddata[$eid]["enemydistance"];
					$searchdistance = $this->getOwner()->Maiddata[$eid]["searchdistance"];
					if(($target === $player->getid() and $targetpos->distance($pos) <= $playerdistance) or ($target !== $player->getid() and $targetpos->distance($pos) <= $enemydistance)){
						if($px > $x){
							$x = $x + 0;
						}else{
							$x = $x - 0;
						}
						if($pz > $z){
							$z = $z + 0;
						}else{
							$z = $z - 0;
						}
					}else{
						if($px > $x){
							$x = $x + $speed;
						}else{
							$x = $x - $speed;
						}
						if($pz > $z){
							$z = $z + $speed;
						}else{
							$z = $z - $speed;
						}
					}
					$yy = $y - 1;
					$yaw = rad2deg(atan2($epz, $epx)) - 90;
					if($yaw < 0){
						$yaw = $yaw + 360;
					}
					$blockid = $level->getBlock(new Vector3($x, $y, $z))->getID();
					$blockid2 = $level->getBlock(new Vector3($x, $yy, $z))->getID();
					$blockid3 = $level->getBlock(new Vector3($x, $y+1, $z))->getID();
					if($blockid !== 0 and $blockid !== 6 and $blockid !== 8 and $blockid !== 9 and $blockid !== 10 and $blockid !== 11 and $blockid !== 27 and $blockid !== 28 and $blockid !== 30 and $blockid !== 31 and $blockid !== 32 and $blockid !== 37 and $blockid !== 38 and $blockid !== 39 and $blockid !== 40 and $blockid !== 50 and $blockid !== 51 and $blockid !== 55 and $blockid !== 59 and $blockid !== 63 and $blockid !== 68 and $blockid !== 70 and $blockid !== 72 and $blockid !== 75 and $blockid !== 76 and $blockid !== 78 and $blockid !== 83 and $blockid !== 90 and $blockid !== 104 and $blockid !== 105 and $blockid !== 106 and $blockid !== 115 and $blockid !== 119 and $blockid !== 126 and $blockid !== 132 and $blockid !== 141 and $blockid !== 142 and $blockid !== 147 and $blockid !== 148 and $blockid !== 171 and $blockid !== 175  and $blockid !== 199 and $blockid !== 244){
						$y++;
					}elseif($blockid2 === 0 or $blockid2 === 6 or $blockid2 === 8 or $blockid2 === 9 or $blockid2 === 10 or $blockid2 === 11 or $blockid2 === 27 or $blockid2 === 28 or $blockid2 === 30 or $blockid2 === 31 or $blockid2 === 32 or $blockid2 === 37 or $blockid2 === 38 or $blockid2 === 39 or $blockid2 === 40 or $blockid2 === 50 or $blockid2 === 51 or $blockid2 === 55 or $blockid2 === 59 or $blockid2 === 63 or $blockid2 === 68 or $blockid2 === 70 or $blockid2 === 72 or $blockid2 === 75 or $blockid2 === 76 or $blockid2 === 78 or $blockid2 === 83 or $blockid2 === 90 or $blockid2 === 104 or $blockid2 === 105 or $blockid2 === 106 or $blockid2 === 115 or $blockid2 === 119 or $blockid2 === 126 or $blockid2 === 132 or $blockid2 === 141 or $blockid2 === 142 or $blockid2 === 147 or $blockid2 === 148 or $blockid2 === 171 or $blockid2 === 175 or $blockid2 === 199 or $blockid2 === 244){
						$y--;
					}elseif($blockid3 !== 0 and $blockid3 !== 8 and $blockid3 !== 9 and $blockid3 !== 10 and $blockid3 !== 11){
						$y++;
					}
					if($player->distance($pos) >= $searchdistance and $mode === 1 and $speed !== 0){
						$x = $player->getX();
						$y = $player->getY();
						$z = $player->getZ();
					}
					$pk = new MovePlayerPacket();
					$pk->entityRuntimeId = $eid;
					$pk->position = new Vector3($x, $y + 1.62, $z);
					$pk->pitch = $pitch;
					$pk->yaw = $yaw;
					$pk->headYaw = $yaw;
					foreach($this->getOwner()->getServer()->getOnlinePlayers() as $players){
						$players->dataPacket($pk);
					}
					if($targetentity->distance($pos) >= $searchdistance and $target !== $player->getid()){
						$this->getOwner()->Maiddata[$eid]["target"] = $player->getid();
					}
					$this->getOwner()->Maiddata[$eid]["x"] = $x;
					$this->getOwner()->Maiddata[$eid]["y"] = $y;
					$this->getOwner()->Maiddata[$eid]["z"] = $z;
					$this->getOwner()->Maiddata[$eid]["yaw"] = $yaw;
					$this->getOwner()->Maiddata[$eid]["pitch"] = $pitch;
					$this->getOwner()->Maiddata[$eid]["level"] = $level;
					$this->getOwner()->Maiddata[$eid]["atktime"] += 1;
					$this->getOwner()->Maiddata[$eid]["time"] -= 1;
					$time = $this->getOwner()->Maiddata[$eid]["time"];
					if($time <= 0){
						$this->getOwner()->MaidReset($eid);
						$this->getOwner()->getScheduler()->scheduleDelayedTask(new MaidMove($this->getOwner(), $eid, $x, $y, $z, $yaw, $pitch, ""), 1);
					}else{
						$target = $this->getOwner()->Maiddata[$eid]["target"];
						$this->getOwner()->getScheduler()->scheduleDelayedTask(new MaidMove($this->getOwner(), $eid, $x, $y, $z, $yaw, $pitch, $target), 1);
						$atktime = $this->getOwner()->Maiddata[$eid]["atktime"];
						$reatk = $this->getOwner()->Maiddata[$eid]["reatk"];
						$atkrange = $this->getOwner()->Maiddata[$eid]["atkrange"];
						$playername = $this->getOwner()->Maiddata[$eid]["playername"];
						if($targetentity->getid() !== $player->getid() and $atktime >= $reatk and $targetpos->distance($pos) <= $atkrange){
							$this->getOwner()->Maiddata[$eid]["atktime"] = 0;
							$this->getOwner()->MaidATK($eid, $targetentity);
						}
					}
				}
			}
		}
	}
}