/**
 *admin custom js
 */

$(document).ready(function() {

    // custom relations
    if ($('#customRelationContainer').length) {

        /**
         * enable or disable param options for custom relations
         * - enable if param type is 'options'
         * - disable if param type is 'text'
         *
         * @param {jQuery Object} $paramType jQuery object of a select.js-params-type
         * @return {void}
         */
        var toggleParamOptions = function($paramType) {
            var $tr = $paramType.parents('tr:first');
            var $options = $tr.find('input.js-params-options');
            if ($paramType.val() == 'text') {
                $options.prop('disabled', true);
            } else {
                $options.prop('disabled', false);
            }
        }

        openAtStart('table[id]');
        $('input.js-del-relation').click(function() {
            if (!confirm(messageDel)) {
                return false ;
            }
            var customId = $(this).prop('title');
            $(this).parents('form:first').prop('action', urlDelete).submit();
            return false;
        });

        $('#customRelationContainer').find('select.js-params-type').each(function() {
            toggleParamOptions($(this));
        });

        $('#customRelationContainer').find('select.js-params-type').change(function() {
            toggleParamOptions($(this));
        });
    }
});
