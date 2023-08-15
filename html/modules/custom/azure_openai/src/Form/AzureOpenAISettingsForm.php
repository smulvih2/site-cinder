<?php

namespace Drupal\azure_openai\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class AzureOpenAISettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'azure_openai_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'azure_openai.config',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('azure_openai.config');

    // Connection settings (https://learn.microsoft.com/en-us/azure/ai-services/openai/reference).
    $form['connection'] = [
      '#type' => 'details',
      '#title' => t('Connection settings'),
      '#description' => t('Connection settings used to connect to Azure OpenAI.'),
      '#open' => TRUE,
    ];

    $form['connection']['resource_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Resource Name'),
      '#default_value' => $config->get('resource_name') ? $config->get('resource_name') : '',
      '#required' => TRUE,
    ];

    $form['connection']['deployment_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Deployment ID'),
      '#default_value' => $config->get('deployment_id') ? $config->get('deployment_id') : '',
      '#required' => TRUE,
    ];

    $form['connection']['api_version'] = [
      '#type' => 'textfield',
      '#title' => $this->t('API Version'),
      '#default_value' => $config->get('api_version') ? $config->get('api_version') : '',
      '#required' => TRUE,
    ];

    $form['connection']['api_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('API Key'),
      '#default_value' => $config->get('api_key') ? $config->get('api_key') : '',
      '#required' => TRUE,
    ];

    // Prompt settings.
    $form['prompt'] = [
      '#type' => 'details',
      '#title' => t('Prompt settings'),
      '#description' => t('Prompt settings to optimize OpenAI responses.'),
      '#open' => TRUE,
    ];

    $form['prompt']['system_context'] = [
      '#type' => 'textarea',
      '#title' => $this->t('System Context'),
      '#default_value' => $config->get('system_context') ? $config->get('system_context') : '',
      '#required' => TRUE,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config('azure_openai.config')
      ->set('resource_name', $form_state->getValue('resource_name'))
      ->set('deployment_id', $form_state->getValue('deployment_id'))
      ->set('api_version', $form_state->getValue('api_version'))
      ->set('api_key', $form_state->getValue('api_key'))
      ->set('system_context', $form_state->getValue('system_context'))
      ->save();
  }
}
