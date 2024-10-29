<script setup>
import { ref, computed, inject } from 'vue';
import _ from 'lodash-es';
import { watch } from 'vue';
import { Link, router, useForm, usePage } from '@inertiajs/vue3';

const props = defineProps({
    item: {
        type: Object,
        default: {},
    },
});

const item = ref(props.item);
const { el_model, el_controller } = inject('GLOBAL_CREATE_AND_EDIT');

const vDraggable = (el, binding) => {

    let mainEl = document.getElementById(item.value.id);
    let rect = mainEl.getBoundingClientRect();

    // 设置拖动元素的初始位置和尺寸
    el.draggable = "true";
    let mouseStartX = 0;
    let mouseStartY = 0;
    let elStartX = 0;
    let elStartY = 0;
    let isDown = false;

    el.ondragstart = () => false;
    el.addEventListener('mousedown', (e) => {
        e.preventDefault();

        el_controller.active(item.value.id);

        isDown = true;
        elStartX = mainEl.offsetLeft;
        elStartY = mainEl.offsetTop;
        mouseStartX = e.pageX;
        mouseStartY = e.pageY;
    });

    document.addEventListener('mousemove', (e) => {

        if (!isDown) return;
        e.preventDefault();

        var x = e.pageX - mouseStartX + elStartX;
        var y = e.pageY - mouseStartY + elStartY;

        mainEl.style.position = 'absolute';
        mainEl.style.top = `${y}px`;
        mainEl.style.left = `${x}px`;

        _.set(item.value, `style`, mainEl.style.cssText);
    });

    document.addEventListener('mouseup', (e) => {
        e.preventDefault();
        isDown = false;
    });
};

const vResize = (el, binding) => {

    // 设置拖动元素的初始位置和尺寸
    el.draggable = "true";
    let mouseStartX = 0;
    let mouseStartY = 0;
    let elStartX = 0;
    let elStartY = 0;
    let isDown = false;

    let itemEl = document.getElementById(`${item.value.id}`);
    let rect = itemEl.getBoundingClientRect();

    el.ondragstart = () => false;
    el.addEventListener('mousedown', (e) => {
        e.preventDefault();

        el_controller.active(item.value.id);

        isDown = true;
        elStartX = rect.width;
        elStartY = rect.height;
        mouseStartX = e.pageX;
        mouseStartY = e.pageY;
    });

    document.addEventListener('mousemove', (e) => {
        e.preventDefault();

        if (!isDown) return;

        var width = e.pageX - mouseStartX + elStartX;
        var height = e.pageY - mouseStartY + elStartY;

        itemEl.style.position = 'absolute';
        itemEl.style.width = `${width}px`;
        itemEl.style.height = `${height}px`;

        _.set(item.value, `style`, itemEl.style.cssText);
        _.set(item.value, `width`, width.toFixed(0));
        _.set(item.value, `height`, height.toFixed(0));
    });

    document.addEventListener('mouseup', (e) => {
        e.preventDefault();

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
        el_controller.update(item.value.id, newVal);
    },
    { deep: true }
)

const form = ref({
    file: null,
})
const itemUpload = async (e) => {

    form.file = e.target.files[0];

    router.post(route('ads-image.upload'), form, {
        forceFormData: true,
        replace: true,
        onSuccess: (page) => {
            item.value.src = page.props.flash?.upload?.url;
        }
    });
}

</script>

<template>
    <div @click="el_controller.active(item.id)" :id="item.id" class="absolute select-none"
        :style="[item.style, `width: ${item.width}px; height: ${item.height}px;`]" :class="[
            { 'ring ring-indigo-100 ring-offset-1': item.active },
            item.class,
        ]">
        <!-- configuration -->
        <Teleport defer to="#configContainer">
            <div v-if="item.active" class="flex flex-col">
                <!-- size -->
                <div class="w-full">
                    <label for="">宽 - 高 (px)</label>
                    <div class="flex flex-row w-fit gap-x-2 align-middle items-center">
                        <input id="item-width" v-model="item.width" class="w-20"
                            :placeholder="item.width ?? `宽`"></input>
                        <span> - </span>
                        <input id="item-height" v-model="item.height" class="w-20"
                            :placeholder="item.height ?? `高`"></input>
                    </div>
                </div>

                <!-- item -->
                <div class="w-full">
                    <label for="item-src">URL</label>
                    <div class="flex flex-row w-fit gap-x-2 align-middle items-center">
                        <input id="item-src" v-model="item.src" class="w-full text-left truncate"
                            placeholder="请贴入背景URL" />
                        <!-- custom file upload -->
                        <form class="flex items-center justify-center bg-white w-14 aspect-square">
                            <div
                                class="h-full rounded border border-gray-400 flex justify-center items-center hover:bg-blue-200">
                                <div class="absolute">
                                    <div class="flex items-center">
                                        <span class="block text-gray-800 font-normal">传</span>
                                    </div>
                                </div>
                                <input id="itemFile" type="file" class="h-full w-full opacity-0" name="itemFile"
                                    @input="itemUpload($event)">
                            </div>
                        </form>

                    </div>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-300" id="file_input_help">SVG, PNG, JPG</p>
                </div>

                <!-- class -->
                <div>
                    <label for="item-class">Class</label>
                    <textarea id="item-class" v-model="item.class"></textarea>
                </div>
                <!-- style -->
                <div>
                    <label for="item-style">Style</label>
                    <textarea id="item-style" v-model="item.style"></textarea>
                </div>
            </div>
        </Teleport>

        <div class="w-full h-full relative bg-contain bg-no-repeat" :style="`background-image:url(${item.src})`">
            <div v-if="item.active" @click.prevent="el_controller.del(item.id)"
                class="absolute top-0 right-0 translate-x-1/2 -translate-y-1/2 bg-black/80 ring ring-white rounded-full aspect-square w-9 grid items-center justify-center">
                <span class='text-base'>❌</span>
            </div>
            <div v-resize v-if="item.active"
                class="absolute bottom-0 right-0 translate-x-1/2 translate-y-1/2 bg-white ring ring-white rounded-full aspect-square w-[2vmin] grid items-center justify-center shadow">
            </div>
            <div v-draggable :src="item.src" alt="图片" class="w-full h-full" />
        </div>
    </div>
</template>