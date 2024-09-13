let loadedKeys = {};
let totalKeys = {};
let currentPage = {};
let searchTerm = {};
let currentDatabase = null;

const databases = {
    1: { name: 'Database 1', availableTabs: ['phone'] },
    5: { name: 'Database 5', availableTabs: ['sms'] },
    9: { name: 'Database 9', availableTabs: ['scanQr'] },
    10: { name: 'Database 10', availableTabs: ['pimmer:accessToken'] },
    15: { name: 'Database 15', availableTabs: ['di_counter', 'qr_token', 'su_lock'] },
    'postgres': { name: 'Database Postgres', availableTabs: ['external_user_id', 'tab2', 'new_tab'] }
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
        alert('Пожалуйста, выберите базу данных.');
        return;
    }

    const tabs = databases[currentDatabase]?.availableTabs || [];

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

    const searchValue = searchTerm[currentDatabase];
    const url = `get_value.php?database=${currentDatabase}&key=${type}&search=${encodeURIComponent(searchValue)}`;

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

    container.innerHTML = '<tr><th>Ключ</th><th>Значение</th><th>Действия</th></tr>';

    keys.forEach(([key, value]) => {
        const row = document.createElement('tr');
        const keyCell = document.createElement('td');
        const valueCell = document.createElement('td');
        const actionCell = document.createElement('td');

        keyCell.textContent = key;
        valueCell.textContent = value;

        const deleteButton = document.createElement('button');
        deleteButton.textContent = 'Удалить';
        deleteButton.onclick = () => deleteKey(type, key);

        actionCell.appendChild(deleteButton);
        row.appendChild(keyCell);
        row.appendChild(valueCell);
        row.appendChild(actionCell);
        container.appendChild(row);
    });
}

function deleteKey(type, key) {
    if (!confirm(`Вы уверены, что хотите удалить ключ "${key}"?`)) {
        return;
    }

    fetch('delete_key.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams({
            database: currentDatabase,
            type: type,
            key: key
        })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Ключ удален успешно.');
                fetchAndRenderKeys(type);
            } else {
                alert('Ошибка при удалении ключа.');
            }
        })
        .catch(error => {
            console.error('Ошибка при удалении ключа:', error);
            alert('Ошибка при удалении ключа.');
        });
}

function searchKeys(type) {
    searchTerm[currentDatabase] = document.getElementById(`search-${type}`).value;
    currentPage[currentDatabase] = 1;
    fetchAndRenderKeys(type);
}

initDatabaseStates();
