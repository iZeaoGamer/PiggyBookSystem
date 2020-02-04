<?php

declare(strict_types=1);

namespace goldentouch74\BookSystem;

use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\item\Item;
use pocketmine\nbt\tag\StringTag;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as C;
use pocketmine\utils\TextFormat;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\command\{
    Command, CommandSender
};
use pocketmine\Server;

use jojoe77777\FormAPI\SimpleForm;
use jojoe77777\FormAPI\CustomForm;

use DaPigGuy\PiggyCustomEnchants\CustomEnchants\CustomEnchant;
use DaPigGuy\PiggyCustomEnchants\PiggyCustomEnchants;
use DaPigGuy\PiggyCustomEnchants\CustomEnchantManager;
use pocketmine\item\enchantment\EnchantmentInstance;

class Main extends PluginBase implements Listener {

    /* @var Config $config */
    public $config;
    public $overs = 0;

    public function onLoad(){
        @mkdir($this->getDataFolder());
    }

    public function onEnable(): void{
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->config = new Config($this->getDataFolder()."config.yml", Config::YAML, ["Common" => 100, "Uncommon" => 200, "Rare" => 300, "Mythic" => 400]);
    }

    public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args): bool{
        if ($sender instanceof Player) {
            $ce = $this->getServer()->getPluginManager()->getPlugin("PiggyCustomEnchants");
            if ($ce instanceof PiggyCustomEnchants) {
                $form = new SimpleForm(function ($sender, $data){
                    if(!is_null($data)) $this->confirm($sender, $data);
                });
                $form->setTitle("CustomEnchants Shop");
                $form->addButton($this->getNameByData(0));
                $form->addButton($this->getNameByData(1));
                $form->addButton($this->getNameByData(2));
                $form->addButton($this->getNameByData(3));
                $sender->sendForm($form);
                return true;
            }
        }
        return false;
    }

    public function getNameByData(int $data, $id = true): string{
        if($id){
            switch($data){
                case 0:
                    return "Common";
                case 1:
                    return "Uncommon";
                case 2:
                    return "Rare";
                case 3:
                    return "Mythic";
            }
        }else{
            switch($data){
                case 0:
                    return "10";
                case 1:
                    return "5";
                case 2:
                    return "2";
                case 3:
                    return "1";
            }
        }
    }

    /**
     * @param int $data
     * @return bool|mixed
     */
    public function getCost(int $data){
        switch($data){
            case 0:
                return $this->config->get("Common");
            case 1:
                return $this->config->get("Uncommon");
            case 2:
                return $this->config->get("Rare");
            case 3:
                return $this->config->get("Mythic");
        }
        return true;
    }

    public function confirm(Player $player, int $dataid): void{
        $ce = $this->getServer()->getPluginManager()->getPlugin("PiggyCustomEnchants");
        if ($ce instanceof PiggyCustomEnchants) {
            $form = new CustomForm(function (Player $player, $data) use ($dataid, $ce) {
                if ($data !== null) {
                  //  if ($ce instanceof PiggyCustomEnchants) {
                        if ($player->getCurrentTotalXp() < $this->getCost($dataid)) {
                            $player->sendMessage(C::RED . "You don't have enough Exp!");
                            return;
                        }
                        $item = Item::get(340);
                        $nbt = $item->getNamedTag();
                        $nbt->setString("ceid", (string)$dataid);
                        $item->setCustomName($this->getNameByData($dataid) . C::RESET . C::YELLOW . " Book");
                        $item->setLore([C::GRAY . "Tap ground to get random enchantment"]);
                        $player->getInventory()->addItem($item);
                        $player->addXp(-$this->getCost($dataid));
                    }
                
            });
            $form->setTitle((int)$this->getNameByData($dataid, false) . $this->getNameByData($dataid));
            $form->addLabel("Cost: " . $this->getCost($dataid) . " Exp");
            $player->sendForm($form);
        }
    }
    public function setOvers(int $overs){
        $this->overs = $overs;
    }
    public function getOvers(): int{
        return $this->overs;
    }
    public function onInteract(PlayerInteractEvent $e): void{
        $player = $e->getPlayer();
        $item = $e->getItem();
        $ce = $this->getServer()->getPluginManager()->getPlugin("PiggyCustomEnchants");
        if ($ce instanceof PiggyCustomEnchants) {
            if($item->getId() == 340){
                if($item->getNamedTag()->hasTag("ceid", StringTag::class)) {
                    $e->setCancelled();

                    $id = $item->getNamedTag()->getString("ceid");
                    $this->getLogger()->info("Plugin passing cid section.");
                    $this->setOvers(0);

                 //foreach(CustomEnchantManager::getEnchantments() as $eid => $data) {
                //     if(CustomEnchantManager::getEnchantments() instanceof CustomEnchant){
               // $manager = Server::getInstance()->getPluginManager()->getPlugin("PiggyCustomEnchants");
              //  if(!$manager instanceof CustomEnchantManager){
                   // return;
          //      $manager = CustomEnchantManager;
                foreach(CustomEnchantManager::$enchants as $enchantmanager => $enchantment){
                      if ($enchantment->getName() !== $this->getNameByData((int)$id)){
                    
                            switch ($id) {
                                case 0: //Common
                                    $enchs = [114, 101, 109, 601, 100, 405];
                                    break;
                                case 1: //Uncommon
                                    $enchs = [108, 122, 120, 309, 113, 801, 412, 408, 117, 121, 206, 202, 401, 209, 208, 603, 500, 103, 415, 402, 207, 210, 312, 504, 602, 304, 211, 800, 104, 403, 203, 406, 414, 201, 501, 502, 421, 111, 305, 115];
                                    break;
                                case 2: //Rare
                                    $enchs = [417, 420, 411, 311, 416, 102, 410, 409, 804, 200, 404, 313, 310, 422, 600, 503, 123, 204, 315, 400, 303, 307, 424, 802, 700, 413, 407, 423, 308, 803, 205, 805, 316];
                                    break;
                                case 3: //Mythic
                                    $enchs = [604, 306, 418, 119, 212, 419, 314, 118, 301];
                                    break;
                            }
                            $enchanted = false;

                            if ($enchanted == false && $this->getOvers() < 1) {
                                $enchanted = true;
                                $this->setOvers($this->getOvers() + 1);
                                $info["ench"] = $enchs[array_rand($enchs)];
                                   $enchant = is_numeric($info["ench"]) ? CustomEnchantManager::getEnchantment((int)$info["ench"]) : CustomEnchantManager::getEnchantmentByName($info["ench"]);
                    if ($enchant == null) {
                        $player->sendMessage(TextFormat::RED . "Invalid enchantment.");
                        return;
                    }
                              //  $ench = CustomEnchantManager::getEnchantment($info["ench"]);
                              //  if (!$ench instanceof CustomEnchant){
                                  //  $player->sendMessage(TextFormat::colorize("&cEnchant not found."));
                              //  }else{
                                    
                                
                                //$enchName = CustomEnchantManager::getEnchantmentByName($info["ench"]);
                                $info["lvl"] = mt_rand(1, $enchant->getMaxLevel());
                                $book = Item::get(Item::ENCHANTED_BOOK);
                                $hand = $player->getInventory()->getItemInHand();
                                $player->getInventory()->setItemInHand($hand->setCount($hand->getCount() - 1));
                             //   $hand = $player->getInventory()->getItemInHand();
                                 $book->addEnchantment(new EnchantmentInstance($enchant, $info["lvl"]));
                           $player->getInventory()->addItem($book);
                           $player->sendMessage(TextFormat::colorize("&aEnchant success."));
                            }
                        }
                    }
            }
    
        }
    }
}
}
