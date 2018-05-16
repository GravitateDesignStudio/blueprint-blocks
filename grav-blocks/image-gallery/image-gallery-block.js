jQuery(document).ready(function($){

    // Gallery Slider with Swiper
    // TODO: Decide if we load swiper here
    if(typeof(Swiper) !== 'undefined')
    {
        console.log('has Swiper');
    }
    // $('.block-image-gallery .swiper-container').each(function(index){
    //     var block_index = $(this).closest('.block-container').attr('data-block-index');
    //     var prev = $(this).find('.swiper-button-prev');
    //     var next = $(this).find('.swiper-button-next');
    //     var pagination = $(this).find('.swiper-pagination');
    //     var swiperTestimonials = new Swiper($(this), {
    //         loop: true,
    //         autoHeight: true,
    //         slidesPerView: 1,
    //         observer: true,
    //         navigation: {
    //             nextEl: next,
    //             prevEl: prev,
    //         },
    //         pagination: {
    //             el: pagination,
    //             type: 'bullets',
    //             clickable: true,
    //         },
    //     });
    // });

    // Gallery Images Popup with Colorbox
    if(jQuery().colorbox)
    {
        $('.block-image-gallery .image-gallery__format--gallery').each(function(index)
        {
            var block_index = $(this).closest('.block-container').attr('data-block-index');
            $(this).find('.image-gallery__link--'+block_index).colorbox({rel:'image-gallery__link--'+block_index, width: '90%', height: '90%'});
        });
    }


});
