import * as types from '../store/mutation-types';

export default {
    computed: {
        logged() {
            return this.$store.getters.logged;
        },
        name: {
            get() {
                return this.$store.getters.name;
            },
            set(value) {
                this.$store.commit(types.NAME, value);
            },
        },
        email: {
            get() {
                return this.$store.getters.email;
            },
            set(value) {
                this.$store.commit(types.EMAIL, value);
            },
        },
        password: {
            get() {
                return this.$store.getters.password;
            },
            set(value) {
                this.$store.commit(types.PASSWORD, value);
            },
        },
        passwordConfirmation: {
            get() {
                return this.$store.getters.passwordConfirmation;
            },
            set(value) {
                this.$store.commit(types.PASSWORD_CONFIRMATION, value);
            },
        },
    },
};
