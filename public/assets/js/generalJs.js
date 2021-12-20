const ready = (callback) => {
    if (document.readyState !== "loading") {
        callback();
    } else {
        document.addEventListener("DOMContentLoaded", callback);
    }
}

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
    } else if (faultInt === '0') {
        faultInt = 'NR';
    }
    return faultInt;
}

function ajaxPostRequest(jsonData, url, successfulCallback) {
    let data = new URLSearchParams(jsonData).toString();
    let request = new XMLHttpRequest();
    request.open(
        'POST',
        url,
        true
    );

    request.setRequestHeader(
        'Content-type',
        'application/x-www-form-urlencoded'
    );
    request.onload = successfulCallback;
    request.send(data);
    return true;
}

function fuzzySearchResponse(action, term, response) {
    let data = {
        action: action,
        term: term,
    };
    ajaxPostRequest(
        data,
        '/assets/ajax/fuzzySearches.php',
        function () {
            if (this.status >= 200 && this.status < 400) {
                let output = JSON.parse(this.response);
                let data = [];
                output.forEach(element => data.push(element.name));
                response(data);
            }
            return true;
        }
    );
}

function emptyDropDown(selector) {
    while (selector.firstChild) {
        selector.removeChild(
            selector.firstChild
        );
    }
}

const earliestYear = 1920;

