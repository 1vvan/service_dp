<template>
    <el-input
        :model-value="modelValue"
        type="tel"
        :placeholder="placeholder"
        :size="size"
        :clearable="clearable"
        maxlength="19"
        @update:model-value="onInput"
        @focus="onFocus"
        @clear="onClear"
    />
</template>

<script>
import { formatPhone, PHONE_INPUT_PREFIX } from '../../lib/utils';

export default {
    name: 'PhoneInput',
    props: {
        modelValue: {
            type: String,
            default: '',
        },
        size: {
            type: String,
            default: 'default',
        },
        placeholder: {
            type: String,
            default: '+380 (00) 000 00 00',
        },
        clearable: {
            type: Boolean,
            default: false,
        },
        /** Підставити префікс маски одразу при фокусі, якщо поле порожнє */
        prefixOnFocus: {
            type: Boolean,
            default: true,
        },
    },
    emits: ['update:modelValue'],
    methods: {
        onInput(value) {
            const v = value ?? '';
            this.$emit('update:modelValue', formatPhone(v));
        },
        onFocus() {
            if (!this.prefixOnFocus) {
                return;
            }
            const v = (this.modelValue || '').trim();
            if (v === '') {
                this.$emit('update:modelValue', PHONE_INPUT_PREFIX);
            }
        },
        onClear() {
            this.$emit('update:modelValue', '');
        },
    },
};
</script>
