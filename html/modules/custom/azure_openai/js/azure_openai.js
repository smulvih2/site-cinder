(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.azureOpenAI = {
    attach: function (context, settings) {

      $("#azure-openai-chat-form button.js-form-submit", context).click(function(e) {
        var text = $('input#edit-user-prompt').val();

        if (text) {
          // Remove value from input on submit.
          $('input#edit-user-prompt').val('');
        }
      });

    }
  };
})(jQuery, Drupal, drupalSettings);