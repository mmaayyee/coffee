/*
 * 基于bootstrap3-validation.js扩展的表单验证
 * */
$.extend(
    $.fn.validation.defaults.validRules.push(
    {
        name: 'plus',
        validate: function(value) {
            return (!/^(|[+]{0,1}(\d+)$|^[+]{0,1}(\d+\.\d+))$/.test(value));
        },
        defaultMsg: '请输入正数。'
    },
    {
        name: 'ints',
        validate: function(value) {
            return (!/^(|[1-9]\d*)$/.test(value));
        },
        defaultMsg: '请输入大于0的整数。'
    },
    {
        name: 'nonnegativeInteger',
        validate: function(value) {
            return (!/^(0|[1-9]\d*)$/.test(value));
        },
        defaultMsg: '请输入非负整数。'
    }
));