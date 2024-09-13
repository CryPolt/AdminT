const pageSize = 10;
let loadedKeys = {};
let totalKeys = {};
let currentPage = {};
let searchTerm = {};
let currentDatabase = null;

const databases = Array.from({ length: 16 }, (_, i) => i + 1);

function initDatabaseStates() {
    databases.forEach(db => {
        loadedKeys[db] = [];
        totalKeys[db] = 0;
        currentPage[db] = 1;
        searchTerm[db] = '';
    });
}

function openTab(tabName) {
    if (!currentDatabase) {
        alert('Пожалуйста, выберите базу данных.');
        return;
    }

    document.querySelectorAll('.tabcontent').forEach(el => el.style.display = 'none');
    document.querySelectorAll('.tablink').forEach(el => el.classList.remove('active'));

    document.getElementById(tabName).style.display = 'block';
    document.querySelector(`.tablink[onclick="openTab('${tabName}')"]`).classList.add('active');

    fetchAndRenderKeys(tabName);
}

function selectDatabase(databaseId) {
    currentDatabase = databaseId;
    initDatabaseStates();
    document.querySelector('.database-selection').style.display = 'none';
    document.querySelector('.tabs').style.display = 'block';
    document.querySelector('.back-button-container').style.display = 'block';
    openTab('scanQr');
}

function goBack() {
    currentDatabase = null;
    document.querySelector('.database-selection').style.display = 'block';
    document.querySelector('.tabs').style.display = 'none';
    document.querySelector('.back-button-container').style.display = 'none';
    document.querySelectorAll('.tabcontent').forEach(el => el.style.display = 'none');
    document.querySelectorAll('.tablink').forEach(el => el.classList.remove('active'));
}

function fetchAndRenderKeys(type) {
    if (!currentDatabase) {
        alert('Пожалуйста, сначала выберите базу данных.');
        return;
    }

    const offset = (currentPage[currentDatabase] - 1) * pageSize;
    const searchValue = searchTerm[currentDatabase];
    const url = `http://127.0.0.1:8001/get_value.php?database=${currentDatabase}&key=${type}&search=${encodeURIComponent(searchValue)}&limit=${pageSize}&offset=${offset}`;

    console.log(`Fetching data from: ${url}`);

    fetch(url)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Response Data:', data);

            loadedKeys[currentDatabase] = Object.entries(data);
            totalKeys[currentDatabase] = Object.keys(data).length;
            renderKeys(type);
            updatePagination(type);
        })
        .catch(error => console.error('Fetch error:', error));
}

function renderKeys(type) {
    const container = document.getElementById(`key-values-${type}`);
    const filteredKeys = filterKeys(loadedKeys[currentDatabase], type);

    container.innerHTML = '';

    filteredKeys.forEach(([key, value]) => {
        const note = key.includes(':') ? ' (external_user_id)' : '';
        const displayValue = Array.isArray(value) ? value.join(', ') : value;
        const row = document.createElement('tr');
        row.innerHTML = `<td>${key}</td><td>${displayValue}</td><td>${note}</td>`;
        container.appendChild(row);
    });
}

function filterKeys(keys, type) {
    return keys.filter(([key]) => {
        const searchValue = searchTerm[currentDatabase].toLowerCase();
        return key.toLowerCase().includes(searchValue);
    });
}

function searchKeys(type) {
    searchTerm[currentDatabase] = document.getElementById(`search-${type}`).value;
    currentPage[currentDatabase] = 1;
    fetchAndRenderKeys(type);
}

function updatePagination(type) {
    const pagination = document.getElementById(`pagination-${type}`);
    pagination.innerHTML = '';

    const totalPages = Math.ceil(totalKeys[currentDatabase] / pageSize);

    for (let i = 1; i <= totalPages; i++) {
        const pageButton = document.createElement('button');
        pageButton.textContent = i;
        pageButton.className = i === currentPage[currentDatabase] ? 'active' : '';
        pageButton.addEventListener('click', () => {
            currentPage[currentDatabase] = i;
            fetchAndRenderKeys(type);
        });
        pagination.appendChild(pageButton);
    }
}

document.addEventListener('DOMContentLoaded', function () {
    initDatabaseStates();
    document.querySelector('.tabs').style.display = 'none';
    document.querySelector('.database-selection').style.display = 'block';
});
