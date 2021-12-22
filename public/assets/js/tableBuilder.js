function generateTableHead(table, data) {
    let thead = table.createTHead();
    let row = thead.insertRow();
    thead.className = 'thead-light';

    for (let key of data) {
        let th = document.createElement('th');
        let text = document.createTextNode(ucwords(key));
        th.appendChild(text);
        row.appendChild(th);
    }
}

function generateTable(table, data) {
    for (let element of data) {
        let row = table.insertRow();
        for (let key in element) {
            let cell = row.insertCell();
            cell.className = 'result-cell';
            if (
                key === 'team'
            ) {
                cell.classList.add('left-align');
            } else {
                cell.classList.add('right-align');
            }

            let cellContent = element[key];
            if (cellContent === null) {
                cellContent = '';
            }
            if (key === 'faults') {
                cellContent = renderFaults(cellContent);
            }

            let text = document.createTextNode(cellContent);
            cell.appendChild(text);
        }
    }
}

function buildTable(tableData) {
    let table = document.querySelector('#result-table');
    if (tableData.length > 0) {
        generateTableHead(table, Object.keys(tableData[0]));
        generateTable(table, tableData);
    } else {
        let data = {
            year: '',
            competition: 'No results for this event',
            singleTower: true,
            unusualTower: false,
        };
        buildResultTitleSection(data);
    }

}

function buildResultTitleSection(eventData) {
    document.querySelector('#result-year').textContent = eventData.year;
    document.querySelector('#result-competition-name').textContent =
        eventData.competition;

    if (
        eventData.singleTower === false ||
        eventData.unusualTower === true
    ) {
        document.querySelector('#held-at').style.display = '';
        document.querySelector('#result-location').textContent =
            eventData.location;
    } else {
        document.querySelector('#held-at').style.display = 'none';
    }
}
