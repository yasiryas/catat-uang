import Alpine from 'alpinejs';
import Swal from 'sweetalert2';

window.Alpine = Alpine;
window.Swal = Swal;

function nowDateTime() {
    const now = new Date();
    const y = now.getFullYear();
    const m = String(now.getMonth() + 1).padStart(2, '0');
    const d = String(now.getDate()).padStart(2, '0');
    const h = String(now.getHours()).padStart(2, '0');
    const min = String(now.getMinutes()).padStart(2, '0');
    return `${y}-${m}-${d}T${h}:${min}`;
}

function formatDate(dateStr) {
    if (!dateStr) return '-';
    return new Date(dateStr).toLocaleDateString('id-ID', {
        year: 'numeric', month: 'long', day: 'numeric'
    });
}

function formatDateTime(dateStr) {
    if (!dateStr) return '-';
    return new Date(dateStr).toLocaleString('id-ID', {
        year: 'numeric', month: 'long', day: 'numeric',
        hour: '2-digit', minute: '2-digit'
    });
}

Alpine.magic('toast', () => Alpine.store('toast'));

Alpine.store('toast', {
    show(message, type = 'success', title = '') {
        const config = {
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            icon: type,
            title: title || (type === 'success' ? 'Berhasil' : type === 'error' ? 'Error' : 'Info'),
            text: message,
            customClass: {
                popup: 'rounded-xl shadow-lg',
                title: 'font-semibold',
                timerProgressBar: 'bg-current opacity-50'
            }
        };

        if (type === 'success') {
            config.background = '#ecfdf5';
            config.color = '#065f46';
            config.iconColor = '#10b981';
        } else if (type === 'error') {
            config.background = '#fef2f2';
            config.color = '#991b1b';
            config.iconColor = '#ef4444';
        } else if (type === 'warning') {
            config.background = '#fffbeb';
            config.color = '#92400e';
            config.iconColor = '#f59e0b';
        } else {
            config.background = '#eff6ff';
            config.color = '#1e40af';
            config.iconColor = '#3b82f6';
        }

        Swal.fire(config);
    },

    success(message, title) {
        this.show(message, 'success', title);
    },

    error(message, title) {
        this.show(message, 'error', title);
    },

    warning(message, title) {
        this.show(message, 'warning', title);
    },

    info(message, title) {
        this.show(message, 'info', title);
    },

    confirm(title, text, confirmText = 'Ya', cancelText = 'Batal') {
        return Swal.fire({
            title,
            text,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3b82f6',
            cancelButtonColor: '#6b7280',
            confirmButtonText: confirmText,
            cancelButtonText: cancelText,
            customClass: {
                popup: 'rounded-xl shadow-lg',
                confirmButton: 'rounded-lg px-4 py-2',
                cancelButton: 'rounded-lg px-4 py-2'
            }
        });
    }
});

window.categoryPage = function () {
    return {
        modal: {
            show: false,
            mode: 'create',
            loading: false,
            errors: {},
            form: {
                id: null,
                name: '',
                type: 'income',
                budget_limit: 0,
            }
        },

        deleteModal: {
            show: false,
            loading: false,
            category: null
        },

        categories: [],
        pagination: {
            current_page: 1,
            last_page: 1,
            total: 0
        },

        async fetchCategories(page = 1) {
            try {
                const response = await fetch(`${route('categories.index')}?page=${page}`, {
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await response.json();
                this.categories = data.data;
                this.pagination = data.pagination;
            } catch (error) {
                console.error('Failed to load categories:', error);
            }
        },

        create() {
            this.resetForm();
            this.modal.mode = 'create';
            this.modal.show = true;
        },

        edit(category) {
            this.resetForm();
            this.modal.mode = 'edit';
            this.modal.form = { ...category };
            this.modal.show = true;
        },

        confirmDelete(category) {
            this.deleteModal.category = category;
            this.deleteModal.show = true;
        },

        async submit() {
            this.modal.loading = true;
            this.modal.errors = {};

            try {
                const url = this.modal.mode === 'create'
                    ? route('categories.store')
                    : route('categories.update', this.modal.form.id);

                const method = this.modal.mode === 'create' ? 'POST' : 'PUT';

                const response = await fetch(url, {
                    method,
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(this.modal.form)
                });

                const data = await response.json();

                if (!response.ok) {
                    if (response.status === 422 && data.errors) {
                        this.modal.errors = data.errors;
                        this.$toast.error('Validasi gagal', 'Error');
                    } else {
                        throw new Error(data.message || 'Terjadi kesalahan');
                    }
                    return;
                }

                this.modal.show = false;
                this.$toast.success(data.message);
                await this.fetchCategories(this.pagination.current_page);
            } catch (error) {
                this.$toast.error(error.message);
            } finally {
                this.modal.loading = false;
            }
        },

async deleteCategory() {
            this.deleteModal.loading = true;

            try {
                const response = await fetch(route('categories.destroy', this.deleteModal.category.id), {
                    method: 'DELETE',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Gagal menghapus kategori');
                }

                this.deleteModal.show = false;
                this.$toast.success(data.message);
                this.categories = this.categories.filter(c => c.id !== this.deleteModal.category.id);
            } catch (error) {
                this.$toast.error(error.message);
            } finally {
                this.deleteModal.loading = false;
            }
        },

        formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(Math.abs(amount || 0));
        },

        resetForm() {
            this.modal.form = {
                id: null,
                name: '',
                type: 'income',
                budget_limit: 0,
            };
            this.modal.errors = {};
        }
    }
};

window.transactionPage = function () {
    return {
        modal: {
            show: false,
            mode: 'create',
            loading: false,
            errors: {},
            form: {
                id: null,
                period_id: '',
                category_id: '',
                type: 'income',
                amount: '',
                note: '',
                date: nowDateTime(),
            }
        },

        deleteModal: {
            show: false,
            loading: false,
            transaction: null
        },

        transactions: [],
        categories: [],
        periods: [],
        currentType: 'all',
        pagination: {
            current_page: 1,
            last_page: 1,
            total: 0
        },

        async init() {
            const el = this.$el;
            this.categories = JSON.parse(el.dataset.categories || '[]');
            this.periods = JSON.parse(el.dataset.periods || '[]');
            this.currentType = el.dataset.type || 'all';
            await this.loadTransactions();
        },

        async loadTransactions(page = 1) {
            const params = new URLSearchParams({
                page,
                ajax: 'true'
            });

            if (this.currentType && this.currentType !== 'all') {
                params.append('type', this.currentType);
            }

            try {
                const response = await fetch(`${route('transactions.index')}?${params}`, {
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await response.json();
                this.transactions = data.data;
                this.pagination = data.pagination;
            } catch (error) {
                console.error('Failed to load transactions:', error);
            }
        },

        create(type = 'income') {
            this.resetForm();
            this.modal.mode = 'create';
            this.modal.form.type = type;
            this.modal.form.date = nowDateTime();
            this.modal.show = true;
        },

        edit(transaction) {
            this.resetForm();
            this.modal.mode = 'edit';
            this.modal.form = {
                id: transaction.id,
                period_id: transaction.period_id,
                category_id: transaction.category_id,
                account_id: transaction.account_id,
                type: transaction.type,
                amount: transaction.amount,
                note: transaction.note || '',
                date: transaction.date ? transaction.date.replace(' ', 'T') : nowDateTime()
            };
            this.modal.show = true;
        },

        confirmDelete(transaction) {
            this.deleteModal.transaction = transaction;
            this.deleteModal.show = true;
        },

        async submit() {
            this.modal.loading = true;
            this.modal.errors = {};

            try {
                const url = this.modal.mode === 'create'
                    ? route('transactions.store')
                    : route('transactions.update', this.modal.form.id);

                const method = this.modal.mode === 'create' ? 'POST' : 'PUT';

                const response = await fetch(url, {
                    method,
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(this.modal.form)
                });

                const data = await response.json();

                if (!response.ok) {
                    if (response.status === 422 && data.errors) {
                        this.modal.errors = data.errors;
                        this.$toast.error('Validasi gagal', 'Error');
                    } else {
                        throw new Error(data.message || 'Terjadi kesalahan');
                    }
                    return;
                }

                this.modal.show = false;
                this.$toast.success(data.message);
                await this.loadTransactions(this.pagination.current_page);
            } catch (error) {
                this.$toast.error(error.message);
            } finally {
                this.modal.loading = false;
            }
        },

        async deleteTransaction() {
            this.deleteModal.loading = true;

            try {
                const response = await fetch(route('transactions.destroy', this.deleteModal.transaction.id), {
                    method: 'DELETE',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Gagal menghapus transaksi');
                }

                this.deleteModal.show = false;
                this.$toast.success(data.message);
                this.transactions = this.transactions.filter(t => t.id !== this.deleteModal.transaction.id);
            } catch (error) {
                this.$toast.error(error.message);
            } finally {
                this.deleteModal.loading = false;
            }
        },

        resetForm() {
            this.modal.form = {
                id: null,
                period_id: '',
                category_id: '',
                account_id: '',
                type: 'income',
                amount: '',
                note: '',
                date: nowDateTime(),
            };
            this.modal.errors = {};
        },

        formatDate(dateStr) {
            return formatDate(dateStr);
        },

        formatDateTime(dateStr) {
            return formatDateTime(dateStr);
        },

        formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(Math.abs(amount));
        },

        getTypeBadgeClass(type) {
            const classes = {
                income: 'bg-emerald-100 text-emerald-800',
                expense: 'bg-red-100 text-red-800',
                mutation: 'bg-blue-100 text-blue-800',
                adjustment: 'bg-amber-100 text-amber-800'
            };
            return classes[type] || 'bg-slate-100 text-slate-800';
        },

        getTypeLabel(type) {
            const labels = {
                income: 'Pemasukan',
                expense: 'Pengeluaran',
                mutation: 'Mutasi',
                adjustment: 'Penyesuaian'
            };
            return labels[type] || type;
        }
    }
};

window.transactionTypePage = function () {
    return {
        type: '',
        title: '',
        subtitle: '',
        emptyMessage: '',
        formModalName: '',
        deleteModalName: '',
        submitButtonClass: '',
        deleteButtonClass: '',

        modal: {
            mode: 'create',
            loading: false,
            errors: {},
            form: {
                id: null,
                period_id: '',
                category_id: '',
                type: 'income',
                amount: '',
                note: '',
                date: nowDateTime(),
            }
        },

        deleteModal: {
            show: false,
            loading: false,
            transaction: null
        },

        transactions: [],
        categories: [],
        periods: [],
        accounts: [],
        pagination: {
            current_page: 1,
            last_page: 1,
            total: 0
        },

        async init() {
            const el = this.$el;
            this.type = el.dataset.type || 'income';
            this.categories = JSON.parse(el.dataset.categories || '[]');
            this.periods = JSON.parse(el.dataset.periods || '[]');
            this.accounts = JSON.parse(el.dataset.accounts || '[]');

            const typeConfig = {
                income: { title: 'Pemasukan', subtitle: 'Kelola pemasukan Anda', emptyMessage: 'Belum ada pemasukan.', formModalName: 'income-form', deleteModalName: 'income-delete', submitButtonClass: 'bg-emerald-600 hover:bg-emerald-700', deleteButtonClass: 'bg-red-600 hover:bg-red-700' },
                expense: { title: 'Pengeluaran', subtitle: 'Kelola pengeluaran Anda', emptyMessage: 'Belum ada pengeluaran.', formModalName: 'expense-form', deleteModalName: 'expense-delete', submitButtonClass: 'bg-red-600 hover:bg-red-700', deleteButtonClass: 'bg-red-600 hover:bg-red-700' },
                mutation: { title: 'Mutasi', subtitle: 'Kelola mutasi Anda', emptyMessage: 'Belum ada mutasi.', formModalName: 'mutation-form', deleteModalName: 'mutation-delete', submitButtonClass: 'bg-blue-600 hover:bg-blue-700', deleteButtonClass: 'bg-red-600 hover:bg-red-700' },
                adjustment: { title: 'Penyesuaian', subtitle: 'Kelola penyesuaian Anda', emptyMessage: 'Belum ada penyesuaian.', formModalName: 'adjustment-form', deleteModalName: 'adjustment-delete', submitButtonClass: 'bg-amber-600 hover:bg-amber-700', deleteButtonClass: 'bg-red-600 hover:bg-red-700' },
            };

            const config = typeConfig[this.type] || typeConfig.income;
            this.title = config.title;
            this.subtitle = config.subtitle;
            this.emptyMessage = config.emptyMessage;
            this.formModalName = config.formModalName;
            this.deleteModalName = config.deleteModalName;
            this.submitButtonClass = config.submitButtonClass;
            this.deleteButtonClass = config.deleteButtonClass;

            await this.loadTransactions();
        },

        async loadTransactions(page = 1) {
            const params = new URLSearchParams({
                page,
                ajax: 'true'
            });

            try {
                const response = await fetch(`${route('transactions.' + this.type + '.index')}?${params}`, {
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await response.json();
                this.transactions = data.data;
                this.pagination = data.pagination;
            } catch (error) {
                console.error('Failed to load transactions:', error);
            }
        },

        create() {
            this.resetForm();
            this.modal.mode = 'create';
            this.modal.form.type = this.type;
            this.modal.form.date = nowDateTime();
            this.modal.show = true;
        },

        edit(transaction) {
            this.resetForm();
            this.modal.mode = 'edit';
            this.modal.form = {
                id: transaction.id,
                period_id: transaction.period_id,
                category_id: transaction.category_id,
                account_id: transaction.account_id,
                type: transaction.type,
                amount: transaction.amount,
                note: transaction.note || '',
                date: transaction.date ? transaction.date.replace(' ', 'T') : nowDateTime()
            };
            this.modal.show = true;
        },

        confirmDelete(transaction) {
            this.deleteModal.transaction = transaction;
            this.deleteModal.show = true;
        },

        async submit() {
            this.modal.loading = true;
            this.modal.errors = {};

            try {
                const url = this.modal.mode === 'create'
                    ? route('transactions.' + this.type + '.store')
                    : route('transactions.' + this.type + '.update', this.modal.form.id);

                const method = this.modal.mode === 'create' ? 'POST' : 'PUT';

                const response = await fetch(url, {
                    method,
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(this.modal.form)
                });

                const data = await response.json();

                if (!response.ok) {
                    if (response.status === 422 && data.errors) {
                        this.modal.errors = data.errors;
                        this.$toast.error('Validasi gagal', 'Error');
                    } else {
                        throw new Error(data.message || 'Terjadi kesalahan');
                    }
                    return;
                }

                this.modal.show = false;
                this.$toast.success(data.message);
                await this.loadTransactions(this.pagination.current_page);
            } catch (error) {
                this.$toast.error(error.message);
            } finally {
                this.modal.loading = false;
            }
        },

        async deleteTransaction() {
            this.deleteModal.loading = true;

            try {
                const response = await fetch(route('transactions.' + this.type + '.destroy', this.deleteModal.transaction.id), {
                    method: 'DELETE',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Gagal menghapus transaksi');
                }

                this.deleteModal.show = false;
                this.$toast.success(data.message);
                this.transactions = this.transactions.filter(t => t.id !== this.deleteModal.transaction.id);
            } catch (error) {
                this.$toast.error(error.message);
            } finally {
                this.deleteModal.loading = false;
            }
        },

        resetForm() {
            this.modal.form = {
                id: null,
                period_id: '',
                category_id: '',
                account_id: '',
                type: this.type,
                amount: '',
                note: '',
                date: nowDateTime(),
            };
            this.modal.errors = {};
        },

        formatDate(dateStr) {
            return formatDate(dateStr);
        },

        formatDateTime(dateStr) {
            return formatDateTime(dateStr);
        },

        formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(Math.abs(amount));
        }
    }
};

window.periodPage = function () {
    return {
        modal: { show: false, mode: 'create', loading: false, errors: {}, form: { id: null, name: '', start_date: '', end_date: '', is_closed: false } },
        deleteModal: { show: false, loading: false, period: null },

        create() {
            this.modal.mode = 'create';
            this.modal.form = { id: null, name: '', start_date: '', end_date: '', is_closed: false };
            this.modal.errors = {};
            this.modal.show = true;
        },

        edit(id, name, startDate, endDate, isClosed) {
            this.modal.mode = 'edit';
            this.modal.form = { id, name, start_date: startDate, end_date: endDate, is_closed: isClosed };
            this.modal.errors = {};
            this.modal.show = true;
        },

        confirmDelete(id, name) {
            this.deleteModal.period = { id, name };
            this.deleteModal.show = true;
        },

        async submit() {
            this.modal.loading = true;
            this.modal.errors = {};
            try {
                const url = this.modal.mode === 'create' ? route('periods.store') : route('periods.update', this.modal.form.id);
                const method = this.modal.mode === 'create' ? 'POST' : 'PUT';
                const response = await fetch(url, {
                    method, credentials: 'same-origin',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: JSON.stringify(this.modal.form)
                });
                const data = await response.json();
                if (!response.ok) {
                    if (response.status === 422 && data.errors) { this.modal.errors = data.errors; this.$toast.error('Validasi gagal', 'Error'); }
                    else { throw new Error(data.message || 'Terjadi kesalahan'); }
                    return;
                }
                this.modal.show = false;
                this.$toast.success(data.message);
                location.reload();
            } catch (error) { this.$toast.error(error.message); }
            finally { this.modal.loading = false; }
        },

        async destroy() {
            this.deleteModal.loading = true;
            try {
                const response = await fetch(route('periods.destroy', this.deleteModal.period.id), {
                    method: 'DELETE', credentials: 'same-origin',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await response.json();
                if (!response.ok) throw new Error(data.message || 'Gagal menghapus periode');
                this.deleteModal.show = false;
                this.$toast.success(data.message);
                location.reload();
            } catch (error) { this.$toast.error(error.message); }
            finally { this.deleteModal.loading = false; }
        }
    }
};

window.accountPage = function () {
    return {
        modal: { show: false, mode: 'create', loading: false, errors: {}, form: { id: null, name: '', type: 'cash', description: '' } },
        deleteModal: { show: false, loading: false, account: null },

        create() {
            this.modal.mode = 'create';
            this.modal.form = { id: null, name: '', type: 'cash', description: '' };
            this.modal.errors = {};
            this.modal.show = true;
        },

        edit(id, name, type, description) {
            this.modal.mode = 'edit';
            this.modal.form = { id, name, type, description: description || '' };
            this.modal.errors = {};
            this.modal.show = true;
        },

        confirmDelete(id, name) {
            this.deleteModal.account = { id, name };
            this.deleteModal.show = true;
        },

        async submit() {
            this.modal.loading = true;
            this.modal.errors = {};
            try {
                const url = this.modal.mode === 'create' ? route('accounts.store') : route('accounts.update', this.modal.form.id);
                const method = this.modal.mode === 'create' ? 'POST' : 'PUT';
                const response = await fetch(url, {
                    method, credentials: 'same-origin',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: JSON.stringify(this.modal.form)
                });
                const data = await response.json();
                if (!response.ok) {
                    if (response.status === 422 && data.errors) { this.modal.errors = data.errors; this.$toast.error('Validasi gagal', 'Error'); }
                    else { throw new Error(data.message || 'Terjadi kesalahan'); }
                    return;
                }
                this.modal.show = false;
                this.$toast.success(data.message);
                location.reload();
            } catch (error) { this.$toast.error(error.message); }
            finally { this.modal.loading = false; }
        },

        async destroy() {
            this.deleteModal.loading = true;
            try {
                const response = await fetch(route('accounts.destroy', this.deleteModal.account.id), {
                    method: 'DELETE', credentials: 'same-origin',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await response.json();
                if (!response.ok) throw new Error(data.message || 'Gagal menghapus dompet');
                this.deleteModal.show = false;
                this.$toast.success(data.message);
                location.reload();
            } catch (error) { this.$toast.error(error.message); }
            finally { this.deleteModal.loading = false; }
        }
    }
};

window.mutationPage = function () {
    return {
        modal: { show: false, mode: 'create', loading: false, errors: {}, form: { id: null, period_id: '', from_account_id: '', to_account_id: '', amount: '', note: '', date: nowDateTime() } },
        deleteModal: { show: false, loading: false, mutation: null },

        periods: JSON.parse(document.querySelector('[data-periods]')?.getAttribute('data-periods') || '[]'),
        accounts: JSON.parse(document.querySelector('[data-accounts]')?.getAttribute('data-accounts') || '[]'),

        create() {
            this.modal.mode = 'create';
            this.modal.form = { id: null, period_id: '', from_account_id: '', to_account_id: '', amount: '', note: '', date: nowDateTime() };
            this.modal.errors = {};
            this.modal.show = true;
        },

        edit(mutation) {
            this.modal.mode = 'edit';
            this.modal.form = {
                id: mutation.id,
                period_id: mutation.period_id,
                from_account_id: mutation.from_account_id,
                to_account_id: mutation.to_account_id,
                amount: mutation.amount,
                note: mutation.note || '',
                date: mutation.date ? mutation.date.replace(' ', 'T') : nowDateTime()
            };
            this.modal.errors = {};
            this.modal.show = true;
        },

        confirmDelete(id) {
            this.deleteModal.mutation = { id };
            this.deleteModal.show = true;
        },

        async submit() {
            this.modal.loading = true;
            this.modal.errors = {};
            try {
                const url = this.modal.mode === 'create' ? route('mutations.store') : route('mutations.update', this.modal.form.id);
                const method = this.modal.mode === 'create' ? 'POST' : 'PUT';
                const response = await fetch(url, {
                    method, credentials: 'same-origin',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: JSON.stringify(this.modal.form)
                });
                const data = await response.json();
                if (!response.ok) {
                    if (response.status === 422 && data.errors) { this.modal.errors = data.errors; this.$toast.error('Validasi gagal', 'Error'); }
                    else { throw new Error(data.message || 'Terjadi kesalahan'); }
                    return;
                }
                this.modal.show = false;
                this.$toast.success(data.message);
                location.reload();
            } catch (error) { this.$toast.error(error.message); }
            finally { this.modal.loading = false; }
        },

        async destroy() {
            this.deleteModal.loading = true;
            try {
                const response = await fetch(route('mutations.destroy', this.deleteModal.mutation.id), {
                    method: 'DELETE', credentials: 'same-origin',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await response.json();
                if (!response.ok) throw new Error(data.message || 'Gagal menghapus mutasi');
                this.deleteModal.show = false;
                this.$toast.success(data.message);
                location.reload();
            } catch (error) { this.$toast.error(error.message); }
            finally { this.deleteModal.loading = false; }
        }
    }
};

window.adjustmentPage = function () {
    return {
        modal: { show: false, mode: 'create', loading: false, errors: {}, form: { id: null, period_id: '', type: 'income', amount: '', note: '', date: nowDateTime() } },
        deleteModal: { show: false, loading: false, adjustment: null },

        periods: JSON.parse(document.querySelector('[data-periods]')?.getAttribute('data-periods') || '[]'),

        create() {
            this.modal.mode = 'create';
            this.modal.form = { id: null, period_id: '', type: 'income', amount: '', note: '', date: nowDateTime() };
            this.modal.errors = {};
            this.modal.show = true;
        },

        edit(adjustment) {
            this.modal.mode = 'edit';
            this.modal.form = {
                id: adjustment.id,
                period_id: adjustment.period_id,
                type: adjustment.type,
                amount: adjustment.amount,
                note: adjustment.note || '',
                date: adjustment.date ? adjustment.date.replace(' ', 'T') : nowDateTime()
            };
            this.modal.errors = {};
            this.modal.show = true;
        },

        confirmDelete(id) {
            this.deleteModal.adjustment = { id };
            this.deleteModal.show = true;
        },

        async submit() {
            this.modal.loading = true;
            this.modal.errors = {};
            try {
                const url = this.modal.mode === 'create' ? route('adjustments.store') : route('adjustments.update', this.modal.form.id);
                const method = this.modal.mode === 'create' ? 'POST' : 'PUT';
                const response = await fetch(url, {
                    method, credentials: 'same-origin',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: JSON.stringify(this.modal.form)
                });
                const data = await response.json();
                if (!response.ok) {
                    if (response.status === 422 && data.errors) { this.modal.errors = data.errors; this.$toast.error('Validasi gagal', 'Error'); }
                    else { throw new Error(data.message || 'Terjadi kesalahan'); }
                    return;
                }
                this.modal.show = false;
                this.$toast.success(data.message);
                location.reload();
            } catch (error) { this.$toast.error(error.message); }
            finally { this.modal.loading = false; }
        },

        async destroy() {
            this.deleteModal.loading = true;
            try {
                const response = await fetch(route('adjustments.destroy', this.deleteModal.adjustment.id), {
                    method: 'DELETE', credentials: 'same-origin',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await response.json();
                if (!response.ok) throw new Error(data.message || 'Gagal menghapus adjustment');
                this.deleteModal.show = false;
                this.$toast.success(data.message);
                location.reload();
            } catch (error) { this.$toast.error(error.message); }
            finally { this.deleteModal.loading = false; }
        }
    }
};

Alpine.start();