ready(() => {
    document.querySelectorAll('.fault-field').forEach(function (e) {
        e.textContent = renderFaults(e.textContent);
    });
});
