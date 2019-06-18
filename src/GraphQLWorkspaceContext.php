<?php

namespace Drupal\graphql;

use Drupal\workspaces\WorkspaceManagerInterface;

/**
 * Simple service that stores the current GraphQL workspace state.
 */
class GraphQLWorkspaceContext {

  /**
   * Indicates if the GraphQL context language is currently active.
   *
   * @var bool
   */
  protected $isActive;

  /**
   * The current language context.
   *
   * @var string
   */
  protected $currentWorkspace;

  /**
   * @var \SplStack
   */
  protected $workspaceStack;

  /**
   * The language manager service.
   *
   * @var WorkspaceManagerInterface
   */
  protected $workspaceManager;

  /**
   * GraphQLLanguageContext constructor.
   *
   * @param \Drupal\workspaces\WorkspaceManagerInterface $workspaceManager
   *   The workspace manager service.
   */
  public function __construct(WorkspaceManagerInterface $workspaceManager) {
    $this->workspaceManager = $workspaceManager;
    $this->workspaceStack = new \SplStack();
  }

  /**
   * Retrieve the current language.
   *
   * @return string|null
   *   The current language code, or null if the context is not active.
   */
  public function getCurrentWorkspace() {
    return $this->isActive
      ? ($this->workspaceManager->getActiveWorkspace())
      : NULL;
  }

  /**
   * Executes a callable in a defined language context.
   *
   * @param callable $callable
   *   The callable to be executed.
   * @param string $language
   *   The langcode to be set.
   *
   * @return mixed
   *   The callables result.
   *
   * @throws \Exception
   *   Any exception caught while executing the callable.
   */
  public function executeInLanguageContext(callable $callable, $language) {
    $this->languageStack->push($this->currentWorkspace);
    $this->currentWorkspace = $language;
    $this->isActive = TRUE;
    $this->workspaceManager->reset();
    // Extract the result array.
    try {
      return call_user_func($callable);
    }
    catch (\Exception $exc) {
      throw $exc;
    }
    finally {
      // In any case, set the language context back to null.
      $this->currentWorkspace = $this->languageStack->pop();
      $this->isActive = FALSE;
      $this->workspaceManager->reset();
    }
  }

}
