;(function(window, undefined)
{
    var Articulate = window.Articulate || {};
    window.Articulate = Articulate;

    // =Helpers
    // =============================================================================

    // =Conditions
    // -----------------------------------------------------------------------------

    var conditionTests = {
        'empty': Articulate.assertions.isEmpty,
        'equal': Articulate.assertions.isEqual,
        'notEqual': Articulate.assertions.isNotEqual,
        'oneOf': Articulate.assertions.oneOf,
    };

    /**
     * @param {Array|null} tokens 
     * 
     * @returns {FormCondition}
     */

    function parseCondition( tokens )
    {
        var parsed = {
            isNull: false,
            operator: null,
            conditions: null,
            subject: null,
            test: null,
            given: null,
        };

        if (tokens === null) {
            parsed.isNull = true;
            return parsed;
        }

        else if (!Array.isArray(tokens)) {
            throw new Error('parseCondition expects `null` or an array of tokens');
        }

        // must starts with one of: 'and', 'or', an array or a subject name/path
        var firstToken = tokens[0];

        if (Array.isArray(firstToken))
        {
            // just one condition without operator => parse that condition
            if (tokens.length == 1) {
                return parseCondition(firstToken);
            }

            else // multiple conditions without operator => default to 'and' operator
            {
                firstToken = 'and';
                tokens.unshift(firstToken);
            }
        }

        if (firstToken === 'and' || firstToken == 'or')
        {
            if (tokens.length < 3) {
                throw new Error("Conditional operators require at least 2 more tokens");
            }

            parsed.operator = firstToken;
            parsed.conditions = [];

            for (var i = 1, ln = tokens.length; i<ln; i++) {
                parsed.conditions.push(parseCondition(tokens[i]));
            }
        }

        else if (!(typeof firstToken == 'string')) {
            throw new Error('First token in condition must be a string');
        }

        if (tokens.length < 2) {
            throw new Error("Form conditions can not include less than 2 tokens");
        }

        if (tokens.length > 3) {
            throw new Error("Form conditions can not include more than 3 tokens");
        }

        // second token is the condition's test
        var test = tokens[1],
            given = token[2] || null;

        if (!(test in conditionTests)) {
            throw new Error("Unknown form condition test '" + test + "'");
        }

        if (test == 'empty' && tokens.length == 3) {
            throw new Error("Form condition can not perform `empty` test against a given value");
        }

        // validate last token based on given test
        if ((test === 'oneOf' || test === 'allOf')
            && !Array.isArray(given))
        {
            throw new Error("Form condition test `" + test + "` must be ran against an array of given values");
        }

        parsed.subject = firstToken;
        parsed.test = test;
        parsed.given = given;

        return parsed;
    };

    // =FormCondition
    // =============================================================================

    /**
     * @param {Array|null} tokens Condition tokens
     */

    function FormCondition( tokens )
    {
        if (tokens && !Array.isArray(tokens)) {
            throw new Error("FormCondition constructor expects an array of tokens");
        }

        var parsed = parseCondition(tokens);

        this.isNull = parsed.operator;
        this.operator = parsed.operator;
        this.conditions = parsed.conditions;
        this.subject = parsed.subject;
        this.test = parsed.test;
        this.given = parsed.conditions;
    }

    Object.assign(FormCondition.prototype, {

        /**
         * 
         * @returns {bool}
         */

        resolve: function( data )
        {
            // no condition? => always resolve to `true`
            if (this.isNull) return true;

            if (this.operator == 'and')
            {
                // if any sub-condition does not resolve
                for (var i = 0, ln = this.conditions.length; i<ln; i++)
                {
                    if (!this.conditions[i].resolve(data)) {
                        return false; // than, this condition does not resolve
                    }
                }

                // all sub-conditions resolved
                return true; // so this one resolves too!
            }

            else if (this.operator == 'or')
            {
                // if any sub-condition resolves
                for (var i = 0, ln = this.conditions.length; i<ln; i++)
                {
                    if (this.conditions[i].resolve(data)) {
                        return true; // than, this condition resolves too
                    }
                }

                // none of sub-conditions resolved
                return false; // so this one also does not resolve!
            }

            var test = conditionTests[this.test];

            if (!test) {
                throw new Error("Can not resolve unknown FormCondition test '" + this.test + "'");
            }

            var value = this.getTestedValue(data);

            return test.call(value, given, true); // form condition tests perform loose comparisons
        },

        /**
         * 
         */

        getTestedValue: function()
        {
            this.subject;

            return null;
        },

    });

    // =FormRule
    // =============================================================================

    /**
     * @param {object} config
     */

    function FormRule( config )
    {
        this.effect = config.effect;
        this.field = config.field;
        this.value = config.value || null;
        this.when = config.when || null;

        this.init();
    }

    Object.assign(FormRule.prototype, {

        /**
         * Initializes FormRule functionality
         */

        init: function()
        {
            if (!this.effect) {
                throw new Error("Missing required FormRule `effect` setting");
            }
        
            if (!this.field) {
                throw new Error("Missing required FormRule `field` setting");
            }

            if (!(this.when instanceof FormCondition)) {
                this.when = parseCondition(this.when);
            }
        },

        /**
         * Check's whether FormRule condition applies for given Form data
         * 
         * @param {object} data Form data against which to check rule's condition
         * 
         * @returns {bool}
         */

        checkCondition: function(data) {
            return this.when.resolve(data);
        },

        /**
         * 
         */

        applyEffect: function()
        {

        },

    });

    // =Export
    // =============================================================================

    Articulate.FormRule = FormRule;

})(window);