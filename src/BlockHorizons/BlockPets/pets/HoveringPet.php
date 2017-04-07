<?php

namespace BlockHorizons\BlockPets\pets;

use pocketmine\block\Block;
use pocketmine\math\Vector3;

abstract class HoveringPet extends BasePet {

	public function onUpdate($currentTick) {
		$petOwner = $this->getPetOwner();
		if($petOwner === null) {
			$this->despawnFromAll();
			return false;
		}
		if($this->distanceSquared($petOwner) >= 60 || $this->getLevel()->getName() !== $petOwner->getLevel()->getName()) {
			$this->teleport($petOwner);
			$this->spawnToAll();
		}
		if(!$this->isOnGround()) {
			if($this->motionY > -$this->gravity * 4) {
				$this->motionY = -$this->gravity * 4;
			} else {
				$this->motionY -= $this->gravity;
			}
			$this->move($this->motionX, $this->motionY, $this->motionZ);
		}

		if($this->isCollidedVertically) {
			$this->motionY = $this->gravity * 4;
		}
		$this->move($this->motionX, $this->motionY, $this->motionZ);

		$x = $petOwner->x - $this->x;
		$z = $petOwner->z - $this->z;
		if($x * $x + $z * $z < 5) {
			$this->motionX = 0;
			$this->motionZ = 0;
		} else {
			$this->motionX = $this->getSpeed() * 0.15 * ($x / (abs($x) + abs($z)));
			$this->motionZ = $this->getSpeed() * 0.15 * ($z / (abs($x) + abs($z)));
		}
		$this->yaw = rad2deg(atan2(-$x, $z));
		if($this->getNetworkId() === 53) {
			$this->yaw += 180;
		}
		$this->pitch = rad2deg(atan($petOwner->y - $this->y));
		$this->move($this->motionX, $this->motionY, $this->motionZ);

		$this->updateMovement();

		parent::onUpdate($currentTick);
		return true;
	}
}
