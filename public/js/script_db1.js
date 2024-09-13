const pageSize = 10;
let loadedKeys = [];
let totalKeys = 0;
let currentPage = 1;
let searchTerm = '';
let currentDatabase = 1;

function fetchAndRenderKeys(type) {
    const offset = (currentPage - 1) * pageSize;
    const url = `http://127.0.0.1:8000/get_value.php?database=${currentDatabase}&key=${type}&search=${encodeURIComponent(searchTerm)}&limit=${pageSize}&offset=${offset}`;

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

            loadedKeys = Object.entries(data);
            totalKeys = Object.keys(data).length;
            renderKeys(type);
            updatePagination(type);
        })
        .catch(error => console.error('Fetch error:', error));
}

function renderKeys(type) {
    const container = document.getElementById(`key-values-${type}`);
    const filteredKeys = filterKeys(loadedKeys, type);

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
        const searchValue = searchTerm.toLowerCase();
        return key.toLowerCase().includes(searchValue);
    });
}

function searchKeys(type) {
    searchTerm = document.getElementById(`search-${type}`).value;
    currentPage = 1;
    fetchAndRenderKeys(type);
}

function updatePagination(type) {
    const pagination = document.getElementById(`pagination-${type}`);
    pagination.innerHTML = '';

    const totalPages = Math.ceil(totalKeys / pageSize);

    for (let i = 1; i <= totalPages; i++) {
        const pageButton = document.createElement('button');
        pageButton.textContent = i;
        pageButton.className = i === currentPage ? 'active' : '';
        pageButton.addEventListener('click', () => {
            currentPage = i;
            fetchAndRenderKeys(type);
        });
        pagination.appendChild(pageButton);
    }
}

document.addEventListener('DOMContentLoaded', function () {
    document.querySelector('.tabs').style.display = 'none';
    document.querySelector('.database-selection').style.display = 'block';
    document.querySelector('.back-button-container').style.display = 'none';
});
