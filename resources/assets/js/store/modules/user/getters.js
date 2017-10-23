export default {
    id: state => state.id,
    name: state => state.name,
    email: state => state.email,
    logged: state => state.logged,
    token: state => state.token,
    password: state => state.password,
    passwordConfirmation: state => state.passwordConfirmation,
    createdAt: state => state.createdAt,
    updatedAt: state => state.updatedAt,
};
