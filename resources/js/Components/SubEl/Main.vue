<script setup>
import { ref, computed, inject } from 'vue';
import _ from 'lodash-es';
import { watch } from 'vue';

const props = defineProps({
    item: {
        type: Object,
        default: {},
    },
});

const item = ref(props.item);
const { el_model, el_controller } = inject('GLOBAL_CREATE_AND_EDIT');

const vDraggable = (el, binding) => {

    let rect = el.getBoundingClientRect();

    // 设置拖动元素的初始位置和尺寸
    el.draggable = "true";
    let mouseStartX = 0;
    let mouseStartY = 0;
    let elStartX = 0;
    let elStartY = 0;
    let isDown = false;

    el.ondragstart = () => false;
    el.addEventListener('mousedown', (e) => {
        isDown = true;
        elStartX = el.offsetLeft;
        elStartY = el.offsetTop;
        mouseStartX = e.pageX;
        mouseStartY = e.pageY;
    });

    document.addEventListener('mousemove', (e) => {

        if (!isDown) return;

        var x = e.pageX - mouseStartX + elStartX;
        var y = e.pageY - mouseStartY + elStartY;

        el.style.position = 'absolute';
        el.style.top = `${y}px`;
        el.style.left = `${x}px`;

        _.set(item.value, `style`, el.style.cssText);
    });

    document.addEventListener('mouseup', () => {
        isDown = false;
    });
};

const emit = defineEmits([
    'clk',
]);

const clk = (e) => {
    emit('clk', e);
}

watch(
    item,
    (newVal, oldVal) => {
        el_controller.update(item.value.id, item.value);
    },
    { deep: true }
)

const editMe = (e) => {
    e.preventDefault();

    console.log(e.target)
}

</script>

<template>
    <div class="absolute w-full h-full pointer-events-none">

        <div :class="item.class" class="pointer-events-none w-full h-full" :style="`background-image:url(${item.src})`">
        </div>
    </div>
</template>