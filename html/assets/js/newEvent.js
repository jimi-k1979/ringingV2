function enableCompetitionFieldIfInYearRange(enteredDate) {
    let dateNow = new Date();
    let searchField = document.querySelector('#competition-fuzzy-search');

    if (
        enteredDate >= earliestYear &&
        enteredDate <= dateNow.getFullYear() &&
        searchField.disabled
    ) {
        searchField.disabled = false;
    } else if (
        (
            enteredDate < earliestYear ||
            enteredDate > dateNow.getFullYear()
        ) &&
        !searchField.disabled
    ) {
        searchField.disabled = true;
    }
}

function teamCountEventHandler() {
    const numberOfTeams = parseInt(this.value);
    const resultRows = document.querySelectorAll('[id^=result-]');
    const pealNumbersCheckbox = document.querySelector('#peal-numbers');
    const clearableInputs = document.querySelectorAll(
        '[id^=result-] > div > input.clearable-field'
    );

    resultRows.disabled = true;
    resultRows.forEach(row => row.classList.add('hidden'));
    clearableInputs.required = false;

    for (let i = 1; i <= numberOfTeams; i++) {
        let resultInput = document.querySelector('#result-' + i);
        let pealInput = document.querySelector('#peal-' + i);
        let faultsInput = document.querySelector('#faults-' + 1);
        let teamInput = document.querySelector('#team-' + i);

        resultInput.disabled = false;
        resultInput.classList.remove('hidden');
        faultsInput.required = true;
        teamInput.required = true;
        if (pealNumbersCheckbox.checked) {
            pealInput.disabled = true;
        } else {
            pealInput.required = true;
        }
    }
}

ready(() => {
    const yearSearchInput = document.querySelector('#year-text-search');
    const locationSearchInput = document.querySelector('#location-fuzzy-search');
    const numberOfTeamsInput = document.querySelector('#number-of-teams');
    const pealNumbersCheckbox = document.querySelector('#peal-numbers');

    const resultRows = document.querySelectorAll('[id^=result-]');
    const clearFormButton = document.querySelector('#clear-form-button');

    const competitionId = document.querySelector('#competition-id');
    const usualLocationId = document.querySelector('#usual-location-id');
    const locationId = document.querySelector('#location-id');

    const metaData = document.querySelector('#meta-data');

    yearSearchInput.addEventListener(
        'change',
        function () {
            enableCompetitionFieldIfInYearRange(
                parseInt(this.value)
            );
        }
    );
    yearSearchInput.addEventListener(
        'keyup',
        function () {
            const searchField = document.querySelector(
                '#competition-fuzzy-search'
            );

            if (this.value.length >= 4) {
                enableCompetitionFieldIfInYearRange(
                    parseInt(this.value)
                );
            } else if (!searchField.disabled) {
                searchField.disabled = true
            }

        }
    );

    numberOfTeamsInput.addEventListener(
        'change',
        teamCountEventHandler
    );
    numberOfTeamsInput.addEventListener(
        'keyup',
        teamCountEventHandler
    );

    pealNumbersCheckbox.addEventListener(
        'click',
        function () {
            const pealNumberInput = document.querySelectorAll('.peal-input');
            const teamCount = parseInt(numberOfTeamsInput.value);

            if (this.checked) {
                pealNumberInput.forEach(input => {
                    input.disabled = true;
                    input.required = false;
                });
            } else {
                pealNumberInput.forEach(input => input.disabled = false);
                for (let i = 1; i <= teamCount; i++) {
                    document.querySelector('#peal-' + i).required = true;
                }
            }
        }
    );

    clearFormButton.addEventListener(
        'click',
        function () {
            document.querySelectorAll('.clearable-field').forEach(
                field => field.value = ''
            );
            document.querySelectorAll('.nullable-field').forEach(
                field => field.value = 'null'
            );
            document.querySelectorAll('.blockable-field').forEach(
                field => field.disabled = true
            );
            resultRows.forEach(row => row.classList.add('hidden'));
            pealNumbersCheckbox.checked = false;

            for (let i = 1; i <= 20; i++) {
                document.querySelector('#position-' + i).value = i;
            }
        }
    );

    new autoComplete({
        selector: '#competition-fuzzy-search',
        minChars: 3,
        source: function (term, response) {
            fuzzySearchResponse(
                'fuzzySearchCompetitions',
                term,
                response
            );
        },
        onSelect: function (e, competition, item) {
            let data = {
                action: 'getCompetitionDetails',
                competition: competition,
                year: yearSearchInput.value,
            };
            ajaxPostRequest(
                data,
                '/assets/ajax/newEvent.php',
                function () {
                    if (this.status >= 200 && this.status < 400) {
                        let output = JSON.parse(this.response);
                        if (output.status === 200) {
                            competitionId.value = output.competitionId;
                            locationSearchInput.disabled = false;

                            usualLocationId.value = output.usualLocationId;
                            locationId.value = output.usualLocationId;
                            if (output.usualLocation) {
                                locationSearchInput.value = output.usualLocation;
                            } else {
                                locationSearchInput.value = '';
                            }

                            metaData.disabled = false;

                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'No can do...',
                                text: output.message,
                            });
                        }

                    }

                }
            );
            // $.ajax({
            //     url: '/assets/ajax/newEvent.php',
            //     type: 'POST',
            //     dataType: 'json',
            //     data: {
            //         action: 'getCompetitionDetails',
            //         competition: competition,
            //         year: $('#year-text-search').val(),
            //     },
            //     success: function (output) {
            //         if (output.status === 200) {
            //             $('#competition-id').val(output.competitionId);
            //             let venueText = $('#location-fuzzy-search');
            //             venueText.attr('disabled', false);
            //
            //             $('#usual-location-id').val(
            //                 output.usualLocationId
            //             );
            //             $('#location-id').val(
            //                 output.usualLocationId
            //             );
            //
            //             if (output.usualLocation) {
            //                 venueText.val(output.usualLocation);
            //             } else {
            //                 venueText.val('');
            //             }
            //
            //             $('#meta-data').attr('disabled', false);
            //
            //         } else {
            //             Swal.fire({
            //                 icon: 'error',
            //                 title: 'No can do...',
            //                 text: output.message,
            //             });
            //         }
            //     }
            // });
        }
    });

    new autoComplete({
        selector: '#location-fuzzy-search',
        minChars: 3,
        source: function (term, response) {
            fuzzySearchResponse(
                'fuzzySearchLocations',
                term,
                response
            );
        },
        onSelect: function (e, location, item) {
            let data = {
                action: 'getLocationId',
                location: location,
                year: yearSearchInput.value,
            };

            ajaxPostRequest(
                data,
                '/assets/ajax/newEvent.php',
                function () {
                    if (this.status >= 200 && this.status < 400) {
                        let output = JSON.parse(this.response);

                        if (output.status === 200) {
                            locationId.value = output.locationId;
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'No can do...',
                                text: output.message,
                            });
                        }
                    }
                }
            );
        }
    });

    for (let j = 1; j <= 20; j++) {
        new autoComplete({
            selector: '#team-' + j,
            minChars: 3,
            source: function (term, response) {
                fuzzySearchResponse(
                    'fuzzySearchTeams',
                    term,
                    response
                );
            },
            onSelect: function (e, team, item) {
                document.querySelector('#team-' + j).value = team;
            }
        });
    }
});
