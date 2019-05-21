<?php

namespace App\Service;

use Doctrine\Common\Persistence\ObjectManager;

class PaginationService {

   private $manager;
   private $entityClassName;
   private $limit       = 10;
   private $currentPage = 1;

   public function __construct(ObjectManager $manager) {
      $this->manager = $manager;
   }

   /**
    * Permet de nous retourner toutes les pages
    *
    * @return $allPages contenant toutes les pages
    */
   public function allPages() {
      $repo = $this->manager->getRepository($this->entityClassName);
      $allPages = count($repo->findAll()) / $this->limit;

      return ceil($allPages);
   }

   /**
    * Permet de nous retourner toutes les données ( mais par limite )
    *
    * @return $data contenant 10 champs par offset
    */
   public function getData() {
      // Caculer l'offset
      $offset = $this->currentPage * $this->limit - $this->limit;

      // Création de repositoty grâce à le manager
      $repo = $this->manager->getRepository($this->entityClassName);

      // Trouver les données
      $data = $repo->findBy([], [], $this->limit, $offset);

      return $data;
   }

   public function setEntityClassName($entityClassName) {
      $this->entityClassName = $entityClassName;

      return $this;
   }

   public function getEntityClassName() {
      return $this->entityClassName;
   }

   public function setLimit(int $limit): self {
      $this->limit = $limit;

      return $this;
   }

   public function getLimit(): ?int {
      return $this->limit;
   }

   public function setCurrentPage(int $currentPage): self {
      $this->currentPage = $currentPage;

      return $this;
   }

   public function getCurrentPage(): ?int {
      return $this->currentPage;
   }

}