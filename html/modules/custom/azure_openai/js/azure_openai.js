(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.azureOpenAI = {
    attach: function (context, settings) {

      var errorMessageDiv = $('#error-message');
      var inputField = $('input#edit-user-prompt');

      // Move the error message below the field label and above the input field.
      errorMessageDiv.insertBefore(inputField);

      $("#azure-openai-chat-form button.js-form-submit", context).click(function(e) {
        var text = inputField.val();

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