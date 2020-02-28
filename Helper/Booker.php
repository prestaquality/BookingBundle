<?php

namespace Kami\BookingBundle\Helper;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\QueryBuilder;

class Booker
{
    /**
     * @var \Doctrine\Bundle\DoctrineBundle\Registry
     */
    private $doctrine;

    /**
     * @var \Doctrine\Common\Persistence\ObjectRepository
     */
    private $repository;

    /**
     * @var string
     */
    private $entity;

    /**
     * @param string   $entity
     * @param Registry $doctrine
     */
    public function __construct($entity, Registry $doctrine)
    {
        $this->entity = $entity;
        $this->doctrine = $doctrine;
        $this->repository = $doctrine->getRepository($entity);
    }


    public function getFreeCapacityForDateRangeQB($item, \DateTime $from, \DateTime $to): QueryBuilder
    {
        //Reseteamos la hora a 0
        $from->setTime(0, 0, 0, 0);
        $to->setTime(0, 0, 0, 0);
        //Construimos
        $qb = $this->repository->createQueryBuilder('b');
        $qb->select('item.capacity - count(b.id) freecapacity');
        $qb->andWhere('b.date >= :from');
        $qb->andWhere('b.date <= :to');
        $qb->innerJoin('b.item', 'item');
        $qb->andWhere('item = :item');
        $qb->andWhere('item.active = :active');
        $qb->setParameter('item', $item);
        $qb->setParameter('from', $from);
        $qb->setParameter('to', $to);
        $qb->setParameter('active', true);
        return $qb;
    }

    public function getFreeCapacityForDateRange($item, \DateTime $from, \DateTime $to)
    {
        $qb = $this->getFreeCapacityForDateRangeQB($item, $from, $to);
        $result = $qb->getQuery()->getScalarResult();
        return $result;
    }


    public function getFreeCapacityForDateQB($item, \DateTime $date): QueryBuilder
    {
        //Reseteamos la hora a 0
        $date->setTime(0, 0, 0, 0);
        //Construimos
        $qb = $this->repository->createQueryBuilder('b');
        $qb->select('item.capacity - count(b.id) freecapacity');
        $qb->andWhere('b.date = :date');
        $qb->innerJoin('b.item', 'item');
        $qb->andWhere('item = :item');
        $qb->andWhere('item.active = :active');
        $qb->setParameter('item', $item);
        $qb->setParameter('date', $date);
        $qb->setParameter('active', true);
        return $qb;
    }

    public function getFreeCapacityForDate($item, \DateTime $date): int
    {
        $qb = $this->getFreeCapacityForDateQB($item, $date);
        $result = $qb->getQuery()->getSingleScalarResult();
        return (int) $result;
    }

    /**
     * @param $item
     * @param \DateTime $date
     *
     * @return bool
     */
    public function isAvailableForDate($item, \DateTime $date): bool
    {
        $capacity = $this->getFreeCapacityForDate($item, $date);
        return $capacity > 0;
    }

    /**
     * Indicates if the user already booked this class on this date
     */
    public function hasUserAlreadyBookedIt($item, $user, \DateTime $date):bool{
        //Reseteamos la hora a 0
        $date->setTime(0, 0, 0, 0);
        //Construimos
        $qb = $this->repository->createQueryBuilder('b');
        $qb->select('b.id');
        $qb->andWhere('b.date = :date');
        $qb->andWhere('b.item = :item');
        $qb->andWhere('b.user = :user');
        $qb->setParameter('item', $item);
        $qb->setParameter('date', $date);
        $qb->setParameter('user', $user);
        
        $results = $qb->getQuery()->getResult();
        return count($results) > 0;
    }

    public function book($item, $user, \DateTime $date)
    {
        //TODO comprobar si el usuario ya está en esta clase, que no se registre dos veces
        if ($this->isAvailableForDate($item, $date) && !$this->hasUserAlreadyBookedIt($item, $user, $date)) {
            $entity = new $this->entity();
            $entity->setItem($item);
            $entity->setDate(new \DateTime());
            $entity->setUser($user); //TODO

            $manager = $this->doctrine->getManager();
            $manager->persist($entity);
            $manager->flush();

            return $entity;
        }

        return false;
    }
}
