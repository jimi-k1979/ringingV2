ready(() => {
    let faultTextFields = document.querySelectorAll('.fault-field');

    Array.prototype.forEach.call(faultTextFields, function (el, i) {
        el.textContent = renderFaults(el.textContent);
    });

});
