import * as types from '../../mutation-types';

export default {
    [types.LOGIN](state, payload) {
        window.localStorage.setItem('token', payload.token);
        window.axios.defaults.headers.common.Authorization = `Bearer ${payload.token}`;

        state.token = payload.token;
        state.logged = true;
    },
    [types.LOGOUT](state) {
        window.localStorage.removeItem('token');
        window.axios.defaults.headers.common.Authorization = '';

        state.token = '';
        state.logged = false;
    },
    [types.ID](state, payload) {
        state.id = payload;
    },
    [types.NAME](state, payload) {
        state.name = payload;
    },
    [types.EMAIL](state, payload) {
        state.email = payload;
    },
    [types.PASSWORD](state, payload) {
        state.password = payload;
    },
    [types.PASSWORD_CONFIRMATION](state, payload) {
        state.passwordConfirmation = payload;
    },
    [types.CREATED_AT](state, payload) {
        state.createdAt = payload;
    },
    [types.UPDATED_AT](state, payload) {
        state.updatedAt = payload;
    },
};
