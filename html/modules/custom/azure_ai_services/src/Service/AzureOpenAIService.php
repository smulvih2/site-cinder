<?php

namespace Drupal\azure_ai_services\Service;

use GuzzleHttp\Client;
use Drupal\Core\Config\ConfigFactoryInterface;

class AzureOpenAIService {

  protected $configFactory;

  public function __construct(ConfigFactoryInterface $configFactory) {
    $this->configFactory = $configFactory;
  }

  public function makeAzureOpenAIChatCompletion($user_prompt) {
    // Fetch Azure OpenAI configuration from Drupal configuration.
    $config = $this->configFactory->get('azure_openai.config');
    $resource_name = $config->get('resource_name');
    $deployment_id = $config->get('deployment_id');
    $api_version = $config->get('api_version');
    $api_key = $config->get('api_key');
    $system_context = $config->get('system_context');
    $endpoint = 'https://' . $resource_name . '.openai.azure.com/openai/deployments/' . $deployment_id . '/chat/completions?api-version=' . $api_version;

    $client = new Client();

    // Prepare the request data.
    $request_data = [
      'messages' => [
        ["role" => "assistant", "content" => $system_context],
        ["role" => "user", "content" => $user_prompt],
      ],
    ];

    $headers = [
      'Content-Type' => 'application/json',
      'api-key' => $api_key,
    ];

    try {
      $response = $client->post($endpoint, [
        'headers' => $headers,
        'json' => $request_data,
      ]);

      $response_body = $response->getBody()->getContents();
      $response = json_decode($response_body, TRUE)['choices'][0]['message']['content'];

      return $response;
    }
    catch (\Exception $e) {
      // Handle error gracefully.
      \Drupal::logger('azure_openai')->error('Azure OpenAI request failed: @message', ['@message' => $e->getMessage()]);
      return NULL;
    }
  }
}
