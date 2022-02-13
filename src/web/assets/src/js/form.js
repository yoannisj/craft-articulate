;(function(window, $, undefined)
{
    console.log('Articulate', window.Articulate);

    var Articulate = window.Articulate || {};
    window.Articulate = Articulate;

    var form = document.querySelector('#main-form');

    function Form( form, rules )
    {
        rules;
    }

    Object.assign( Form.prototype, {

        /**
         * @returns {object}
         */

        makeConfig: function()
        {
            var fields = [], fieldRules = [];            

            return {
                fields: fields,
                rules: fieldRules,
            };
        },

        /**
         * @returns {object}
         */

        getData: function()
        {
            return {};
        },

        /**
         * 
         * @param {*} name 
         */

        getInputRules: function( name )
        {

        },

        /**
         * 
         * @param {string} name 
         * @param {Node} input 
         * @param {object} data 
         * 
         * @param {string} subject 
         */

        updateField: function( name, input, data, subjectName )
        {
            // way to go: check if `name` has "disable" or "hide" effect,
            //  then check rule's disable/hide conditions in 'data'

            var rules = this.getInputRules(name),
                rule;

            for (var ri = 0, rln = rules.length; ri<rln; ri++)
            {
                rule = rules[ri];
                if (rule.checkConditions(data))
                {
                    // apply effect
                }
            }
        },

    });



})(window, jQuery);