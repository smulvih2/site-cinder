/*(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.azureOpenAIAppendContent = {
    attach: function (context, settings) {
      // Listen for the AJAX success event.
      $(document).ajaxSuccess(function (event, xhr, settings) {
        var data = xhr.responseJSON;

        if (data && data.content) {
          // Append the new content to the specified <div>.
          $('#cinder-chatbot-wrapper', context).append(data.content);
        }
      });
    }
  };
})(jQuery, Drupal, drupalSettings);*/