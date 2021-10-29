function addGetParamsAndSend(event) {
    let compositionId = event.target.dataset.id;
    let direction = document
        .getElementById('direction-' + compositionId)
        .value;
    let url = new URL(
        window.location.origin + '/' +
        event.target.getAttribute('href')
    );

    event.preventDefault();
    url.searchParams.set('id', compositionId);
    url.searchParams.append('direction', direction);
    document.location = url;
}

ready(() => {
    const pdfButtons = document.querySelectorAll('.btn-pdf');
    const viewButtons = document.querySelectorAll('.btn-view');

    pdfButtons.forEach(button => {
        button.addEventListener('click', event => {
            addGetParamsAndSend(event);
        });
    });

    viewButtons.forEach(button => {
        button.addEventListener('click', event => {
            addGetParamsAndSend(event);
        })
    })
});
