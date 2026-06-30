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
        const action = form.getAttribute('action') || window.location.pathname;
        window.location.href = `${action}${params.toString() ? `?${params}` : ''}`;
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

document.querySelectorAll('[data-admin-shell]').forEach((shell) => {
    const toggle = shell.querySelector('[data-admin-sidebar-toggle]');
    const storageKey = 'diagnomed.admin.sidebarCollapsed';

    const setCollapsed = (collapsed) => {
        shell.classList.toggle('admin-sidebar-collapsed', collapsed);
        toggle?.setAttribute('aria-pressed', collapsed.toString());
        toggle?.setAttribute('aria-label', collapsed ? 'Perbesar sidebar' : 'Minimize sidebar');
        toggle?.setAttribute('title', collapsed ? 'Perbesar sidebar' : 'Minimize sidebar');
    };

    setCollapsed(window.localStorage.getItem(storageKey) === 'true');

    toggle?.addEventListener('click', () => {
        const collapsed = !shell.classList.contains('admin-sidebar-collapsed');
        window.localStorage.setItem(storageKey, collapsed.toString());
        setCollapsed(collapsed);
    });
});

document.querySelectorAll('[data-password-toggle]').forEach((button) => {
    button.addEventListener('click', () => {
        const wrapper = button.closest('.relative');
        const input = wrapper?.querySelector('input[type="password"], input[type="text"]');

        if (!input) {
            return;
        }

        const isHidden = input.type === 'password';
        input.type = isHidden ? 'text' : 'password';
        button.setAttribute('aria-label', isHidden ? button.dataset.hideLabel : button.dataset.showLabel);
        button.querySelector('[data-password-icon-show]')?.classList.toggle('hidden', isHidden);
        button.querySelector('[data-password-icon-hide]')?.classList.toggle('hidden', !isHidden);
    });
});

document.querySelectorAll('[data-upload-form]').forEach((form) => {
    const fileInput = form.querySelector('[data-upload-file], input[type="file"][name="image_file"]');
    const preview = form.querySelector('[data-upload-preview]');
    const emptyPreview = form.querySelector('[data-upload-empty]');
    const status = form.querySelector('[data-upload-status]');
    const idleText = form.querySelector('[data-upload-idle-text]');
    let previewUrl;

    fileInput?.addEventListener('change', () => {
        const file = fileInput.files?.[0];

        if (!file) {
            return;
        }

        if (previewUrl) {
            URL.revokeObjectURL(previewUrl);
        }

        status?.classList.remove('hidden');

        if (status) {
            status.textContent = 'Memproses preview gambar obat...';
        }

        if (idleText) {
            idleText.textContent = `${file.name} dipilih. Preview akan langsung berubah, lalu klik Simpan untuk menyimpan.`;
        }

        previewUrl = URL.createObjectURL(file);

        if (preview) {
            preview.onload = () => {
                if (status) {
                    status.textContent = 'Gambar siap dipreview. Klik Simpan untuk menyimpan perubahan.';
                }
            };
            preview.src = previewUrl;
            preview.classList.remove('hidden');
        }

        emptyPreview?.classList.add('hidden');
    });

    form.addEventListener('submit', () => {
        if (!fileInput?.files?.length) {
            return;
        }

        const submitButton = form.querySelector('[data-upload-submit]');

        if (submitButton) {
            submitButton.disabled = true;
            submitButton.classList.add('cursor-wait', 'opacity-75');
            submitButton.textContent = 'Menyimpan...';
        }

        if (status) {
            status.textContent = 'Menyimpan dan mengunggah gambar obat...';
        }

        status?.classList.remove('hidden');
        form.classList.add('cursor-wait');
    });
});
