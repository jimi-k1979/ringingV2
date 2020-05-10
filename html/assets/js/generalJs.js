function ucwords(str) {
    return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
        return $1.toUpperCase();
    });
}

function strtolower(str) {
    return (str + '').toLowerCase();
}

function renderFaults(faultInt) {
    faultInt += '';
    if (faultInt.slice(-2) === '.5') {
        faultInt = faultInt.slice(0, -2) + '½';
    } else if (faultInt.slice(-3) === '.25') {
        faultInt = faultInt.slice(0, -3) + '¼';
    } else if (faultInt.slice(-3) === '.75') {
        faultInt = faultInt.slice(0, -3) + '¾';
    } else if (faultInt.slice(-3) === '.33') {
        faultInt = faultInt.slice(0, -3) + '⅓'
    } else if (faultInt.slice(-3) === '.67') {
        faultInt = faultInt.slice(0, -3) + '⅔'
    }
    return faultInt;
}