(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.azureOpenAI = {
    attach: function (context, settings) {

      $("#azure-openai-chat-form button.js-form-submit", context).click(function(e) {
        var inputField = $('input#edit-user-prompt');
        var text = inputField.val();
        var errorMessageDiv = $('#error-message');

        // Remove error message.
        errorMessageDiv.empty();

        if (text) {
          // Remove value from input on submit.
          $('input#edit-user-prompt').val('');
        }
      });

    }
  };
})(jQuery, Drupal, drupalSettings);