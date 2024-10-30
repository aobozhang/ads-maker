<script setup>
import { ref, computed, inject } from 'vue';
import _ from 'lodash-es';
import Text from "./SubEl/Text.vue";
import Image from "./SubEl/Image.vue";
import Main from "./SubEl/Main.vue";

const cpt = {
    text: Text,
    picture: Image,
    main: Main,
}

const props = defineProps({
    item: {
        type: Object,
        default: {},
    },
    isActived: {
        type: Boolean,
        default: false,
    }
});

const item = ref(props.item);
const { el_model, el_ctl } = inject('GLOBAL_CREATE_AND_EDIT');

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

        _.set(item.value, `prop.style.left`, el.style.left);
        _.set(item.value, `prop.style.top`, el.style.top);
    });

    document.addEventListener('mouseup', () => {
        isDown = false;
    });
};

const emit = defineEmits([
    'clk',
]);


</script>

<template>
    <component :is="cpt[item.type]" :item="item" :isActived="isActived" />
</template>