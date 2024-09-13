const pageSize = 10;
let loadedKeys = {};
let totalKeys = {};
let currentPage = {};
let searchTerm = {};
let currentDatabase = null;

const databases = {
    1: { name: 'Database 1', availableTabs: ['phone', 'tab2', 'tab3'] },
    5: { name: 'Database 5', availableTabs: ['sms'] },
    9: { name: 'Database 9', availableTabs: ['scanQr'] },
    10: { name: 'Database 10', availableTabs: ['pimmer:accessToken'] },
    15: { name: 'Database 15', availableTabs: ['di_counter', 'qr_token', 'su_lock'] }
};

function initDatabaseStates() {
    Object.keys(databases).forEach(db => {
        loadedKeys[db] = [];
        totalKeys[db] = 0;
        currentPage[db] = 1;
        searchTerm[db] = '';
    });
}

function openTab(tabName) {
    if (!currentDatabase) {
        alert('Пожалуйста, сначала выберите базу данных.');
        return;
    }

    const tabs = databases[currentDatabase].availableTabs;

    if (!tabs.includes(tabName)) {
        alert('Выбранная вкладка недоступна для текущей базы данных.');
        return;
    }

    document.querySelectorAll('.tabcontent').forEach(el => el.style.display = 'none');
    document.querySelectorAll('.tablink').forEach(el => el.classList.remove('active'));

    document.getElementById(tabName).style.display = 'block';
    document.querySelector(`.tablink[onclick="openTab('${tabName}')"]`).classList.add('active');

    fetchAndRenderKeys(tabName);
}

function selectDatabase(databaseId) {
    if (!databases[databaseId]) {
        alert('База данных не найдена.');
        return;
    }

    currentDatabase = databaseId;
    initDatabaseStates();
    document.querySelector('.database-selection').style.display = 'none';
    document.querySelector('.tabs').style.display = 'block';
    document.querySelector('.back-button-container').style.display = 'block';

    const tabs = document.querySelectorAll('.tablink');
    tabs.forEach(tab => {
        const id = tab.id.replace('tab-', '');
        if (databases[databaseId].availableTabs.includes(id)) {
            tab.style.display = 'block';
        } else {
            tab.style.display = 'none';
        }
    });

    const firstTab = databases[databaseId].availableTabs[0];
    openTab(firstTab);
    fetchAndRenderKeys(firstTab); // Автоматически загружаем данные для первой вкладки
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
    const keys = loadedKeys[currentDatabase];

    container.innerHTML = '<tr><th>Ключ</th><th>Значение</th></tr>';

    keys.forEach(([key, value]) => {
        const row = document.createElement('tr');
        const keyCell = document.createElement('td');
        const valueCell = document.createElement('td');

        keyCell.textContent = key;
        valueCell.textContent = value;

        row.appendChild(keyCell);
        row.appendChild(valueCell);
        container.appendChild(row);
    });
}

function updatePagination(type) {
    const pagination = document.getElementById(`pagination-${type}`);
    const totalPages = Math.ceil(totalKeys[currentDatabase] / pageSize);

    pagination.innerHTML = '';

    if (totalPages <= 1) return;

    if (currentPage[currentDatabase] > 1) {
        const prevButton = document.createElement('button');
        prevButton.textContent = 'Предыдущая';
        prevButton.onclick = () => {
            currentPage[currentDatabase]--;
            fetchAndRenderKeys(type);
        };
        pagination.appendChild(prevButton);
    }

    if (currentPage[currentDatabase] < totalPages) {
        const nextButton = document.createElement('button');
        nextButton.textContent = 'Следующая';
        nextButton.onclick = () => {
            currentPage[currentDatabase]++;
            fetchAndRenderKeys(type);
        };
        pagination.appendChild(nextButton);
    }

    for (let i = 1; i <= totalPages; i++) {
        const pageButton = document.createElement('button');
        pageButton.textContent = i;
        pageButton.onclick = () => {
            currentPage[currentDatabase] = i;
            fetchAndRenderKeys(type);
        };
        pagination.appendChild(pageButton);
    }
}

function searchKeys(type) {
    searchTerm[currentDatabase] = document.getElementById(`search-${type}`).value;
    currentPage[currentDatabase] = 1;
    fetchAndRenderKeys(type);
}

document.addEventListener('DOMContentLoaded', () => {
    initDatabaseStates();
});
