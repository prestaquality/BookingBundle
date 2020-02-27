<?php

namespace Kami\BookingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BookableItem.
 *
 * @codeCoverageIgnore
 * @ORM\MappedSuperclass
 */
abstract class BookableItem
{
   
    /**
     * @ORM\Column(type="integer")
     * Defines the capacity of the class
     */
    protected $capacity;

    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    public function setCapacity(int $capacity): self
    {
        $this->capacity = $capacity;

        return $this;
    }
}
