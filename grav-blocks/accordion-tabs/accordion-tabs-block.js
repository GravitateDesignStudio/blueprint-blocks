jQuery(document).ready(function($){

    if($('.block-accordion-tabs').length) {
        $('.block-accordion-tabs').each(function() {
            // var block_index = $(this).closest('.block-container').attr('data-block-index');

            $('.block-accordion-tabs__tab-list li').each(function() {
                $(this).on('click', function(e) {
                    var target = $(this).attr('data-target');
                    $(this).addClass('active');
                    $(this).siblings('li').removeClass('active');

                    $('#' + target).addClass('active');
                    $('#' + target).siblings('.block-accordion-tabs__item').removeClass('active');
                });
            });
            $('.block-accordion-tabs__item .block-accordion-tabs__item--title').each(function() {
                $(this).on('click', function(e) {
                    var target = $(this).parent('.block-accordion-tabs__item'),
                        sectionName = $(target).attr('id');
                    $(target).addClass('active');
                    $(this).parent().siblings('.block-accordion-tabs__item').removeClass('active');

                    $('.block-accordion-tabs__tab-list li[data-target=' + sectionName + ']').addClass('active');
                    $('.block-accordion-tabs__tab-list li[data-target=' + sectionName + ']').siblings('li').removeClass('active');
                });
            });

        })

    }

});
