function populateDropDown(data) {
    emptyDropDown(data.selector);
    if (data.firstEntryText.length > 0) {
        let option = document.createElement('option');
        option.value = '0';
        option.text = data.firstEntryText;
        data.selector.add(option);
    }

    data.output.forEach(row => {
        let option = document.createElement('option');
        option.value = row.id;
        option.text = row.text;
        data.selector.add(option);
    });

    data.selector.disabled = false;
}

function yearChangeAction(value) {
    const eventSelect = document.querySelector('#year-event');
    const resultsButton = document.querySelector('#year-get-results');

    if (
        value > 1920 &&
        value <= new Date().getFullYear()
    ) {
        let data = {
            eventYear: value,
            action: 'getYearEvents',
        };
        ajaxPostRequest(
            data,
            '/assets/ajax/fuzzySearches.php',
            function () {
                if (this.status >= 200 && this.status < 400) {
                    let output = JSON.parse(this.response);
                    let dataForDropdown = {
                        selector: eventSelect,
                        firstEntryText: 'Events',
                        output: output,
                    }
                    populateDropDown(dataForDropdown);
                    resultsButton.textContent = 'Get these results';
                    resultsButton.disabled = false;
                }
                return true;
            }
        )

    } else {
        let emptyOption = document.createElement('option');
        emptyOption.value = '0';
        emptyOption.text = 'Select a year';

        emptyDropDown(eventSelect);
        eventSelect.add(emptyOption);
        eventSelect.disabled = true;

        resultsButton.textContent = 'Waiting...';
        resultsButton.disabled = true;
    }
}

function getEventResults(eventId) {
    const resultTable = document.querySelector('#result-table');
    const judgesSection = document.querySelector('#judges-section');
    const resultsSection = document.querySelector('#results-section');

    let data = {
        eventId: eventId,
        action: 'getResults',
    };

    ajaxPostRequest(
        data,
        '/assets/ajax/fuzzySearches.php',
        function () {
            if (this.status >= 200 && this.status < 400) {
                let output = JSON.parse(this.response);
                if (output.message) {
                    if (output.code === 2302) { // no results for event
                        buildResultTitleSection(output.event);
                        resultTable.style.display = 'none';
                        judgesSection.style.display = 'none';
                        resultsSection.classList.remove('hidden');
                    }
                } else {
                    resultTable.innerHTML = '';
                    resultTable.style.display = '';
                    buildResultTitleSection(output.event);
                    buildTable(output.results);
                    buildResultJudgesSection(output.judges);
                    resultsSection.classList.remove('hidden');
                }
            }
            return true;
        }
    );
}

function buildResultJudgesSection(judgesData) {
    const judgesSection = document.querySelector('#judges-section');

    if (judgesData.length > 0) {
        const judgesList = document.querySelector('#result-judges-list');

        judgesSection.style.display = '';
        judgesList.innerHTML = '';
        judgesData.forEach(function () {
            let li = document.createElement('li');
            judgesList.appendChild(li);
            li.innerHTML = this.name;
        });
    } else {
        judgesSection.style.display = 'none';
    }
}

ready(() => {
    const eventYearDropDown = document.querySelector('#event-year');
    const eventGetResultsButton = document.querySelector('#event-get-results');
    const locationEventDropDown = document.querySelector('#location-event');
    const locationYearDropDown = document.querySelector('#location-year');
    const locationGetResultsButton = document.querySelector('#location-get-results');
    const locationTextSearch = document.querySelector('#location-text-search');
    const yearTextSearch = document.querySelector('#year-text-search');
    const yearGetResultsButton = document.querySelector('#year-get-results');
    const yearEventDropDown = document.querySelector('#year-event');

    new autoComplete({
        selector: '#competition-text-search',
        minChars: 1,
        source: function (term, response) {
            let length = term.length;
            if (length >= 3) {
                fuzzySearchResponse(
                    'fuzzySearchCompetitions',
                    term,
                    response
                );
            } else {
                let emptyOption = document.createElement('option');
                emptyOption.value = '0';
                emptyOption.text = 'Select an event';

                emptyDropDown(eventYearDropDown);
                eventYearDropDown.add(emptyOption);

                eventGetResultsButton.textContent = 'Waiting...';
                eventGetResultsButton.disabled = true;
            }
        },
        onSelect: function (e, competition, item) {
            let data = {
                action: 'getCompetitionYears',
                competition: competition,
            };
            ajaxPostRequest(
                data,
                '/assets/ajax/fuzzySearches.php',
                function () {
                    if (this.status >= 200 && this.status < 400) {
                        let output = JSON.parse(this.response);
                        if (output.message) {
                            alert('fuck a duck, it failed');
                        } else {
                            let dataForDropdown = {
                                selector: eventYearDropDown,
                                firstEntryText: 'Years',
                                output: output,
                            };
                            populateDropDown(dataForDropdown);
                            eventGetResultsButton.disabled = false;
                            eventGetResultsButton.textContent =
                                'Get these results!';
                        }
                    }
                    return true;
                }
            )
        },
    });

    new autoComplete({
        selector: '#location-text-search',
        minChars: 1,
        source: function (term, response) {
            let length = term.length;
            if (length >= 3) {
                fuzzySearchResponse(
                    'fuzzySearchLocations',
                    term,
                    response
                );
            } else {
                let emptyOption = document.createElement('option');
                emptyOption.value = '0';
                emptyOption.text = 'Select a location';

                emptyDropDown(locationEventDropDown);
                locationEventDropDown.add(emptyOption);

                let anotherEmptyOption = document.createElement('option');
                anotherEmptyOption.value = '0';
                anotherEmptyOption.text = 'Select a location and event';

                emptyDropDown(locationYearDropDown);
                locationYearDropDown.add(anotherEmptyOption);

                locationGetResultsButton.textContent = 'Waiting...';
                locationGetResultsButton.disabled = true;
            }
        },
        onSelect: function (e, location, item) {
            let data = {
                action: 'getLocationEvents',
                location: location,
            };
            ajaxPostRequest(
                data,
                '/assets/ajax/fuzzySearches.php',
                function () {
                    if (this.status >= 200 && this.status < 400) {
                        let output = JSON.parse(this.response);
                        if (output.message) {
                            alert('fuck a duck, it failed');
                        } else {
                            let dataForDropdown = {
                                selector: locationEventDropDown,
                                firstEntryText: 'Events',
                                output: output,
                            };
                            populateDropDown(dataForDropdown);

                            let emptyOption = document.createElement('option');
                            emptyOption.value = '0';
                            emptyOption.text = 'Select an event';

                            emptyDropDown(locationYearDropDown);
                            locationYearDropDown.add(emptyOption);
                        }
                    }
                    return true;
                }
            )
        },
    });

    locationEventDropDown.addEventListener(
        'change',
        function () {
            let competitionId = parseInt(this.value);

            if (competitionId === 0) {
                emptyDropDown(locationYearDropDown);
                let option = document.createElement('option');
                option.value = '0';
                option.text = 'Select an event';
                locationYearDropDown.add(option);
                locationYearDropDown.disabled = true;

                locationGetResultsButton.disabled = true;
                locationGetResultsButton.textContent = 'Waiting...';
            } else {
                let data = {
                    location: locationTextSearch.value,
                    competitionId: competitionId,
                    action: 'getLocationEventYears',
                };
                ajaxPostRequest(
                    data,
                    '/assets/ajax/fuzzySearches.php',
                    function () {
                        if (this.status >= 200 && this.status < 400) {
                            let output = JSON.parse(this.response);
                            if (output.message) {
                                alert('fuck a duck, it failed');
                            } else {
                                let dataForDropdown = {
                                    selector: locationYearDropDown,
                                    firstEntryText: 'Years',
                                    output: output,
                                }
                                populateDropDown(dataForDropdown);
                                locationGetResultsButton.textContent = 'Get these results!';
                                locationGetResultsButton.disabled = false;
                            }
                        }
                        return true;
                    }
                );
            }
        }
    );

    yearTextSearch.addEventListener(
        'keyup',
        function () {
            yearChangeAction(this.value);
        }
    );
    yearTextSearch.addEventListener(
        'change',
        function () {
            yearChangeAction(this.value);
        }
    );

    eventGetResultsButton.addEventListener(
        'click',
        function () {
            getEventResults(eventYearDropDown.value);
        }
    );

    locationGetResultsButton.addEventListener(
        'click',
        function () {
            getEventResults(locationYearDropDown.value);
        }
    );

    yearGetResultsButton.addEventListener(
        'click',
        function () {
            getEventResults(yearEventDropDown.value);
        }
    )
});
