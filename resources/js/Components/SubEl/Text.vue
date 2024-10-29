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

    const oriWidth = rect.width;
    const oriHeight = rect.height;

    // 设置拖动元素的初始位置和尺寸
    el.draggable = "true";
    let mouseStartX = 0;
    let mouseStartY = 0;
    let elStartX = 0;
    let elStartY = 0;
    let isDown = false;

    el.ondragstart = () => false;
    el.addEventListener('mousedown', (e) => {

        el_controller.active(item.value.id);

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
        el.style.width = `${oriWidth}px`;
        el.style.height = `${oriHeight}px`;

        _.set(item.value, `style`, el.style.cssText);
    });

    document.addEventListener('mouseup', () => {
        isDown = false;
        el.style.width = 'fit-content';
        el.style.height = 'fit-content';
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

</script>

<template>
    <div v-draggable class="absolute select-none" :style="item.style" :class="[
        { 'ring-1 ring-pink-100 ring-offset-4': item.active },
        item.class,
    ]">
        <!-- configuration -->
        <Teleport defer to="#configContainer">
            <div v-if="item.active" class="flex flex-col">
                <!-- content -->
                <div>
                    <label for="text-content">内容</label>
                    <textarea id="text-content" v-model="item.innerHTML" :placeholder="item.placeholder"></textarea>
                </div>
                <!-- class -->
                <div>
                    <label for="text-class">Class</label>
                    <textarea id="text-class" v-model="item.class"></textarea>
                </div>
                <!-- style -->
                <div>
                    <label for="text-style">Style</label>
                    <textarea id="text-style" v-model="item.style"></textarea>
                </div>
            </div>
        </Teleport>

        <div class="w-full h-full relative">
            <div v-if="item.active" @click="el_controller.del(item.id)"
                class="absolute top-0 right-0 translate-x-full -translate-y-full bg-black/80 ring ring-white rounded-full aspect-square w-9 grid items-center justify-center">
                <span class='text-base'>❌</span>
            </div>
            <div class="whitespace-pre-line" v-html="item.innerHTML ?? item.placeholder"></div>
        </div>
    </div>
</template>