<?php

namespace Kami\BookingBundle\Twig;

use Doctrine\Bundle\DoctrineBundle\Registry;

class CalendarExtension extends \Twig\Extension\AbstractExtension
{
    /**
     * booker service.
     *
     */
    private $booker;

    /**
     * @var \Doctrine\Bundle\DoctrineBundle\Registry
     */
    private $doctrine;

    /**
     * @var \Twig_Environment
     */
    private $environment;

    /**
     * @param string   $entity
     * @param Registry $doctrine
     */
    public function __construct(\Kami\BookingBundle\Helper\Booker $booker, Registry $doctrine)
    {
        $this->booker = $booker;
        $this->doctrine = $doctrine;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig\TwigFunction('kami_booking_calendar', [$this, 'renderBookCalendar'], ['is_safe'=>['html']]),
        ];
    }

    public function renderBookCalendar($item, $start = 'now', $months = 1)
    {
        if (intval($months) === 0) {
            throw new \InvalidArgumentException('Month number should be integer');
        }
        $now = new \DateTime($start);
        $end = new \DateTime();
        $end->add(new \DateInterval('P'.$months.'M'));

        $capacity = $this->booker->getFreeCapacityForDateRange($item, $now, $end);
        dump($capacity);die();

        return $this->environment->render('KamiBookingBundle:Calendar:month.html.twig', [
            'bookings'=> $bookings,
            'start'   => $start,
            'months'  => $months,
        ]);
    } 

    public function renderCalendar($item, $start = 'now', $months = 1)
    {
        if (intval($months) === 0) {
            throw new \InvalidArgumentException('Month number should be integer');
        }
        $now = new \DateTime($start);
        $end = new \DateTime();
        $end->add(new \DateInterval('P'.$months.'M'));

        $bookings = $this->doctrine->getRepository($this->entity)
            ->createQueryBuilder('b')
            ->select('b')
            ->where('b.start >= :now')
            ->orWhere('b.start <= :end')
            ->orWhere('b.end >= :now')
            ->andWhere('b.item = :item')
            ->orderBy('b.start', 'ASC')
            ->setParameters([
                'now' => $now,
                'end' => $end,
                'item'=> $item,
            ])
            ->getQuery()
            ->getResult();

        return $this->environment->render('KamiBookingBundle:Calendar:month.html.twig', [
            'bookings'=> $bookings,
            'start'   => $start,
            'months'  => $months,
        ]);
    }

    public function initRuntime(\Twig\Environment $environment)
    {
        $this->environment = $environment;
    }

    public function getName()
    {
        return 'kami_booking_bundle_calendar';
    }
}
