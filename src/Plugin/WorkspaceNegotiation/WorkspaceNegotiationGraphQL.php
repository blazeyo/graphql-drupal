<?php

namespace Drupal\graphql\Plugin\WorkspaceNegotiation;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\graphql\GraphQLWorkspaceContext;
use Drupal\graphql\Plugin\SchemaPluginManager;
use Drupal\workspaces\Negotiator\WorkspaceNegotiatorInterface;
use Drupal\workspaces\WorkspaceInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * The
 */
class WorkspaceNegotiationGraphQL implements WorkspaceNegotiatorInterface {

  /**
   * The cache key for schema definitions.
   *
   * @var string
   */
  static $SCHEMAS_KEY = 'graphql:workspace_negotiator:schema_definitions';

  /**
   * The graphql workspace context.
   *
   * @var \Drupal\graphql\GraphQLWorkspaceContext
   */
  protected $workspaceContext;

  /**
   * The graphql schema plugin manager.
   *
   * @var \Drupal\graphql\Plugin\SchemaPluginManager
   */
  protected $schemaPluginManager;

  /**
   * The static cache.
   *
   * @var \Drupal\Core\Cache\CacheBackendInterface
   */
  protected $staticCache;

  /**
   * Constructor.
   *
   * @param \Drupal\graphql\GraphQLWorkspaceContext $workspaceContext
   * @param \Drupal\graphql\Plugin\SchemaPluginManager $schemaPluginManager
   * @param \Drupal\Core\Cache\CacheBackendInterface $staticCache
   */
  public function __construct(GraphQLWorkspaceContext $workspaceContext, SchemaPluginManager $schemaPluginManager, CacheBackendInterface $staticCache) {
    $this->workspaceContext = $workspaceContext;
    $this->schemaPluginManager = $schemaPluginManager;
    $this->staticCache = $staticCache;

    // The definitions need to be set in the constructor to avoid an infinite loop.
    $this->staticCache->set(self::$SCHEMAS_KEY, $this->schemaPluginManager->getDefinitions());
  }

  /**
   * {@inheritdoc}
   */
  public function applies(Request $request) {
    $definitions = $this->staticCache->get(self::$SCHEMAS_KEY) ?: [];
    foreach ($definitions as $definition) {
      $t=1;
    }
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function getActiveWorkspace(Request $request) {
    return $this->workspaceContext->getCurrentWorkspace();
  }

  /**
   * {@inheritdoc}
   */
  public function setActiveWorkspace(WorkspaceInterface $workspace) {

  }

}
