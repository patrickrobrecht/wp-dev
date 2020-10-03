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
let inputContainer = document.getElementById('plugin-inputs')
let addRowButton = document.getElementById('add-row');
let template = document.getElementById('plugins-input-template');
addRowButton.onclick = function () {
    let row = document.importNode(template.content, true);
    inputContainer.appendChild(row);
};

/**
 * Update plugin list in the hidden field.
 */
function setPlugins() {
    let inputs = document.getElementsByName('plugin-slugs');
    document.getElementById('plugin-slugs').value = Array.from(inputs).map(i => i.value).filter(i => i !== '').join(',');
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
