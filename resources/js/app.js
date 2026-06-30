import './bootstrap';

document.querySelectorAll('.motion-fade').forEach((element) => {
    requestAnimationFrame(() => element.classList.add('is-visible'));
});

document.querySelectorAll('.magic-card').forEach((card) => {
    card.addEventListener('pointermove', (event) => {
        const rect = card.getBoundingClientRect();
        card.style.setProperty('--x', `${event.clientX - rect.left}px`);
        card.style.setProperty('--y', `${event.clientY - rect.top}px`);
    });
});

document.querySelectorAll('[data-toggle-target]').forEach((button) => {
    button.addEventListener('click', () => {
        const target = document.querySelector(button.dataset.toggleTarget);
        target?.classList.toggle('hidden');
    });
});

document.querySelectorAll('[data-modal-open]').forEach((button) => {
    button.addEventListener('click', () => {
        document.querySelector(button.dataset.modalOpen)?.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    });
});

document.querySelectorAll('[data-modal-close]').forEach((button) => {
    button.addEventListener('click', () => {
        button.closest('[data-modal]')?.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    });
});

const selectedList = document.querySelector('[data-selected-symptoms]');
const selectedCount = document.querySelector('[data-selected-count]');
const emptySelected = document.querySelector('[data-selected-empty]');

function refreshSelectedSymptoms() {
    if (!selectedList) {
        return;
    }

    const checked = Array.from(document.querySelectorAll('[data-symptom-checkbox]:checked'));
    selectedList.innerHTML = '';

    checked.forEach((checkbox) => {
        const item = document.createElement('div');
        item.className = 'flex items-center justify-between rounded-[6px] bg-blue-100 px-3 py-2 text-xs font-semibold text-blue-700';

        const label = document.createElement('span');
        label.textContent = checkbox.dataset.symptomLabel;

        const removeButton = document.createElement('button');
        removeButton.type = 'button';
        removeButton.className = 'grid h-5 w-5 place-items-center rounded-full text-blue-700 transition hover:bg-blue-200 hover:text-blue-900';
        removeButton.dataset.removeSymptom = checkbox.value;
        removeButton.setAttribute('aria-label', `Hapus ${checkbox.dataset.symptomLabel}`);
        removeButton.textContent = 'x';

        item.append(label, removeButton);
        selectedList.appendChild(item);
    });

    if (selectedCount) {
        selectedCount.textContent = checked.length.toString();
    }

    if (emptySelected) {
        emptySelected.classList.toggle('hidden', checked.length > 0);
    }
}

document.querySelectorAll('[data-symptom-checkbox]').forEach((checkbox) => {
    checkbox.addEventListener('change', refreshSelectedSymptoms);
});

selectedList?.addEventListener('click', (event) => {
    const removeButton = event.target.closest('[data-remove-symptom]');

    if (!removeButton) {
        return;
    }

    const checkbox = Array.from(document.querySelectorAll('[data-symptom-checkbox]'))
        .find((symptomCheckbox) => symptomCheckbox.value === removeButton.dataset.removeSymptom);

    if (checkbox) {
        checkbox.checked = false;
        checkbox.dispatchEvent(new Event('change', { bubbles: true }));
    }
});

document.querySelectorAll('form').forEach((form) => {
    form.addEventListener('reset', () => {
        requestAnimationFrame(refreshSelectedSymptoms);
    });
});

refreshSelectedSymptoms();

const symptomFilterInput = document.querySelector('[data-filter-input]');
const symptomCategoryContainer = document.querySelector('[data-filter-categories]');
const symptomRows = Array.from(document.querySelectorAll('[data-filter-row]'));
const activeFilterClasses = ['border-blue-500', 'text-blue-600'];
const inactiveFilterClasses = ['border-transparent', 'text-slate-800'];

function activeSymptomCategory() {
    return symptomCategoryContainer?.querySelector('[data-filter-category][aria-pressed="true"]')?.dataset.filterCategory ?? 'all';
}

function updateSymptomCategoryButtons(activeCategory) {
    symptomCategoryContainer?.querySelectorAll('[data-filter-category]').forEach((button) => {
        const isActive = button.dataset.filterCategory === activeCategory;
        button.setAttribute('aria-pressed', isActive.toString());
        button.classList.toggle(activeFilterClasses[0], isActive);
        button.classList.toggle(activeFilterClasses[1], isActive);
        button.classList.toggle(inactiveFilterClasses[0], !isActive);
        button.classList.toggle(inactiveFilterClasses[1], !isActive);
    });
}

function filterSymptoms() {
    const query = symptomFilterInput?.value.trim().toLowerCase() ?? '';
    const category = activeSymptomCategory();

    symptomRows.forEach((row) => {
        const matchesQuery = row.textContent.toLowerCase().includes(query);
        const matchesCategory = category === 'all' || row.dataset.symptomCategory === category;
        row.classList.toggle('hidden', !matchesQuery || !matchesCategory);
    });
}

symptomFilterInput?.addEventListener('input', filterSymptoms);
symptomFilterInput?.addEventListener('search', filterSymptoms);
symptomFilterInput?.addEventListener('keydown', (event) => {
    if (event.key === 'Enter') {
        event.preventDefault();
        filterSymptoms();
    }
});

symptomCategoryContainer?.addEventListener('click', (event) => {
    const button = event.target.closest('[data-filter-category]');

    if (button) {
        updateSymptomCategoryButtons(button.dataset.filterCategory ?? 'all');
        filterSymptoms();
    }
});

document.querySelectorAll('[data-auto-submit-search]').forEach((form) => {
    const input = form.querySelector('input[name="q"]');
    let timeoutId;

    if (!input) {
        return;
    }

    const submitSearch = () => {
        const params = new URLSearchParams(window.location.search);
        const nextQuery = input.value.trim();

        if ((params.get('q') ?? '') === nextQuery) {
            return;
        }

        if (nextQuery) {
            params.set('q', nextQuery);
        } else {
            params.delete('q');
        }

        params.delete('page');
        window.location.href = `${form.action}${params.toString() ? `?${params}` : ''}`;
    };

    const queueSearch = () => {
        window.clearTimeout(timeoutId);
        timeoutId = window.setTimeout(submitSearch, Number(form.dataset.searchDelay ?? 350));
    };

    input.addEventListener('input', queueSearch);
    input.addEventListener('search', queueSearch);
    input.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
            submitSearch();
        }
    });
});
