<?php

namespace Drupal\custom_issue_tracking\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'Latest Assigned Issues' block.
 *
 * @Block(
 *   id = "latest_assigned_issues_block",
 *   admin_label = @Translation("Latest Assigned Issues"),
 * )
 */
class LatestAssignedIssuesBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * Constructs a new LatestAssignedIssuesBlock.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The current user.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager, AccountInterface $current_user) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entity_type_manager;
    $this->currentUser = $current_user;
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
      $container->get('current_user')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $issues = $this->getLatestAssignedIssues();

    // Build the block content.
    $build = [];

    if (!empty($issues)) {
      foreach ($issues as $issue) {
        $build['issue_' . $issue->id()] = [
          '#markup' => $this->t('Issue Title: @title', ['@title' => $issue->getTitle()]),
        ];
      }
    }
    else {
      $build['#markup'] = $this->t('No assigned issues found.');
    }

    return $build;
  }

  /**
   * Helper function to get the latest 3 issues assigned to the current user.
   *
   * @return \Drupal\node\NodeInterface[]
   *   An array of issue nodes.
   */
  protected function getLatestAssignedIssues() {
    // To retrieve the latest 3 issues assigned to the current user.
    $query = $this->entityTypeManager->getStorage('node')
      ->getQuery()
      ->condition('type', 'issue')
      ->condition('field_assignee.target_id', $this->currentUser->id())
      ->sort('created', 'DESC')
      ->range(0, 3)
      ->accessCheck(FALSE);
    $issue_ids = $query->execute();

    return $this->entityTypeManager->getStorage('node')->loadMultiple($issue_ids);
  }

}
