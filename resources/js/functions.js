/**
 * Toggle button for form display.
 */
let forms = document.getElementById('forms');
let toggleButton = document.getElementById('toggle-button');
toggleButton.onclick = function () {
    forms.className = forms.className === 'hide' ? '' : 'hide';
}

/**
 * Add row mechanism.
 */
for (let addRowButton of document.getElementsByClassName('add-row')) {
    addRowButton.onclick = function (event) {
        let button = event.target;
        let template = document.getElementById(button.getAttribute('data-template'));
        let container = document.getElementById(button.getAttribute('data-container'));
        let newRow = document.importNode(template.content, true);
        newRow.children.item(0).onchange = updateHiddenInput;
        container.appendChild(newRow);
    };
}

for (let slugInput of document.getElementsByClassName('slug-input')) {
    slugInput.onchange = updateHiddenInput;
}

function updateHiddenInput(event) {
    let input = event.target;
    let allInputs = document.getElementsByName(input.name);
    document.getElementById(input.name).value = Array.from(allInputs).map(i => i.value).filter(i => i !== '').join(',');
}

/**
 * Initialize Tablesort library for both tables.
 */
let pluginTable = document.getElementById('table-plugins');
if (pluginTable) {
    new Tablesort(pluginTable, {
        descending: true
    });
}

let translationsTable = document.getElementById('table-translations');
if (translationsTable) {
    new Tablesort(translationsTable, {
        descending: true
    });
}
