<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
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
    private $Booker;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Ad", inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $BookerAd;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\date(message="Attention, La date d'arrivée doit être au bon format")
     * @Assert\GreaterThan("today", message="Vous pouvez réserver dès des dates d'aujourd'hui !", groups={"user-front"})
     */
    private $startDate;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\date(message="Attention, La date de départ doit être le bon")
     * @Assert\GreaterThan(propertyPath="startDate", message="La date d'arrivée doit être ultérieure de date de départ, Tout a fait normal")
     */
    private $endDate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="float")
     */
    private $amount;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $comment;

    /**
     * Permet de calculer le montant et de se créer la date de création
     * 
     * @ORM\PrePersist
     * @ORM\PreUpdate
     *
     * @return void
     */
    public function prePersist() {
        if (empty($this->createdAt)):
            $this->createdAt = new \DateTime();
        endif;

        if (empty($this->amount)):
            $this->amount = $this->BookerAd->getPrice() * $this->getDays();
        endif;
    }

    /**
     * Permet de retourner le nombre de jour
     * 
     * @ORM\PrePersist
     * @ORM\PreUpdate
     *
     * @return void
     */
    public function getDays() {
        $diff = $this->endDate->diff($this->startDate);
        return $diff->days;
    }
    
    /**
     * Permet de comparer les dates qui sont déja réservées et les dates sélectionnées
     *
     * @return boolean
     */
    public function isBookableDays() {
        // Les dates qui sont déja réservées
        $notAvailableDays = $this->BookerAd->getNotAvailableDays();
        // Les dates choisis par l'utilisateur
        $daysChoice       = $this->getDaysChoice();
        
        // Fonction annonyme qui sert à transformer un tableau d'objet DatTime en string
        $dateTimeToString = function($dayTime) {
            return $dayTime->format('Y-m-d');
        };
        
        // Transoformation les deux tableaux dateTime sous la forme de string
        $notAvilDays      = array_map($dateTimeToString, $notAvailableDays);

        $dayChoi          = array_map($dateTimeToString, $daysChoice);

        foreach($dayChoi as $day):
            if (array_search($day, $notAvilDays) !== false) return false;
        endforeach;

        return true;
    }

    /**
     * Permet de nous retourner les dates choisis par l'utilisateur
     *
     * @return array contenant les dates choisis
     */
    public function getDaysChoice() {
        // Les dates sous la forme de timestamp
        $resultat = range(
            $this->startDate->getTimestamp(),
            $this->endDate->getTimestamp(),
            24 * 3600
        );

        // Transformation le tableau $resultat en objet DateTime
        $days = array_map(function($dayTimeStamp) {
            return new \DateTime(date('Y-m-d', $dayTimeStamp));
        }, $resultat);

        return $days;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBooker(): ?User
    {
        return $this->Booker;
    }

    public function setBooker(?User $Booker): self
    {
        $this->Booker = $Booker;

        return $this;
    }

    public function getBookerAd(): ?Ad
    {
        return $this->BookerAd;
    }

    public function setBookerAd(?Ad $BookerAd): self
    {
        $this->BookerAd = $BookerAd;

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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

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
