<?php

namespace Drupal\advanceentitiesautocomplete\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Listens to the dynamic route events.
 */
class RouteSubscriber extends RouteSubscriberBase {
  
  /**
   *
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    // Change la methode de traitement de la route : system.entity_autocomplete
    // afin d'ajouter le fitre par domaine.
    if ($route = $collection->get('system.entity_autocomplete')) {
      // $route->setDefault('_controller',
      // '\Drupal\lesroidelareno\Services\EntityReferenceAutocomplete::handleAutocompleteCustom');
    }
  }
  
}