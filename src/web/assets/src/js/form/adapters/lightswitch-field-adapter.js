;(function(window, $, undefined)
{
    var Articulate = window.Articulate || {};
    window.Articulate = Articulate;

    var BaseFieldAdapter = Articulate.BaseFieldAdapter;

    // =LightswitchFieldAdapter
    // =============================================================================

    /**
     * Constructor for LightswitchFieldAdapter
     *
     * @param {Node|jQuery} form Form element the field belongs to
     * @param {string} handle The field's handle (i.e. in Craft-CMS)
     * @param {string} id Html id of field input element (namespaced)
     * @param {string} name Name of field input element (namespaced)
     */

    function LightswitchFieldAdapter( form, handle, fieldId, inputName )
    {
        BaseFieldAdapter.construct.call(this, form, handle, fieldId, inputName);
    }

    /* include BaseFieldAdapter properties and methods */
    Object.assign(LightswitchFieldAdapter.prototype, BaseFieldAdapter);

    /**
     *
     */

    LightswitchFieldAdapter.prototype.getComponentElement = function()
    {
        return this.field.querySelector('.lightswitch');
    };

    /**
     *
     */

    LightswitchFieldAdapter.prototype.getComponentInstance = function(container)
    {
        return $(container).data('lightswitch');
    };

    /**
     *
     */

    LightswitchFieldAdapter.prototype.initComponent = function(component)
    {
        // register component
        this.inputComponent = component;

        // override the lightswitch's `onChange` hook to inject our own logic
        let adapter = this;
        let currentOnChange = component.settings.onChange;

        component.settings.onChange = function()
        {
            if (currentOnChange && typeof currentOnChange == 'function') {
            currentOnChange.call(component.settings, component.on);
            }

            // trigger DOM event
            adapter.input.dispatchEvent(new CustomEvent('componentChange', {
            bubbles: false,
            detail: {
                component: adapter.inputComponent,
                value: adapter.getValue()
            },
            }));
        };
    };

    /**
     *
     */

    LightswitchFieldAdapter.prototype.getValue = function()
    {
        if (!this.inputComponent) return null;
        return this.inputComponent.on;
    };

    /**
     *
     */

    LightswitchFieldAdapter.prototype.setValue = function(value)
    {
        if (!this.inputComponent) return;
        this.inputComponent.on = !!(value);
    };

    // =LightswitchFieldAdapter
    // =============================================================================

    Articulate.BaseFieldAdapter = LightswitchFieldAdapter;

})(window, jQuery);