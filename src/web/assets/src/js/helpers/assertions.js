var Articulate = window.Articulate || {};
window.Articulate = Articulate;

// =Helpers
// =============================================================================

/**
 * Checks if given value is empty
 * @see https://www.sitepoint.com/testing-for-empty-values/
 * 
 * @param {*} value 
 * 
 * @returns {bool}
 */

function isEmpty( value )
{
    var typeOfValue = typeof value;

    // numbers and booleans are never empty
    // cuation, this is ≠ from PHP's empty() function
    if (typeOfValue == 'number' || typeOfValue == 'boolean') { 
        return false; 
    }

    if (typeOfValue == 'undefined' || value === null) {
        return true; 
    }

    // Empty strings, empty arrays and alike
    if (typeof(value.length) != 'undefined') {
        return value.length == 0;
    }

    // objects without enumerable properties
    var count = 0;

    for (var i in value)
    {
        if (value.hasOwnProperty(i)) {
            count ++;
        }
    }

    return count == 0;
}

/**
 * 
 * @param {*} value 
 * @param {*} given
 * @param {bool} loose Whether to do loose comparison
 *  
 * @returns {bool}
 */

function isEqual( value, given, loose )
{
    if ((!loose && value === given)
        || (loose && value == given))
    {
        return true;
    }

    // falsey value can not be equal to truethy value, and vice-versa
    if ((value && !given) || (!value && given)) {
        return false;
    }

    var typeOfValue = typeof value;
    var typeOfGiven = typeof given;

    if (loose)
    {
        // normalize strings to double-check if they are in fact equal
        // @see https://www.javascripttutorial.net/string/javascript-string-equals/
        if (typeOfValue == 'string' && typeOfGiven == 'string') {
            return value.normalize() == given.normalize();
        }

        else if (typeOfValue == 'string' && typeOfGiven == 'number') {
            return parseFloat(value) === given;
        }

        else if (typeOfValue == 'number' && typeOfGiven == 'number') {
            return value == parseFloat(given);
        }
    }

    if (typeOfValue == 'object' && typeOfGiven == 'object') {
        return isEqualObject(value, given, loose);
    }

    return false;
}

/**
 * Compares two object values
 * 
 * @caution Accepts two arrays, in which case it delegates to `isEqualArray`
 * @caution Otherwise only works for plain objects by comparing their own properties
 * 
 * @param {object} value 
 * @param {object} given 
 * @param {bool} loose Whether to do loose comparison
 * 
 * @returns {bool}
 */

function isEqualObject( value, given, loose )
{
    var isArrayValue = Array.isArray(value);
    var isArrayGiven = Array.isArray(given);

    if (isArrayValue && isArrayGiven) {
        return isEqualArray(value, given, strict);
    }

    else if (isArrayValue || isArrayGiven) {
        return false; // only one of them is an array
    }

    // falsey value can not be equal to truethy value, and vice-versa
    if ((value && !given) || (given && !value)) {
        return false;
    }

    var aProps = Object.getOwnPropertyNames(a);
    var bProps = Object.getOwnPropertyNames(b);

    // objects must have same own properties
    // (and they must be in same order if `loose` is `true`)
    if (!isEqualArray(aProps, bProps, loose)) {
        return false;
    }

    // check that all properties retain equal values
    for (var i = 0, ln = aProps.length, propName; i<ln; i++)
    {
        propName = aProps[i];

        if (!isEqual(value[propName], given[propName], loose)) {
            return false;
        }
    }

    // all properties had equal values
    return true;
}

/**
 * 
 * @param {array} value 
 * @param {array} given
 * @param {bool} loose Whether to do loose comparison
 *  
 * @returns {bool}
 */

function isEqualArray( value, given, loose )
{
    var ln = value.length;

    if (ln != given.length) {
        return false;
    }

    // falsey value can not be equal to truethy value, and vice-versa
    if ((value && !given) || (given && !value)) {
        return false;
    }

    if (!loose)
    {
        // check if all indexes retain equal values (strict order)
        for (var i = 0; i<ln; i++)
        {
            if (!isEqual(value[i], given[i], strict)) {
                return false;
            }
        }

        return true;
    }

    else
    {
        // check if both arrays contain the equal values (loose order)
        // (copy given as to not mutate the argument)
        var valuesAvailable = given.slice(),
            itemValue;

        itemValueCheck: for (var i = 0; i<ln; i++)
        {
            itemValue = value[i];

            for (var vi = 0, vln = valuesAvailable.length; vi<vln; vi++)
            {
                if (isEqual(itemValue, valuesAvailable[vi], loose))
                {
                    // remove found value as to not interfere with next item checks
                    valuesAvailable.splice(gi, 1);
                    continue itemValueCheck; // check next item value
                }
            }

            return false; // item value was not found
        }

        return true; // all items' values were found
    }
}

/**
 * 
 * @param {*} value 
 * @param {*} given
 * @param {*} loose
 *  
 * @returns {bool}
 */

function isNotEqual(value, given, loose)
{
    return !isEqual(value, given, loose);
}

/**
 * 
 * @param {*} value
 * @param {array} acceptedValues
 * 
 * @returns {bool}
 */

function isOneOf( value, acceptedValues, loose )
{
    for (var i = 0, ln = acceptedValues.length; i<ln; i++)
    {
        if (isEqual(value, acceptedValues[i], loose)) {
            return true;
        }
    }

    return false;
}

// =Export
// =============================================================================

Articulate.assertions = {
    isEmpty: isEmpty,
    isEqual: isEqual,
    isNotEqual: isNotEqual,
    isOneOf: isOneOf,
};