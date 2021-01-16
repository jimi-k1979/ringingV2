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
        },
        onSelect: function (e, competition, item) {
            $('#location-id').val(competition);
        }
    });

    $('#number-of-teams')
        .on('change', function () {
            let numberOfTeams = parseInt($(this).val());

            $('[id^=result-]').attr('disabled', true)
                .prop('required', false)
                .addClass('hidden');
            for (let i = 1; i <= numberOfTeams; i++) {
                $('#result-' + i).attr('disabled', false).removeClass('hidden');
                if ($('#peal-numbers').prop('checked') === true) {
                    $('#peal-' + i).attr('disabled', true);
                }
                $('#faults-' + i).prop('required', true);
                $('#team-' + i).prop('required', true);
            }
        });

    $('#peal-numbers')
        .on('click', function () {
            let pealNumber = $('.peal-input');
            if ($(this).prop('checked')) {
                pealNumber.attr('disabled', true);
            } else {
                pealNumber.attr('disabled', false);
            }
        });

    $('#clear-form-button')
        .on('click', function () {
            $('.clearable-field').val('');
            $('.nullable-field').val('null');
            $('.blockable-field').attr('disabled', true);
            $('#peal-numbers').prop('checked', false);
            $('[id^=result-]').addClass('hidden');

            for (let i = 1; i <= 20; i++) {
                $('#position-' + i).val(i)
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
                $('#team-' + j).val(team);
            }
        });
    }
});
