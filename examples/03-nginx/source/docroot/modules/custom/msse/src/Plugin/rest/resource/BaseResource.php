<?php

namespace Drupal\msse\Plugin\rest\resource;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Drupal\rest\ModifiedResourceResponse;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseResource extends ResourceBase
{
  public const STATUS_SUCCESS = 200;

//  protected function getBaseRouteRequirements($method)
//  {
//    //Just For Dev Mode,You Can Remove It In Production
//    if ($method == 'GET') {
//      return [];
//    }
//
//    return parent::getBaseRouteRequirements($method);
//  }

  /**
   * @param $data
   * @param bool $cached
   * @param int $maxAge
   * @return Response
   */
  protected function buildResponse($data, $cached=false, $maxAge=Cache::PERMANENT)
  {
    if ($cached){
      $response = new ResourceResponse($data);
      $disableCache = new CacheableMetadata();
      $disableCache->setCacheMaxAge($maxAge);

      $response->addCacheableDependency($disableCache);
      return $response;
    }else{
      return new ModifiedResourceResponse($data);
    }
  }

  /**
   * @param $result mixed assign Object
   * @param string $action
   * @return Response
   */
  protected function success($result, $action = 'get') {
    $response = [
      'code' => self::STATUS_SUCCESS,
      'message' => 'SUCCESS',
      'data' => [
        'result' => $result //$result assign Object
      ],
    ];

    if ($action == 'get') {
      return $this->buildResponse($response);
    } else {
      return $this->buildResponse($response);
    }
  }

  /**
   * @param $collection mixed assign ResultSet
   * @param array $metadata append metadata for data level
   * @return ResourceResponse
   */
  protected function successMetadata($collection, $metadata = []) {
    $response = [
      'code' => self::STATUS_SUCCESS,
      'message' => 'SUCCESS',
      'data' => [
        'result' => $collection //$result assign ResultSet
      ],
    ];

    if (!empty($metadata)) {
      $response['data'] += $metadata;
    }

    return $this->buildResponse($response);
  }

  /**
   *
   * @param $collection mixed assign ResultSet
   * @param bool $pageSize
   * @return ResourceResponse
   */
  protected function successAutoPager($collection, $pageSize = null) {
    if (empty($pageSize)) {
      $pageSize = \Drupal::request()->query->get('page_size', 10);
    }
    $response = [
      'code' => self::STATUS_SUCCESS,
      'message' => 'SUCCESS',
      'data' => [
        'result' => $collection,
        'hasMorePage' => count($collection) < $pageSize ? FALSE : TRUE
      ],
    ];
    return $this->buildResponse($response);
  }

  /**
   *
   * @param $collection mixed assign ResultSet
   * @param bool $hasMorePage
   * @return ResourceResponse
   */
  protected function successManualPager($collection, $hasMorePage = FALSE) {
    $response = [
      'code' => self::STATUS_SUCCESS,
      'message' => 'SUCCESS',
      'data' => [
        'result' => $collection,
        'hasMorePage' => $hasMorePage
      ],
    ];
    return $this->buildResponse($response);
  }

  /**
   * @param $code
   * @param $msg
   * @return ModifiedResourceResponse
   */
  protected function fail($code, $msg)
  {
    $response = [
      'code' => $code,
      'message' => $msg,
      'data' => [
        'result' => [] // Client Not Care The Field
      ],
    ];

    return $this->buildResponse($response);
  }
}
