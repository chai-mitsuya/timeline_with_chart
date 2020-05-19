let rules = {
    "no-unused-vars": ["warn", {"vars": "local", "args": "none"}],
    "no-undef": "warn",
    "no-redeclare": "warn",
    "no-debugger": "warn",
    "no-console": "warn",
    "no-empty": "warn"
};
if (process.env.NODE_ENV == "production") {
    rules["no-debugger"] ="error";
    rules["no-console"] ="error";
}

module.exports = {
    extends: [
        "eslint:recommended",
        "plugin:vue/essential" // vue.js使ってなくてもlaravel-mix-eslintの仕様上必要
    ],
    env: {
        browser: true,
        es6: true,
        amd: true,
        jquery: true
    },
    parserOptions: {
        ecmaVersion: "2018"
    },
    rules
};
