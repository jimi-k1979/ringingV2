function getFuzzySearchData(request, response, action) {
    $.ajax({
        url: '/assets/ajax/fuzzySearches.php',
        type: 'POST',
        dataType: 'json',
        data: {
            term: request.term,
            action: action,
        },
        success: function (output) {
            if (output.message) {
                response($.map(output, function () {
                    return {
                        value: 'Database error, try again later',
                        label: 'Database error, try again later',
                        id: 0,
                    }
                }));
            } else {
                response($.map(output, function (item) {
                    return {
                        value: item.name,
                        label: item.name,
                        id: item.id,
                    }
                }));
            }
        }
    });
}

function populateDropDown(data) {
    data.selector.empty()
    if (data.firstEntryText.length > 0) {
        data.selector.append(
            $("<option />").val('0').text(data.firstEntryText)
        );
    }
    $.each(data.output, function () {
        data.selector.append(
            $("<option />").val(this.id)
                .text(this.text)
        );
    });
    data.selector.attr('disabled', false);
}

function yearChangeAction(value) {
    let $eventSelect = $('#year-event');
    let $resultsButton = $('#year-get-results');
    if (
        value > 1920 &&
        value <= new Date().getFullYear()
    ) {
        $.ajax({
            url: '/assets/ajax/fuzzySearches.php',
            type: 'POST',
            dataType: 'json',
            data: {
                eventYear: value,
                action: 'getYearEvents',
            },
            success: function (output) {
                if (output.message) {
                    alert('fuck a duck, it failed');
                } else {
                    let dataForDropdown = {
                        selector: $eventSelect,
                        firstEntryText: '',
                        output: output,
                    }
                    populateDropDown(dataForDropdown);
                    $resultsButton.text('Get these results!')
                        .attr('disabled', false);
                }
            }
        });

    } else {
        $eventSelect.empty().append(
            $("<option />").val('0').text('Select an event')
        ).attr('disabled', true);
        $resultsButton.text('Waiting...').attr('disabled', true);
    }
}

function getEventResults(eventId) {
    // do ajax and display results
    $.ajax({
        url: '/assets/ajax/fuzzySearches.php',
        type: 'POST',
        dataType: 'json',
        data: {
            eventId: eventId,
            action: 'getResults',
        },
        success: function (output) {
            if (output.message) {
                if (output.code === 2302) { // no results for event
                    buildResultTitleSection(output.event);
                    $('#result-table').hide();
                    $('#judges-section').hide();
                    $('#results-section').removeClass('hidden');

                }
            } else {
                $('#result-table').html('').show();
                buildTable(output.results);
                buildResultTitleSection(output.event);
                buildResultJudgesSection(output.judges);
                $('#results-section').removeClass('hidden');
            }
        }
    });
}

function buildResultTitleSection(eventData) {
    $('#result-year').text(eventData.year);
    $('#result-competition-name').text(eventData.competition);
    if (
        eventData.singleTower === false ||
        eventData.unusualTower === true
    ) {
        $('#held-at').show();
        $('#result-location').text(eventData.location);
    } else {
        $('#held-at').hide();
    }
}

function buildResultJudgesSection(judgesData) {
    let $judgesSection = $('#judges-section');

    if (judgesData.length > 0) {
        let $judgesList = $('#result-judges-list');

        $judgesSection.show();
        $judgesList.html('');
        $.each(judgesData, function () {
            $judgesList.append(
                $("<li />").text(this.name)
            );
        });
    } else {
        $judgesSection.hide();
    }
}

$(document).ready(function () {
    $('#competition-text-search').autocomplete({
        minLength: 0,
        source: function (request, response) {
            let length = request.term.length;
            if (length > 2) {
                getFuzzySearchData(
                    request,
                    response,
                    'fuzzySearchCompetitions'
                );
            } else {
                if (length === 0) {
                    $('#event-year').empty().append(
                        $("<option />").val('0').text('Select an event')
                    ).attr('disabled', true);
                    $('#event-get-results').text('Waiting...')
                        .attr('disabled', true)
                }
            }
        },
        select: function (e, data) {
            $.ajax({
                url: '/assets/ajax/fuzzySearches.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    competitionId: data.item.id,
                    action: 'getCompetitionYears',
                },
                success: function (output) {
                    if (output.message) {
                        alert('fuck a duck, it failed');
                    } else {
                        let dataForDropdown = {
                            selector: $('#event-year'),
                            firstEntryText: '',
                            output: output,
                        };
                        populateDropDown(dataForDropdown);
                        $('#event-get-results').attr('disabled', false)
                            .text('Get these results!');
                    }
                }
            });
        },
    });

    $('#location-text-search').autocomplete({
        minLength: 0,
        source: function (request, response) {
            let length = request.term.length;
            if (length > 2) {
                getFuzzySearchData(request, response, 'fuzzySearchLocations');
            } else {
                if (length === 0) {
                    $('#location-event').empty().append(
                        $("<option />").val('0').text('Select a location')
                    ).attr('disabled', true);
                    $('#location-year').empty().append(
                        $("<option />").val('0').text('Select a location and event')
                    ).attr('disabled', true);
                    $('#location-get-results').text('Waiting...')
                        .attr('disabled', true)
                }
            }
        },
        select: function (e, data) {
            $('#hidden-location-id').val(data.item.id);
            $.ajax({
                url: '/assets/ajax/fuzzySearches.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    locationId: data.item.id,
                    action: 'getLocationEvents',
                },
                success: function (output) {
                    if (output.message) {
                        alert('fuck a duck, it failed');
                    } else {
                        let dataForDropdown = {
                            selector: $('#location-event'),
                            firstEntryText: 'Select an event',
                            output: output,
                        }
                        populateDropDown(dataForDropdown);
                        $('#location-year:first-child option')
                            .text('Select an event');
                    }
                }
            });
        }
    });

    $('#location-event').on('change', function () {
        let competitionId = parseInt($(this).val());
        let $yearSelect = $('#location-year');
        let $resultsButton = $('#location-get-results');
        if (competitionId === 0) {
            $yearSelect.empty().append(
                $("<option />").val('0').text('Select an event')
            ).attr('disabled', true);
            $resultsButton.text('Waiting...').attr('disabled', true);
        } else {
            $.ajax({
                url: '/assets/ajax/fuzzySearches.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    locationId: $('#hidden-location-id').val(),
                    competitionId: competitionId,
                    action: 'getLocationEventYears',
                },
                success: function (output) {
                    if (output.message) {
                        alert('fuck a duck, it failed');
                    } else {
                        let dataForDropdown = {
                            selector: $yearSelect,
                            firstEntryText: '',
                            output: output,
                        }
                        populateDropDown(dataForDropdown);
                        $resultsButton.text('Get these results!')
                            .attr('disabled', false);
                    }
                }
            })
        }

    });

    $('#year-text-search')
        .on('keyup', function () {
            yearChangeAction($(this).val());
        })
        .on('change', function () {
            yearChangeAction($(this).val());
        });

    $('#event-get-results').on('click', function () {
        getEventResults($('#event-year').val());
    });

    $('#location-get-results').on('click', function () {
        getEventResults($('#location-year').val());
    });

    $('#year-get-results').on('click', function () {
        getEventResults($('#year-event').val());
    });
});