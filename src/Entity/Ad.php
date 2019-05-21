<?php

namespace App\Entity;

use App\Entity\User;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * @ORM\Entity(repositoryClass="App\Repository\AdRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(
 *  fields={"title"}
 * )
 */
class Ad
{
   /**
    * @ORM\Id()
    * @ORM\GeneratedValue()
    * @ORM\Column(type="integer")
    */
   private $id;

   /**
    * @ORM\Column(type="string", length=255)
    * @Assert\Length(
    *  min=10,
    *  max=255,
    *  minMessage="Ce champ doit faire plus de 10 caractères",
    *  maxMessage="Ce champ ne doit pas faire plus de 255 caractères"
    * )
    */
   private $title;

   /**
    * @ORM\Column(type="string", length=255)
    */
   private $slug;

   /**
    * @ORM\Column(type="float")
    */
   private $price;

   /**
    * @ORM\Column(type="text")
    * @Assert\Length(
    *  min=20,
    *  minMessage="Ce champ ne doit pas faire moins de 20 caractères"
    * )
    */
   private $introduction;

   /**
    * @ORM\Column(type="text")
    * Assert\Length(
    *  min=20,
    *  minMessage="Ce champ ne doit pas faire moins de 20 caractères"
    * )
    */
   private $content;

   /**
    * @ORM\Column(type="string", length=255)
    * @Assert\Url(
    *  protocols = {"http", "https", "ftp"}
    * )
    */
   private $coverImage;

   /**
    * @ORM\Column(type="integer")
    */
   private $rooms;

   /**
    * @ORM\OneToMany(targetEntity="App\Entity\Image", mappedBy="ad", orphanRemoval=true)
    * @Assert\Valid
    */
   private $images;

   /**
    * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="ads")
    * @ORM\JoinColumn(nullable=false)
    */
   private $author;

   /**
    * @ORM\OneToMany(targetEntity="App\Entity\Booking", mappedBy="BookerAd", orphanRemoval=true)
    */
   private $bookings;

   /**
    * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="ad", orphanRemoval=true)
    */
   private $comments;

   public function __construct()
   {
      $this->images = new ArrayCollection();
      $this->bookings = new ArrayCollection();
      $this->comments = new ArrayCollection();
   }

   /**
    * Cette méthode va donc nous retourner tout simplement le slug via le title
    * @ORM\PrePersist
    * @ORM\PreUpdate
    * @return void
    */
   public function initilizeSlug() {
      if ( empty($this->slug) ):
         $slugify = new Slugify();
         $this->slug = $slugify->slugify($this->title);
      endif;
   }

   /**
    * Permet de nous retourner Objet DateTime contenant toutes les dates qui sont déja réservées
    *
    * @return array représentant toutes les dates d'occupation
    */
   public function getNotAvailableDays() {

      $notAvailableDays = [];

      foreach($this->bookings as $booking):
         // Toutes les dates qui sont déja réservées sous la forme de Timestamp
         $resultat = range(
            $booking->getStartDate()->getTimestamp(),
            $booking->getEndDate()->getTimestamp(),
            24 * 3600
         );

         // Transformation le tableau $resultat en DateTime
         $days = array_map(function($dayTimeStamp) {
            return new \DateTime(date('Y-m-d', $dayTimeStamp));
         }, $resultat);

         // Fusionner deux tableaux
         $notAvailableDays = array_merge($notAvailableDays, $days);
      endforeach;

      return $notAvailableDays;
   }

   /**
    * Permet de retourner le rating de tous les commentaires
    *
    * @return void
    */
   public function getRatings() {
      // Calculer le rating de tous les commentaires
      $ratings = array_reduce($this->comments->toArray(), function($total, $comment){
         return $total + $comment->getRating();
      }, 0);
      
      // Le rating chaque commentaire
      return $ratingAd = count($this->comments) > 0 ? $ratings / count($this->comments) : 0;
   }
   
   /**
    * Permet de nous retourner les commentaires
    *
    * @return $comment, null
    */
   public function getAllComment(User $user) {
      foreach($this->comments as $comment):
         if ($comment->getAuthor() === $user) return $comment;
      endforeach;

      return null;
   }

   public function getId(): ?int
   {
      return $this->id;
   }

   public function getTitle(): ?string
   {
      return $this->title;
   }

   public function setTitle(string $title): self
   {
      $this->title = $title;

      return $this;
   }

   public function getSlug(): ?string
   {
      return $this->slug;
   }

   public function setSlug(string $slug): self
   {
      $this->slug = $slug;

      return $this;
   }

   public function getPrice(): ?float
   {
      return $this->price;
   }

   public function setPrice(float $price): self
   {
      $this->price = $price;

      return $this;
   }

   public function getIntroduction(): ?string
   {
      return $this->introduction;
   }

   public function setIntroduction(string $introduction): self
   {
      $this->introduction = $introduction;

      return $this;
   }

   public function getContent(): ?string
   {
      return $this->content;
   }

   public function setContent(string $content): self
   {
      $this->content = $content;

      return $this;
   }

   public function getCoverImage(): ?string
   {
      return $this->coverImage;
   }

   public function setCoverImage(string $coverImage): self
   {
      $this->coverImage = $coverImage;

      return $this;
   }

   public function getRooms(): ?int
   {
      return $this->rooms;
   }

   public function setRooms(int $rooms): self
   {
      $this->rooms = $rooms;

      return $this;
   }

   /**
    * @return Collection|Image[]
    */
   public function getImages(): Collection
   {
      return $this->images;
   }

   public function addImage(Image $image): self
   {
      if (!$this->images->contains($image)) {
         $this->images[] = $image;
         $image->setAd($this);
      }

      return $this;
   }

   public function removeImage(Image $image): self
   {
      if ($this->images->contains($image)) {
         $this->images->removeElement($image);
         // set the owning side to null (unless already changed)
         if ($image->getAd() === $this) {
               $image->setAd(null);
         }
      }

      return $this;
   }

   public function getAuthor(): ?User
   {
      return $this->author;
   }

   public function setAuthor(?User $author): self
   {
      $this->author = $author;

      return $this;
   }

   /**
    * @return Collection|Booking[]
    */
   public function getBookings(): Collection
   {
      return $this->bookings;
   }

   public function addBooking(Booking $booking): self
   {
      if (!$this->bookings->contains($booking)) {
         $this->bookings[] = $booking;
         $booking->setBookerAd($this);
      }

      return $this;
   }

   public function removeBooking(Booking $booking): self
   {
      if ($this->bookings->contains($booking)) {
         $this->bookings->removeElement($booking);
         // set the owning side to null (unless already changed)
         if ($booking->getBookerAd() === $this) {
               $booking->setBookerAd(null);
         }
      }

      return $this;
   }

   /**
    * @return Collection|Comment[]
    */
   public function getComments(): Collection
   {
       return $this->comments;
   }

   public function addComment(Comment $comment): self
   {
       if (!$this->comments->contains($comment)) {
           $this->comments[] = $comment;
           $comment->setAd($this);
       }

       return $this;
   }

   public function removeComment(Comment $comment): self
   {
       if ($this->comments->contains($comment)) {
           $this->comments->removeElement($comment);
           // set the owning side to null (unless already changed)
           if ($comment->getAd() === $this) {
               $comment->setAd(null);
           }
       }

       return $this;
   }
}


