jQuery(document).ready(function($)
{

    // NOTE gravBlockData is from localized script passed from php in gravitate-blocks.php

    gravBlocks = {};

    gravBlocks.init = function(){
        // Loop through all block list "popup" templates
        //
        // These are the dropdown lists that appear when clicking "Add Content"
        // in a flexible field section
        $('.tmpl-popup').each(function () {
            var $gravpopup = $($(this).html());

            // loop through blocks and add additional choices if necessary
            $.each(gravBlockData, function (index, value) {
                var blockName = index;
            });

            // update the "popup" tempate HTML
            try {
                $(this).html($gravpopup[0].outerHTML);
            } catch (e) {
                // safety net to prevent an uncaught error if $gravpopup is undefined
            }
        });

        gravBlocks.setClick();
    }

    gravBlocks.setClick = function(){
        $('[data-event="add-layout"]').on('click touch', function(e){
            $(document).on('click touch', e, gravBlocks.addFormat);
        });
    }

    gravBlocks.addFormat = function(e){

        if(e.target.dataset.layout){
            $(document).off('click touch', gravBlocks.addFormat);
        }
    }

    gravBlocks.init();
});
