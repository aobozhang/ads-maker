<script setup>
import { ref, computed, inject } from 'vue';
import _ from 'lodash-es';
import { watch } from 'vue';

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
    let elCurrentX = 0;
    let elCurrentY = 0;
    let isDown = false;

    el.ondragstart = () => false;
    el.addEventListener('mousedown', (e) => {
        isDown = true;
        elStartX = el.offsetLeft;
        elStartY = el.offsetTop;
        mouseStartX = e.pageX;
        mouseStartY = e.pageY;

        el.addEventListener('mousemove', onMouseMove);
        el.addEventListener('mouseup', onMouseUp);
    });

    const onMouseUp = () => {
        isDown = false;
        _.set(item.value, `style`, el.style.cssText);

        el.removeEventListener('mousemove', onMouseMove);
        el.removeEventListener('mouseup', onMouseUp);

    }

    const updatePosition = () => {
        el.style.top = `${elCurrentY}px`;
        el.style.left = `${elCurrentX}px`;

        console.log(rect.width);
    }

    const onMouseMove = (e) => {
        if (!isDown) return;

        e.preventDefault();

        elCurrentX = e.pageX - mouseStartX + elStartX;
        elCurrentY = e.pageY - mouseStartY + elStartY;

        window.requestAnimationFrame(updatePosition);
    }

    el.addEventListener('mousemove', onMouseMove);

    el.addEventListener('mouseup', () => {
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
        el_ctl.updateData(item.value.id, newVal);
    },
    { deep: true }
)

</script>

<template>
    <div @click="el_ctl.active(item)" v-draggable class="absolute select-none" :style="item.style" :class="[
        { 'ring-1 ring-pink-100 ring-offset-4': isActived },
        item.class,
        'z-40'
    ]">
        <!-- configuration -->
        <Teleport defer to="#configContainer">
            <div v-if="isActived" class="flex flex-col h-full text-sm gap-y-4 py-4">
                <!-- content -->
                <div class="grow w-full flex flex-col">
                    <label for="text-content">内容</label>
                    <textarea class="grow" id="text-content" v-model="item.innerHTML"
                        :placeholder="item.placeholder"></textarea>
                </div>
                <!-- class -->
                <div class="grow w-full flex flex-col">
                    <label for="text-class">Class</label>
                    <textarea class="grow" id="text-class" v-model="item.class"></textarea>
                </div>
                <!-- style -->
                <div class="grow w-full flex flex-col">
                    <label for="text-style">Style</label>
                    <textarea class="grow" id="text-style" v-model="item.style"></textarea>
                </div>
            </div>
        </Teleport>

        <div class="w-full h-full relative">
            <div v-if="isActived" @click.prevent="el_ctl.del(item.id)"
                class="absolute top-0 right-0 translate-x-full -translate-y-full bg-black/80 ring ring-white rounded-full aspect-square w-9 grid items-center justify-center">
                <span class='text-base'>❌</span>
            </div>
            <div v-html="item.innerHTML ?? item.placeholder"></div>
        </div>
    </div>
</template>
