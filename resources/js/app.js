import './bootstrap';

import Alpine from 'alpinejs';
import {
    ArcElement,
    BarElement,
    CategoryScale,
    Chart,
    DoughnutController,
    BarController,
    Legend,
    LinearScale,
    Tooltip,
} from 'chart.js';
import {
    Activity,
    ArrowDown,
    ArrowUp,
    ArrowUpDown,
    Blocks,
    CalendarCheck,
    ChartPie,
    ChevronDown,
    ChevronLeft,
    ChevronRight,
    CircleAlert,
    CircleCheck,
    Clock,
    FileText,
    GitBranch,
    LayoutDashboard,
    ListFilter,
    LogOut,
    Network,
    PanelLeftClose,
    PanelLeftOpen,
    Pencil,
    Plus,
    Search,
    SlidersHorizontal,
    Trash2,
    Upload,
    Users,
    X,
    createIcons,
} from 'lucide';

window.Alpine = Alpine;

Alpine.start();

Chart.register(
    ArcElement,
    BarElement,
    BarController,
    CategoryScale,
    DoughnutController,
    Legend,
    LinearScale,
    Tooltip,
);

const sidebarIcons = {
    Activity,
    ArrowDown,
    ArrowUp,
    ArrowUpDown,
    Blocks,
    CalendarCheck,
    ChartPie,
    ChevronDown,
    ChevronLeft,
    ChevronRight,
    CircleAlert,
    CircleCheck,
    Clock,
    FileText,
    GitBranch,
    LayoutDashboard,
    ListFilter,
    LogOut,
    Network,
    PanelLeftClose,
    PanelLeftOpen,
    Pencil,
    Plus,
    Search,
    SlidersHorizontal,
    Trash2,
    Upload,
    Users,
    X,
};

const applySidebarState = (collapsed) => {
    document.body.classList.toggle('sidebar-collapsed', collapsed);

    document.querySelectorAll('[data-sidebar-toggle]').forEach((button) => {
        button.setAttribute('aria-label', collapsed ? 'Buka sidebar' : 'Tutup sidebar');

        const icon = button.querySelector('.sidebar-toggle-icon');

        if (icon) {
            icon.setAttribute('data-lucide', collapsed ? 'PanelLeftOpen' : 'PanelLeftClose');
        }
    });

    createIcons({ icons: sidebarIcons });
};

applySidebarState(localStorage.getItem('sidebar-collapsed') === 'true');

document.querySelectorAll('[data-sidebar-toggle]').forEach((button) => {
    button.addEventListener('click', () => {
        const collapsed = !document.body.classList.contains('sidebar-collapsed');

        localStorage.setItem('sidebar-collapsed', String(collapsed));
        applySidebarState(collapsed);
    });
});

createIcons({ icons: sidebarIcons });

const openModal = (modal) => {
    if (!modal) {
        return;
    }

    modal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
};

const closeModal = (modal) => {
    if (!modal) {
        return;
    }

    modal.classList.add('hidden');

    if (!document.querySelector('.app-modal:not(.hidden)')) {
        document.body.classList.remove('overflow-hidden');
    }
};

document.querySelectorAll('[data-modal-open]').forEach((button) => {
    button.addEventListener('click', () => {
        openModal(document.getElementById(button.dataset.modalOpen));
    });
});

document.querySelectorAll('[data-modal-close]').forEach((button) => {
    button.addEventListener('click', () => {
        closeModal(button.closest('.app-modal'));
    });
});

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
        document.querySelectorAll('.app-modal:not(.hidden)').forEach(closeModal);
    }
});

const modalWithErrors = document.querySelector('[data-open-modal-on-load]');

if (modalWithErrors) {
    openModal(document.getElementById(modalWithErrors.dataset.openModalOnLoad));
}

document.querySelectorAll('[data-password-toggle]').forEach((button) => {
    button.addEventListener('click', () => {
        const input = document.getElementById(button.dataset.passwordToggle);

        if (!input) {
            return;
        }

        const isHidden = input.type === 'password';
        input.type = isHidden ? 'text' : 'password';
        button.textContent = isHidden ? 'Sembunyikan' : 'Tampilkan';
        button.setAttribute('aria-label', isHidden ? 'Sembunyikan password' : 'Tampilkan password');
    });
});

document.querySelectorAll('[data-auto-dismiss]').forEach((alert) => {
    const delay = Number(alert.dataset.autoDismiss || 10000);

    window.setTimeout(() => {
        alert.classList.add('opacity-0', '-translate-y-1');

        window.setTimeout(() => {
            alert.remove();
        }, 300);
    }, delay);
});

const setEmployeeIdState = (input, message = null) => {
    const form = input.closest('form');
    const messageElement = form?.querySelector('[data-employee-id-message]');
    const submitButton = form?.querySelector('button[type="submit"]');
    const hasError = Boolean(message);

    input.classList.toggle('border-red-500', hasError);
    input.classList.toggle('focus:border-red-500', hasError);
    input.classList.toggle('focus:ring-red-500', hasError);

    if (messageElement) {
        messageElement.textContent = message || '';
        messageElement.classList.toggle('hidden', !hasError);
    }

    if (submitButton) {
        submitButton.disabled = hasError;
        submitButton.classList.toggle('opacity-60', hasError);
        submitButton.classList.toggle('cursor-not-allowed', hasError);
    }
};

document.querySelectorAll('[data-employee-id-check]').forEach((input) => {
    input.addEventListener('input', () => {
        setEmployeeIdState(input);
    });

    input.addEventListener('blur', async () => {
        const employeeId = input.value.trim();

        if (!employeeId) {
            setEmployeeIdState(input);
            return;
        }

        const url = new URL(input.dataset.checkUrl, window.location.origin);
        url.searchParams.set('employee_id', employeeId);

        if (input.dataset.ignoreId) {
            url.searchParams.set('ignore_id', input.dataset.ignoreId);
        }

        try {
            const response = await fetch(url, {
                headers: {
                    Accept: 'application/json',
                },
            });
            const result = await response.json();

            setEmployeeIdState(input, result.exists ? result.message : null);
        } catch (error) {
            setEmployeeIdState(input);
        }
    });
});

const bulkCheckboxes = Array.from(document.querySelectorAll('[data-bulk-checkbox]'));
const bulkSelectAll = document.querySelector('[data-bulk-select-all]');
const bulkDeleteTrigger = document.querySelector('[data-bulk-delete-trigger]');
const selectedCountElements = document.querySelectorAll('[data-selected-count]');

const updateBulkDeleteState = () => {
    const selectedCount = bulkCheckboxes.filter((checkbox) => checkbox.checked).length;
    const hasSelection = selectedCount > 0;

    selectedCountElements.forEach((element) => {
        element.textContent = String(selectedCount);
    });

    if (bulkDeleteTrigger) {
        bulkDeleteTrigger.disabled = !hasSelection;
        bulkDeleteTrigger.classList.toggle('opacity-60', !hasSelection);
        bulkDeleteTrigger.classList.toggle('cursor-not-allowed', !hasSelection);
    }

    if (bulkSelectAll) {
        bulkSelectAll.checked = bulkCheckboxes.length > 0 && selectedCount === bulkCheckboxes.length;
        bulkSelectAll.indeterminate = selectedCount > 0 && selectedCount < bulkCheckboxes.length;
    }
};

if (bulkSelectAll) {
    bulkSelectAll.addEventListener('change', () => {
        bulkCheckboxes.forEach((checkbox) => {
            checkbox.checked = bulkSelectAll.checked;
        });

        updateBulkDeleteState();
    });
}

bulkCheckboxes.forEach((checkbox) => {
    checkbox.addEventListener('change', updateBulkDeleteState);
});

updateBulkDeleteState();

const chartColors = ['#0891b2', '#0f766e', '#4f46e5', '#f59e0b', '#dc2626', '#64748b'];

document.querySelectorAll('[data-chart]').forEach((canvas) => {
    const labels = JSON.parse(canvas.dataset.labels || '[]');
    const values = JSON.parse(canvas.dataset.values || '[]');

    if (!labels.length || !values.some((value) => Number(value) > 0)) {
        return;
    }

    if (canvas.dataset.chart === 'attendance-status') {
        new Chart(canvas, {
            type: 'doughnut',
            data: {
                labels,
                datasets: [{
                    data: values,
                    backgroundColor: chartColors.slice(0, labels.length),
                    borderColor: '#ffffff',
                    borderWidth: 3,
                }],
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                },
            },
        });
    }

    if (canvas.dataset.chart === 'cluster-result') {
        const precision = Number(canvas.dataset.precision || 0);

        new Chart(canvas, {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: canvas.dataset.datasetLabel || 'Nilai',
                    data: values,
                    backgroundColor: chartColors.slice(0, labels.length),
                    borderRadius: 6,
                }],
            },
            options: {
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision,
                        },
                    },
                },
                plugins: {
                    legend: {
                        display: false,
                    },
                },
            },
        });
    }
});
