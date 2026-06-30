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
        item.innerHTML = `<span>${checkbox.dataset.symptomLabel}</span><span aria-hidden="true">x</span>`;
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

document.querySelectorAll('form').forEach((form) => {
    form.addEventListener('reset', () => {
        requestAnimationFrame(refreshSelectedSymptoms);
    });
});

refreshSelectedSymptoms();

document.querySelectorAll('[data-filter-input]').forEach((input) => {
    input.addEventListener('input', () => {
        const query = input.value.toLowerCase();
        document.querySelectorAll('[data-filter-row]').forEach((row) => {
            row.classList.toggle('hidden', !row.textContent.toLowerCase().includes(query));
        });
    });
});
