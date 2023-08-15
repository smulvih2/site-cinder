<?php

namespace Drupal\azure_openai\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Provides a chat block to ask questions.
 *
 * @Block(
 *   id = "azure_openai_chat_block",
 *   admin_label = @Translation("Azure OpenAI Chat Block"),
 *   category = @Translation("Azure OpenAI"),
 * )
 */
class AzureOpenAIChatBlock extends BlockBase implements BlockPluginInterface {

  public function build() {
    $form = \Drupal::formBuilder()->getForm('Drupal\azure_openai\Form\AzureOpenAIChatForm');
    return $form;
  }

  public function blockAccess(AccountInterface $account) {
    return AccessResult::allowedIfHasPermission($account, 'use azure openai chat form');
  }

}
