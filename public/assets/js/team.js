ready(() => {
    let generalFields = document.querySelectorAll('.general');
    let generalBtn = document.getElementById('general-button');
    let eventFields = document.querySelectorAll('.event');
    let eventBtn = document.getElementById('events-button');
    let leagueFields = document.querySelectorAll('.league');
    let leagueBtn = document.getElementById('league-button');

    document.querySelectorAll('.fault-field').forEach((e) => {
        e.textContent = renderFaults(e.textContent);
    });
    generalFields.forEach((e) => {
        e.classList.add('d-table-cell', 'd-md-table-cell');
    })
    eventFields.forEach((e) => {
        e.classList.add('d-none', 'd-md-table-cell');
    });
    leagueFields.forEach((e) => {
        e.classList.add('d-none', 'd-md-table-cell');
    });

    generalBtn.addEventListener('click', () => {
        showFields(generalFields);
        hideFields(eventFields);
        hideFields(leagueFields);
        deactivateButtons();
        generalBtn.classList.add('active');
    });

    eventBtn.addEventListener('click', () => {
        showFields(eventFields);
        hideFields(generalFields);
        hideFields(leagueFields);
        deactivateButtons();
        eventBtn.classList.add('active');
    });

    leagueBtn.addEventListener('click', () => {
        showFields(leagueFields);
        hideFields(generalFields);
        hideFields(eventFields);
        deactivateButtons();
        leagueBtn.classList.add('active');
    });
});

function showFields(fieldsSelector) {
    fieldsSelector.forEach((e) => {
        e.classList.replace('d-none', 'd-table-cell');
    });
}

function hideFields(fieldsSelector) {
    fieldsSelector.forEach((e) => {
        e.classList.replace('d-table-cell', 'd-none');
    });
}

function deactivateButtons() {
    document.querySelectorAll('.visibility-controller').forEach((e) => {
        e.classList.remove('active');
    });
}
