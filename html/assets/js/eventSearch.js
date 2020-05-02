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
                response($.map(output, function (item) {
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

$(document).ready(function () {
    $('#competition-text-search').autocomplete({
        minLength: 3,
        source: function (request, response) {
            getFuzzySearchData(request, response, 'fuzzySearchCompetitions');
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
        minLength: 3,
        source: function (request, response) {
            getFuzzySearchData(request, response, 'fuzzySearchLocations');
        },
        select: function (e, data) {
            $.ajax({
                url: '/assets/ajax/fuzzySearches.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    competitionId: data.item.id,
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
});