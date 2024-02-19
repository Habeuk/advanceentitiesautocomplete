<?php

namespace Drupal\advanceentitiesautocomplete\Controller;

use Drupal\system\Controller\EntityAutocompleteController as EntityAutocompleteControllerBase;
use Drupal\Component\Utility\Crypt;
use Drupal\Component\Utility\Tags;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityAutocompleteMatcherInterface;
use Drupal\Core\KeyValueStore\KeyValueStoreInterface;
use Drupal\Core\Site\Settings;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Drupal\Component\Utility\Html;

/**
 *
 * @author stephane
 *        
 */
class EntityAutocompleteController extends EntityAutocompleteControllerBase {
  
  /**
   * Autocomplete the label of an entity.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *        The request object that contains the typed tags.
   * @param string $target_type
   *        The ID of the target entity type.
   * @param string $selection_handler
   *        The plugin ID of the entity reference selection handler.
   * @param string $selection_settings_key
   *        The hashed key of the key/value entry that holds the selection
   *        handler
   *        settings.
   *        
   * @return \Symfony\Component\HttpFoundation\JsonResponse The matched entity
   *         labels as a JSON response.
   *        
   * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
   *         Thrown if the selection settings key is not found in the key/value
   *         store
   *         or if it does not match the stored data.
   */
  public function handleAutocomplete(Request $request, $target_type, $selection_handler, $selection_settings_key) {
    /**
     * Pour gagner en temps, on va recherche les paragraphes avec l'id et le
     * reste sans chanegement.
     *
     * @var array $matches
     */
    if ($target_type == 'paragraph') {
      // Get the typed string from the URL, if it exists.
      if ($input = $request->query->get('q')) {
        $tag_list = Tags::explode($input);
        $typed_string = !empty($tag_list) ? mb_strtolower(array_pop($tag_list)) : '';
        
        // Selection settings are passed in as a hashed key of a serialized
        // array
        // stored in the key/value store.
        $selection_settings = $this->keyValue->get($selection_settings_key, FALSE);
        if ($selection_settings !== FALSE) {
          $selection_settings_hash = Crypt::hmacBase64(serialize($selection_settings) . $target_type . $selection_handler, Settings::getHashSalt());
          if (!hash_equals($selection_settings_hash, $selection_settings_key)) {
            // Disallow access when the selection settings hash does not match
            // the
            // passed-in key.
            throw new AccessDeniedHttpException('Invalid selection settings key.');
          }
        }
        else {
          // Disallow access when the selection settings key is not found in the
          // key/value store.
          throw new AccessDeniedHttpException();
        }
        
        $entity_type_id = $request->query->get('entity_type');
        if ($entity_type_id && $this->entityTypeManager()->hasDefinition($entity_type_id)) {
          $entity_id = $request->query->get('entity_id');
          if ($entity_id) {
            $entity = $this->entityTypeManager()->getStorage($entity_type_id)->load($entity_id);
            if ($entity->access('update')) {
              $selection_settings['entity'] = $entity;
            }
          }
        }
        $query = $this->entityTypeManager()->getStorage($target_type)->getQuery();
        $query->accessCheck(TRUE);
        $query->condition('status', true);
        $query->condition('id', $typed_string . '%', 'LIKE');
        $query->sort('created');
        $query->range(0, 25);
        $ids = $query->execute();
        
        if ($ids) {
          $entities = $this->entityTypeManager()->getStorage($target_type)->loadMultiple($ids);
          foreach ($entities as $entity_id => $entity) {
            $label = $entity->label();
            $key = "$label ($entity_id)";
            $key = preg_replace('/\s\s+/', ' ', str_replace("\n", '', trim(Html::decodeEntities(strip_tags($key)))));
            // Names containing commas or quotes must be wrapped in quotes.
            $key = Tags::encode($key);
            $matches[] = [
              'value' => $key,
              'label' => $label
            ];
          }
        }
      }
      
      return new JsonResponse($matches);
    }
    else
      return parent::handleAutocomplete($request, $target_type, $selection_handler, $selection_settings_key);
    $matches = [];
  }
  
}