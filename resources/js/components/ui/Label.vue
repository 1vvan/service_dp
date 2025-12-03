<template>
    <label :class="labelClasses" v-bind="$attrs">
        <slot />
    </label>
</template>

<script>
import { cn } from '../../lib/utils';

export default {
    name: 'Label',
    inheritAttrs: false,
    props: {
        disabled: {
            type: Boolean,
            default: false
        }
    },
    computed: {
        labelClasses() {
            return cn(
                'label',
                this.disabled && 'disabled',
                this.$attrs.class
            );
        }
    }
};
</script>

<style scoped>
.label {
    font-size: 0.875rem;
    font-weight: 500;
    line-height: 1;
    color: hsl(var(--foreground));
}

.label.disabled {
    cursor: not-allowed;
    opacity: 0.7;
}
</style>
