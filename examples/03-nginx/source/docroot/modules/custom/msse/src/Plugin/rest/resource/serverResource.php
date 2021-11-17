<?php

namespace Drupal\msse\Plugin\rest\resource;


use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\rest\Plugin\ResourceBase;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a resource for guide ta.
 *
 * @RestResource(
 *   id = "server_avaiable",
 *   label = @Translation("server avaiable"),
 *   uri_paths = {
 *     "canonical" = "/apis/servers"
 *   }
 * )
 */
class serverResource extends BaseResource {
  /**
   * @var EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager, $serializer_formats, LoggerInterface $logger,
    ConfigFactoryInterface $config_factory) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);
    $this->entityTypeManager = $entity_type_manager;
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->getParameter('serializer.formats'),
      $container->get('logger.factory')->get('rest'),
      $container->get('config.factory'),
    );
  }
  /**
   * @return \Drupal\rest\ModifiedResourceResponse|\Drupal\rest\ResourceResponse
   */
  public function get() {
    $redis = new \Redis();
    $redis->connect('03-nginx_redis_1', 6379);
    $result = $redis->lrange( 'client_on', 0, -1 );
    return $this->success($result);
  }

}
