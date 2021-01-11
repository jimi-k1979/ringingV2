function fuzzySearchResponse(action, term, response) {
    return $.ajax({
        url: '/assets/ajax/fuzzySearches.php',
        type: 'POST',
        dataType: 'json',
        data: {
            action: action,
            term: term,
        },
        success: function (output) {
            let data = [];
            output.forEach(element => data.push(element.name));
            response(data);
        }
    });
}

function enableCompetitionFieldIfInYearRange(enteredDate) {
    let dateNow = new Date();
    let searchField = $('#competition-fuzzy-search');

    if (
        enteredDate >= earliestYear &&
        enteredDate <= dateNow.getFullYear() &&
        searchField.attr('disabled')
    ) {
        searchField.attr('disabled', false);
    } else if (
        (
            enteredDate < earliestYear ||
            enteredDate > dateNow.getFullYear()
        ) &&
        !searchField.attr('disabled')
    ) {
        searchField.attr('disabled', true);
    }
}

$(function () {
    $('#year-text-search')
        .on('change', function () {
            enableCompetitionFieldIfInYearRange(
                parseInt($(this).val())
            );
        })
        .on('keyup', function () {
            let searchField = $('#competition-fuzzy-search');

            if ($(this).val().length >= 4) {
                enableCompetitionFieldIfInYearRange(
                    parseInt($(this).val())
                )
            } else if (
                !searchField.attr('disabled')
            ) {
                searchField.attr('disabled', true);
            }
        });

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
            $.ajax({
                url: '/assets/ajax/newEvent.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'getCompetitionDetails',
                    competition: competition,
                    year: $('#year-text-search').val(),
                },
                success: function (output) {
                    if (output.status === 200) {
                        $('#competition-id').val(output.competitionId);
                        let venueText = $('#location-fuzzy-search');
                        venueText.attr('disabled', false);

                        $('#usual-location-id').val(
                            output.usualLocationId
                        );
                        $('#location-id').val(
                            output.usualLocationId
                        );

                        if (output.usualLocation) {
                            venueText.val(output.usualLocation);
                        } else {
                            venueText.val('');
                        }

                        $('#meta-data').attr('disabled', false);

                    } else {

                    }
                }
            });
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
        }
    })
});