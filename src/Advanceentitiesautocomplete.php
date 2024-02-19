<?php

namespace Drupal\advanceentitiesautocomplete;

use Drupal\Core\Form\FormStateInterface;

class Advanceentitiesautocomplete {
  
  /**
   * Permet de surcharger certains elements non accessible.
   *
   * @param array $element
   * @param FormStateInterface $form_state
   * @param array $complete_form
   * @return array
   */
  public static function processEntityAutocompleteCustom(array &$element, FormStateInterface $form_state, array &$complete_form) {
    $element['#autocomplete_route_name'] = 'advanceentitiesautocomplete.entity_autocomplete';
    if (!empty($element['#attributes']['data-autocomplete-path']))
      $element['#attributes']['data-autocomplete-path'] = "/advanceentitiesautocomplete" . $element['#attributes']['data-autocomplete-path'];
    return $element;
  }
  
}