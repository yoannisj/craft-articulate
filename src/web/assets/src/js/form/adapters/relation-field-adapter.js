;(function(window, $, undefined)
{
    var Articulate = window.Articulate || {};
    window.Articulate = Articulate;

    var BaseFieldAdapter = Articulate.BaseFieldAdapter;

    // =RelationFieldAdapter
    // =============================================================================

    /**
     * Constructor for RelationFieldAdapter
     *
     * @param {Node|jQuery} form Form element the field belongs to
     * @param {string} handle The field's handle (i.e. in Craft-CMS)
     * @param {string} id Html id of field input element (namespaced)
     * @param {string} name Name of field input element (namespaced)
     */

    function RelationFieldAdapter( form, handle, fieldId, inputName )
    {
        BaseFieldAdapter.construct.call(this, form, handle, fieldId, inputName);
    }

    /* include BaseFieldAdapter properties and methods */
    Object.assign(RelationFieldAdapter.prototype, BaseFieldAdapter);

    /**
     *
     */

    RelationFieldAdapter.prototype.getComponentElement = function()
    {
        return this.field.querySelector('.elementselect');
    };

    /**
     *
     */

    RelationFieldAdapter.prototype.getComponentInstance = function(container)
    {
        return $(container).data('elementSelect');
    };

    /**
     *
     */

    RelationFieldAdapter.prototype.initComponent = function(component)
    {
        let adapter = this;

        component.on('selectElements', function(ev)
        {
            adapter.input.dispatchEvent(new CustomEvent('componentChange', {
            bubbles: false,
            detail: {
                component: adapter.inputComponent,
                value: adapter.getValue(),
                action: 'select',
                elements: ev.elements,
            },
            }));
        });

        component.on('removeElements', function(ev)
        {
            adapter.input.dispatchEvent(new CustomEvent('componentChange', {
            bubbles: false,
            detail: {
                component: adapter.inputComponent,
                value: adapter.getValue(),
                action: 'remove',
            },
            }));
        });
    };

    /**
     *
     */

    RelationFieldAdapter.prototype.getValue = function()
    {
        if (!this.inputComponent) return null;
        return this.inputComponent.getSelectedElementIds();
    };

    /**
     *
     */

    RelationFieldAdapter.prototype.setValue = function(value)
    {
        throw new Error('Can not update value of Relation field ' + this.fieldHandle);
    };

    // =Export
    // =============================================================================

    Articulate.RelationFieldAdapter = RelationFieldAdapter;

})(window, jQuery);