document.addEventListener('DOMContentLoaded', function () {
    if (typeof $ === 'undefined') {
        return;
    }

    $('.block-image-gallery').each(function () {
        if (typeof Swiper !== 'undefined') {
            var $swiperContainer = $(this).find('.swiper-container').first();
            var swiperInstance = new Swiper($swiperContainer, {
                loop: true,
                autoHeight: true,
                slidesPerView: 1,
                observer: true,
                navigation: {
                    nextEl: $swiperContainer.find('.swiper-button-next').first(),
                    prevEl: $swiperContainer.find('.swiper-button-prev').first()
                },
                pagination: {
                    el: $swiperContainer.find('.swiper-pagination').first(),
                    type: 'bullets',
                    clickable: true
                }
            });
        }

        // Gallery Images Popup with Colorbox
        if ($.colorbox) {
            $('.block-image-gallery .image-gallery__format--gallery').each(function (index) {
                var block_index = $(this).closest('.block-container').attr('data-block-index');
                $(this).find('.image-gallery__link--'+block_index).colorbox({rel:'image-gallery__link--'+block_index, width: '90%', height: '90%'});
            });
        }
    });
});
