<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;

class ApplicationType extends AbstractType {

   /**
      * Permet de nous retourner pour chaque champ leur label et leur placeholder
      *
      * @param string $label
      * @param string $placeholder
      * @param array $option
      * @return array
   */

   protected function paramForm($label, $placeholder = null, $option = []) {

      return array_merge_recursive([
         'label' => $label,
         'attr'  => [
            'placeholder' => $placeholder
         ],
      ], $option);
   }
}