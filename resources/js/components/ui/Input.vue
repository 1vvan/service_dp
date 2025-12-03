<template>
    <input
        :type="type"
        :class="inputClasses"
        v-bind="$attrs"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
    />
</template>

<script>
import { cn } from '../../lib/utils';

export default {
    name: 'Input',
    inheritAttrs: false,
    emits: ['update:modelValue'],
    props: {
        modelValue: {
            type: [String, Number],
            default: ''
        },
        type: {
            type: String,
            default: 'text'
        }
    },
    computed: {
        inputClasses() {
            return cn('input', this.$attrs.class);
        }
    }
};
</script>

<style scoped>
.input {
    flex: 1;
    height: 2.5rem;
    width: 100%;
    border-radius: calc(var(--radius) - 2px);
    border: 1px solid hsl(var(--input));
    background-color: hsl(var(--background));
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
    color: hsl(var(--foreground));
    transition: all 0.2s ease;
}

.input::placeholder {
    color: hsl(var(--muted-foreground));
}

.input:focus-visible {
    outline: none;
    box-shadow: 0 0 0 2px hsl(var(--ring));
}

.input:disabled {
    cursor: not-allowed;
    opacity: 0.5;
}

@media (min-width: 768px) {
    .input {
        font-size: 0.875rem;
    }
}
</style>
