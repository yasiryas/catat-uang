import Alpine from 'alpinejs';
import Swal from 'sweetalert2';

window.Alpine = Alpine;
window.Swal = Swal;

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

                this.$toast.success(data.message);

                if (this.modal.mode === 'create') {
                    this.categories.unshift(data.data);
                } else {
                    const index = this.categories.findIndex(c => c.id === data.data.id);
                    if (index !== -1) this.categories[index] = data.data;
                }

                this.modal.show = false;
            } catch (error) {
                this.$toast.error(error.message);
            } finally {
                this.modal.loading = false;
            }
        },

async delete() {
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

                this.$toast.success(data.message);
                this.categories = this.categories.filter(c => c.id !== this.deleteModal.category.id);
                this.deleteModal.show = false;
            } catch (error) {
                this.$toast.error(error.message);
            } finally {
                this.deleteModal.loading = false;
            }
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
                date: new Date().toISOString().split('T')[0],
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
            this.modal.form.date = new Date().toISOString().split('T')[0];
            this.modal.show = true;
        },

        edit(transaction) {
            this.resetForm();
            this.modal.mode = 'edit';
            this.modal.form = {
                id: transaction.id,
                period_id: transaction.period_id,
                category_id: transaction.category_id,
                type: transaction.type,
                amount: transaction.amount,
                note: transaction.note || '',
                date: transaction.date
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

                this.$toast.success(data.message);
                this.modal.show = false;
                await this.loadTransactions(this.pagination.current_page);
            } catch (error) {
                this.$toast.error(error.message);
            } finally {
                this.modal.loading = false;
            }
        },

async delete() {
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

                this.$toast.success(data.message);
                this.transactions = this.transactions.filter(t => t.id !== this.deleteModal.transaction.id);
                this.deleteModal.show = false;
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
                type: 'income',
                amount: '',
                note: '',
                date: new Date().toISOString().split('T')[0],
            };
            this.modal.errors = {};
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

Alpine.start();