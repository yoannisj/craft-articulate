;(function(window, $, undefined)
{
    var Articulate = window.Articulate || {};
    window.Articulate = Articulate;

    // =Helpers
    // =============================================================================

    /**
     * Helper to retry getting a result from an anonymous function.
     *
     * **Note**: Given `fn` is considered successfull as soon as it returns something
     * different from `undefined`
     *
     * @param {Function} fn Anonymous function to retry
     * @param {Function} onSuccess Function called with the fn results upon success
     * @param {Function} onFail Function called upon failure
     * @param {integer} retryInterval Time (in ms) between each retry
     * @param {integer} maxRetries Maximum retries to run
     *
     */

    function runAndRetry(fn, onSuccess, onFail, retryInterval, maxRetries)
    {
    if (!retryInterval) retryInterval = 300;
    if (!maxRetries) maxRetries = 20;

    let retry = 0, result, hasResult;
    let ticker = setInterval(function()
    {
        // run function to grab the component once again
        result = fn();
        hasResult = (typeof result !== 'undefined');

        // if successful, or we reached the timeout
        if (hasResult || ++retry > maxRetries)
        {
        clearInterval(ticker); // stop retrying

        // execute callbacks
        if (hasResult) {
            if (onSuccess) onSuccess(result);
        } else if (onFail) {
            onFail();
        }
        }
    }, retryInterval);
    }

    // =Adapters
    // =============================================================================

    // =BaseFieldAdapter
    // ----------------------------------------------------------------------------

    let BaseFieldAdapter = {

        construct: function(form, handle, fieldId, inputName)
        {
            this.handle = handle;
            this.fieldId = fieldId;
            this.inputName = inputName;
            this.inputComponent = null;

            // get refernece to elements
            this.$form = form instanceof $ ? form : $(form);
            this.form = this.$form.get(0);

            this.$field = this.$form.find('#'+fieldId);
            this.field = this.$field.get(0);

            this.$input = this.$field.find('[name="'+inputName+'"]');
            this.input = this.$input.get(0);
        },

        /**
         * Builds adapted field's config for DyamicForms
         *
         * @see https://simomosi.github.io/dynamic-forms/configurations/field-configuration/
         *
         * @returns {Object}
         */

        makeConfig: function()
        {
            this.initInput();

            return {
            name: this.inputName,
            io: this.getFieldio(),
            };
        },

        /**
         * Returns adapted field's `io` config setting
         * @see https://simomosi.github.io/dynamic-forms/configurations/field-configuration/#io
         *
         * @returns {object}
         */

        getFieldIo: function()
        {
            // make sure we can access the input component and
            // trigger custom change events
            if (!this.isInputInitialized) {
            this.initInput();
            }

            let adapter = this;

            return {
            // the adapter's `initComponent` method should implement the triggering
            // of this event on the field's input element.
            event: 'componentChange',
            get: function(input) {
                return adapter.getValue();
            },
            set: function(input, value) {
                return adapter.setValue(value);
            }
            };
        },

        /**
         *
         */

        initInput: function(grabInterval, maxGrabRetries)
        {
            let adapter = this;
            let container = this.getComponentElement();

            if (!container) { // no can do..
            throw new Error("Could not find field's custom component element");
            }

            let tryGab = function() {
            return adapter.getComponentInstance(container);
            };

            let onSuccess = function(component) {
            adapter.inputComponent = component;
            adapter.initComponent(component);
            adapter.isInputInitialized = true;
            };

            let onFail = function() {
            throw new Error("Could not access field's custom component");
            }

            runAndRetry(tryGab, onSuccess, onFail, grabInterval, maxGrabRetries);
        },

        /**
         * Returns HTML element on which to access the field's component
         *
         * @returns {Node}
         */

        getComponentElement: function()
        {
            return this.input;
        },

        /**
         * Returns the adapted field's custom component instance
         *
         * @param {Node} container HTML element returned by `getComponentElement()`
         */

        getComponentInstance: function(container)
        {
            throw new Error("Field adapter's `grabComponent()` method implementation missing");
        },

        /**
         * Initializes adapted field's custom component.
         * This method is responsible for implementing the triggering of a custom
         * 'componentChange' DOM event on the input when the field's value changes.
         *
         * @param {*} component
         */

        initComponent: function(component)
        {
            throw new Error("Field adapter's `initComponent()` method implementation missing");
        },

        /**
         * Returns the adapted field's current value. You can most probably do
         * this via the `inputComponent` property, which stores a reference of
         * what was returned by `getComponentInstance()`
         *
         * **Note**: by default `getFieldIo()` will map the field's `io.get` setting
         * to this method.
         *
         * @return {*}
         */

        getValue: function()
        {
            throw new Error("Field adapter's `getValue()` method implementation missing");
        },

        /**
         * Method used to update the adapted field's value. You can most probably do
         * this via the `inputComponent` property, which stores a reference of
         * what was returned by `getComponentInstance()`
         *
         * **Note**: by default `getFieldIo()` will map the field's `io.get` setting
         * to this method.
         *
         * @param {*} value
         */

        setValue: function(value)
        {
            throw new Error("Field adapter's `setValue()` method implementation missing");
        },

    };

    // =Export
    // =============================================================================

    Articulate.BaseFieldAdapter = BaseFieldAdapter;

})(window, jQuery);