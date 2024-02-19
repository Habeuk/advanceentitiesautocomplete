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
    $url = '';
    if (\Drupal::languageManager()->isMultilingual()) {
      $languageId = \Drupal::languageManager()->getCurrentLanguage()->getId();
      $url .= '/' . $languageId;
    }
    if (!empty($element['#attributes']['data-autocomplete-path'])) {
      $taget_type = $element['#autocomplete_route_parameters']['target_type'];
      $selection_handler = $element['#autocomplete_route_parameters']['selection_handler'];
      $taget_type = $element['#autocomplete_route_parameters']['target_type'];
      $selection_settings_key = $element['#autocomplete_route_parameters']['selection_settings_key'];
      $url .= '/advanceentitiesautocomplete/entity_reference_autocomplete/' . $taget_type . '/' . $selection_handler . '/' . $selection_settings_key;
      $element['#attributes']['data-autocomplete-path'] = $url;
    }
    return $element;
  }
  
}