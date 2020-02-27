<?php

namespace Kami\BookingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Booking.
 *
 * @codeCoverageIgnore
 * @ORM\MappedSuperclass
 */
abstract class Booking
{
    protected $id;
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date")
     */
    protected $date;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set start.
     *
     * @param \DateTime $start
     *
     * @return Booking
     */
    public function setDate(\DateTime $date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get start.
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }
   

    abstract public function getItem();

    abstract public function setItem($item);

    abstract public function getUser();

    abstract public function setUser($user);
}
