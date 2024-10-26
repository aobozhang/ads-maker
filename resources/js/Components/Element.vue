<script setup>
import { h, computed } from 'vue';
import _ from 'loadsh-es';

const props = defineProps({
    config: {
        type: Object,
        default: {},
    }
});

const attributes = computed(() => _.get(props.config, 'attributes'))

const emit = defineEmits([
    'clk',
]);

const vnode = () => {
    return h(
        config.type,
        config.props,
        config.children.map((chld) => {
            h(chld)
        }),
        config.key,
    )
}

</script>

<template>
    <vnode class="absolute -translate-x-1/2 -translate-y-1/2"
        :style="`left: ${attributes.left}; top: ${attributes.top}; width: ${attributes.width}; height:${attributes.height};`" />
</template>