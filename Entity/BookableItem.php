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

    /**
     * @ORM\Column(type="boolean")
     */
    private $active = true;

    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    public function setCapacity(int $capacity): self
    {
        $this->capacity = $capacity;

        return $this;
    }

    

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }
}
