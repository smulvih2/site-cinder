<?php

namespace Drupal\azure_openai\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\azure_openai\Service\AzureOpenAIService;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;

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
      '#markup' => '<div id="cinder-chatbot-wrapper"></div>',
    ];

    $form['user_prompt'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Ask a question'),
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
    // Get user prompt.
    $user_prompt = $form_state->getValue('user_prompt');

    // Call the service to make the Azure OpenAI request.
    $azure_response = $this->azureOpenAIService->makeAzureOpenAICall($user_prompt);

    $output = '<div class="group">';
    $output .= '<div class="prompt">' . $user_prompt . '</div>';
    $output .= '<div class="response">' . $azure_response . '</div>';
    $output .= '</div>';

    // Create an AJAX response.
    $response = new AjaxResponse();

    // Update the content of the specified <div>.
    $response->addCommand(new HtmlCommand('#cinder-chatbot-wrapper', $output));

    return $response;
  }

}
