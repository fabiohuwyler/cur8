(function($) {
    'use strict';

    $(document).ready(function() {
        $('.wptext-update').each(function() {
            $(this).css('opacity', '0');
            $(this).animate({opacity: 1}, 600);
        });

        $(document).on('click', '.wptext-update-image img', function(e) {
            e.preventDefault();
            const imgSrc = $(this).attr('src');
            
            const modal = $('<div class="wptext-image-modal"></div>');
            const modalContent = $('<div class="wptext-modal-content"></div>');
            const modalImg = $('<img src="' + imgSrc + '" alt="">');
            const closeBtn = $('<span class="wptext-modal-close">&times;</span>');
            
            modalContent.append(closeBtn);
            modalContent.append(modalImg);
            modal.append(modalContent);
            
            $('body').append(modal);
            
            setTimeout(function() {
                modal.addClass('active');
            }, 10);
            
            modal.on('click', function(e) {
                if ($(e.target).hasClass('wptext-image-modal') || $(e.target).hasClass('wptext-modal-close')) {
                    modal.removeClass('active');
                    setTimeout(function() {
                        modal.remove();
                    }, 300);
                }
            });
        });
    });

})(jQuery);
