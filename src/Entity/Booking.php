<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\PrePersist;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BookingRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Booking
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $booker;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Ad", inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $ad;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Date(message="Attention, la date d'arrivée doit être au bon format !")
     * @Assert\GreaterThan("today", message="La date d'arrivée doit être ultérieur à la date d'aujourd'hui")
     */
    private $startDate;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Date(message="Attention, la date de départ doit être au bon format !")
     * @Assert\GreaterThan(propertyPath="startDate", message="La date de départ doit être plus éloignée
     que la date d'arrivée !")
     */
    private $endDate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createAt;

    /**
     * @ORM\Column(type="float")
     */
    private $amount;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    /**
     * CallBack appelé à chaque fois que l'on veut créer une réservation
     * 
     * @ORM\PrePersist
     *
     * @return void
     */
    public function PrePersist()
    {
        if(empty($this->createAt))
        {
            $this->createAt = new \DateTime();
        }
        if(empty($this->amount))
        {
            //montant = prix de l'annonce * nombre de jours
            $this->amount = $this->ad->getPrice() * $this->getDuration();
        }
    }

    /**
     * Permet de savoir si une date est disponibles pour la réservation
     *
     * @return boolean
     */
    public function isBookableDates()
    {
        // 1. Il faut connaître les dates déja de réservées de l'annonce
        $notAvailableDays = $this->ad->getNotAvailableDays();
        // 2. Il faut comparer les dates choisies avec les dates déjà réservées
        $bookingDays = $this->getDays();

        $formatDay = function($day){
            return new \DateTime(date("Y-m-d", $day));
        };

        // Tableau des chaines de caractères des journées
        $days = array_map($formatDay, $bookingDays);

        $notAvailable = array_map($formatDay, $notAvailableDays);

        foreach($days as $day)
        {
            if(array_search($day, $notAvailable) !== false)
                return false;
        }

        return true;
    }

    /**
     * Permet d'avoir un tableau des jours qui correspondent à ma réservation
     *
     * @return array Un tableau d'objets DateTime représentant les jours d'occupation
     */
    public function getDays()
    {
        $resultat = range(
            $this->startDate->getTimestamp(),
            $this->endDate->getTimestamp(),
            24*60*60
        );

        $days = array_map(function($dayTimestamp){
            return new \DateTime(date('Y-m-d', $dayTimestamp));
        }, $resultat);

        return $days;
    }

    public function getDuration()
    {
        // la différence entre 2 dates renvoie un objet "DateInterval"
        $diff = $this->endDate->diff($this->startDate);
        // on recupère la différence en nombre de jours
        return $diff->days;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBooker(): ?User
    {
        return $this->booker;
    }

    public function setBooker(?User $booker): self
    {
        $this->booker = $booker;

        return $this;
    }

    public function getAd(): ?Ad
    {
        return $this->ad;
    }

    public function setAd(?Ad $ad): self
    {
        $this->ad = $ad;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getCreateAt(): ?\DateTimeInterface
    {
        return $this->createAt;
    }

    public function setCreateAt(\DateTimeInterface $createAt): self
    {
        $this->createAt = $createAt;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }
}
