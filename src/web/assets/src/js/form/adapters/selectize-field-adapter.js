;(function(window, undefined)
{
    var Articulate = window.Articulate || {};
    window.Articulate = Articulate;

    var BaseFieldAdapter = Articulate.BaseFieldAdapter;

    // =SelectizeFieldAdapter
    // =============================================================================

    /**
     * Constructor for SelectizeFieldAdapter
     *
     * @param {Node|jQuery} form Form element the field belongs to
     * @param {string} handle The field's handle (i.e. in Craft-CMS)
     * @param {string} id Html id of field input element (namespaced)
     * @param {string} name Name of field input element (namespaced)
     */

    function SelectizeFieldAdapter( form, handle, fieldId, inputName )
    {
        BaseFieldAdapter.construct.call(this, form, handle, fieldId, inputName);
    }

    /* include BaseFieldAdapter properties and methods */
    Object.assign(SelectizeFieldAdapter.prototype, BaseFieldAdapter);

    /**
     *
     */

    SelectizeFieldAdapter.prototype.getComponentInstance = function(container)
    {
        return container.selectize;
    };

    SelectizeFieldAdapter.prototype.initComponent = function(component)
    {
        let adapter = this;

        component.on('change', function(value)
        {
            adapter.input.dispatchEvent(new CustomEvent('componentChange', {
            bubbles: false,
            detail: { value: value, component: component },
            }));
        });
    };

    /**
     *
     */

    SelectizeFieldAdapter.prototype.getValue = function()
    {
        if (!this.inputComponent) return null;

        let value = this.inputComponent.getValue();
        return (value && !Array.isArray(value)) ? [ value ] : value;
    };

    // =Export
    // =============================================================================

    Articulate.SelectizeFieldAdapter = SelectizeFieldAdapter;

})(window);