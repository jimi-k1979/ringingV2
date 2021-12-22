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
    const showFiltersLink = document.getElementById('show-filters');
    const filtersDiv = document.getElementById('filters');
    const checkBoxes = document.querySelectorAll('input[type=checkbox]');
    const compositionCards = document.querySelectorAll('.composition-card');

    pdfButtons.forEach(button => {
        button.addEventListener('click', event => {
            addGetParamsAndSend(event);
        });
    });

    viewButtons.forEach(button => {
        button.addEventListener('click', event => {
            addGetParamsAndSend(event);
        });
    });

    showFiltersLink.addEventListener('click', () => {
        if (filtersDiv.classList.contains('hidden')) {
            showFiltersLink.innerHTML = 'Hide';
            filtersDiv.classList.remove('hidden');
        } else {
            showFiltersLink.innerHTML = 'Show';
            filtersDiv.classList.add('hidden');
        }
    });

    checkBoxes.forEach(checkbox => {
        checkbox.addEventListener('click', () => {
            let selectedOptions = document.querySelectorAll(
                'input[type=checkbox]:checked'
            );
            let allowedOptions = {
                'bells': [],
                'changes': [],
                'tenor': [],
            };

            selectedOptions.forEach(option => {
                switch (option.value) {
                    case '6':
                        allowedOptions.bells.push('5');
                        allowedOptions.bells.push('6');
                        break;

                    case '8':
                        allowedOptions.bells.push('7');
                        allowedOptions.bells.push('8');
                        break;

                    case '10':
                        allowedOptions.bells.push('9');
                        allowedOptions.bells.push('10');
                        allowedOptions.bells.push('11');
                        allowedOptions.bells.push('12');
                        break;

                    case 'short':
                        allowedOptions.changes.push('short');
                        break;

                    case 'medium':
                        allowedOptions.changes.push('medium');
                        break;

                    case 'long':
                        allowedOptions.changes.push('long');
                        break;

                    case 'in':
                        allowedOptions.tenor.push('true');
                        break;

                    case 'behind':
                        allowedOptions.tenor.push('false');
                        break;
                }
            });

            compositionCards.forEach(card => {
                if (
                    allowedOptions.bells.includes(card.dataset.bells)
                    && allowedOptions.changes.includes(card.dataset.changeCount)
                    && allowedOptions.tenor.includes(card.dataset.tenor)
                ) {
                    card.classList.remove('hidden');
                } else {
                    card.classList.add('hidden');
                }
            });


        });
    })
});
