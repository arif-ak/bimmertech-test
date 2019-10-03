(function ($) {
    'use strict';

    function slugify(text)
    {
        return text.toString().toLowerCase()
            .replace(/\s+/g, '-')           // Replace spaces with -
            .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
            .replace(/\-\-+/g, '-')         // Replace multiple - with single -
            .replace(/^-+/, '')             // Trim - from start of text
            .replace(/-+$/, '');            // Trim - from end of text
    }

    $.fn.extend({
        pageSlugGenerator: function () {
            let timeout;

            $('#app_page_title').on('input', function() {
                clearTimeout(timeout);
                let element = $(this);

                timeout = setTimeout(function() {
                    updateSlug(element);
                }, 1000);
            });

            $('.toggle-page-slug-modification').on('click', function(e) {
                e.preventDefault();
                toggleSlugModification($(this), $(this).siblings('input'));
            });

            function updateSlug(element) {
                let slugInput = $('#app_page_slug');
                let loadableParent = slugInput.parents('.field.loadable');

                if ('readonly' === slugInput.attr('readonly')) {
                    return;
                }

                loadableParent.addClass('loading');

                slugInput.val( slugify(element.val()));
            }

            function toggleSlugModification(button, slugInput) {
                if (slugInput.attr('readonly')) {
                    slugInput.removeAttr('readonly');
                    button.html('<i class="unlock icon"></i>');
                } else {
                    slugInput.attr('readonly', 'readonly');
                    button.html('<i class="lock icon"></i>');
                }
            }
        }
    });
})(jQuery);

(function($) {
    $(document).ready(function () {
        $(this).pageSlugGenerator();
    });
})(jQuery);
