function generateTableHead(table, data) {
    let thead = table.createTHead();
    let row = thead.insertRow();

    for (let key of data) {
        let th = document.createElement('th');
        let text = document.createTextNode(ucwords(key));
        th.appendChild(text);
        th.className = 'bold green';
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
    console.log(tableData);
    let table = document.querySelector('#result-table');
    generateTableHead(table, Object.keys(tableData[0]));
    generateTable(table, tableData);

}
