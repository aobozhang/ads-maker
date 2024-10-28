<script setup>
import { ref, computed } from 'vue';
import _ from 'lodash-es';

const props = defineProps({
    item: {
        type: Object,
        default: {},
    },
});


const item = ref(props.item);
const elements = computed(() => _.get(item.value, 'elements', []));


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

const clk = (e) => {
    emit('clk', e);
}

const styleStr = computed(
    () => _.reduce(
        _.get(item.value, 'prop.style', {}),
        function (res, val, key) {
            return res.concat(`${key}: ${val};`);
        }, String(''))
);

const editMe = (e) => {
    e.preventDefault();

    console.log(e.target)
}

</script>

<template>
    <div :style="styleStr" class="absolute" @click="editMe">

        <!-- configuration -->
        <Teleport defer to="#configContainer">
            <div v-for="(val, key, index) in item.prop.style" :key="index">
                {{ key }}: {{ val }}
            </div>
        </Teleport>

        <span v-if="item.text" class="select-none">{{ item.text }}</span>
        <img v-if="item.image" :src="item.image" alt="item.image">
        <div class="relative w-full h-full">
            <Element v-for="(el, i) in elements" :key="i" :item="el" class="absolute -translate-x-1/2 -translate-y-1/2"
                v-draggable>
            </Element>
        </div>
        <slot />
    </div>
</template>