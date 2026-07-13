import Alpine from 'alpinejs';

window.Alpine = Alpine;

window.categoryPage = function () {
    return {
        openForm: false,
        openDelete: false,
        mode: 'create',

        form: {
            id: null,
            name: '',
            type: 'income',
            budget_limit: 0,
        },

        create() {
            this.mode = 'create';

            this.form = {
                id: null,
                name: '',
                type: 'income',
                budget_limit: 0,
            };

            this.openForm = true;
        },

        edit(category) {
            this.mode = 'edit';
            this.form = { ...category };
            this.openForm = true;
        },

        remove(category) {
            this.form = { ...category };
            this.openDelete = true;
        }
    }
}

Alpine.start();
