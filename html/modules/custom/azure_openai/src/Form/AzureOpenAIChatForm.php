<?php

namespace Drupal\azure_openai\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\azure_openai\Service\AzureOpenAIService;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\filter\FilterPluginCollection;

class AzureOpenAIChatForm extends FormBase {

  protected $azureOpenAIService;

  public function __construct(AzureOpenAIService $azureOpenAIService) {
    $this->azureOpenAIService = $azureOpenAIService;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('azure_openai.chat_completions')
    );
  }

  public function getFormId() {
    return 'azure_openai_chat_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    // AJAX wrapper.
    $form['markup'] = [
      '#markup' => '<div id="cinder-chatbot-wrapper"></div><div id="error-message"></div>',
    ];

    $form['important'] = [
      '#type' => 'textfield',
      '#attributes' => [
        'class' => [
          'important-field',
          'hidden'
        ]
      ],
    ];

    $form['user_prompt'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Ask a question'),
      '#description' => $this->t('Please provide a question with a maximum of 128 characters.'),
      '#required' => TRUE,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
      '#ajax' => [
        'callback' => [$this, 'ajaxSubmitCallback'],
        'event' => 'click',
        'method'=>'replace',
        'effect'=>'fade',
      ],
    ];

    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $form_state->setRebuild();
  }

  public function ajaxSubmitCallback(array &$form, FormStateInterface $form_state) {
    // Clear any existing messages.
    \Drupal::messenger()->deleteAll();

    // Get user prompt.
    $user_prompt = $form_state->getValue('user_prompt');
    $honeypot = $form_state->getValue('important');

    if (!empty($honeypot)) {
      // Create an AJAX response.
      $response = new AjaxResponse();

      // Show an error message.
      $error_message = '<div class="wrapper"><strong id="title1-error" class="error"><span class="label label-danger"><span class="prefix">' . t('Error') . '&nbsp;1: </span>' . t('Spam submission detected.') . '</span></strong></div>';
      $response->addCommand(new HtmlCommand('#error-message', $error_message));

      return $response;
    }

    if (empty($user_prompt)) {
      // Create an AJAX response.
      $response = new AjaxResponse();

      // Show an error message.
      $error_message = '<div class="wrapper"><strong id="title1-error" class="error"><span class="label label-danger"><span class="prefix">' . t('Error') . '&nbsp;1: </span>' . t('This field is required.') . '</span></strong></div>';
      $response->addCommand(new HtmlCommand('#error-message', $error_message));

      return $response;
    }

    // Call the service to make the Azure OpenAI request.
    $azure_response = $this->azureOpenAIService->makeAzureOpenAICall($user_prompt);

    // The text processing filters service.
    $manager = \Drupal::service('plugin.manager.filter');
    $filter_collection = new FilterPluginCollection($manager, []);
    $filter = $filter_collection->get('filter_url');
    // Applying filter to convert links.
    $result = _filter_url($azure_response, $filter);

    $output = '<div class="group">';
    $output .= '<div class="prompt">' . $user_prompt . '</div>';
    $output .= '<div class="response">' . $result . '</div>';
    $output .= '</div>';

    // Create an AJAX response.
    $response = new AjaxResponse();

    // Update the content of the specified <div>.
    $response->addCommand(new HtmlCommand('#cinder-chatbot-wrapper', $output));

    return $response;
  }

}
