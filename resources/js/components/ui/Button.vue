<template>
    <button
        :class="buttonClasses"
        v-bind="$attrs"
        @click="$emit('click', $event)"
    >
        <slot />
    </button>
</template>

<script>
import { cn } from '../../lib/utils';

export default {
    name: 'Button',
    inheritAttrs: false,
    emits: ['click'],
    props: {
        variant: {
            type: String,
            default: 'default',
            validator: (value) => [
                'default',
                'destructive',
                'outline',
                'secondary',
                'ghost',
                'link',
                'hero',
                'hero-outline'
            ].includes(value)
        },
        size: {
            type: String,
            default: 'default',
            validator: (value) => ['sm', 'default', 'lg', 'xl', 'icon'].includes(value)
        }
    },
    computed: {
        buttonClasses() {
            const baseClasses = 'btn';
            const variantClass = `btn--${this.variant}`;
            const sizeClass = this.size === 'default' ? 'btn--default-size' : `btn--${this.size}`;
            
            return cn(baseClasses, variantClass, sizeClass);
        }
    }
};
</script>
