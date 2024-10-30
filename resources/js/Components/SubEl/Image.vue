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
    isActived: {
        type: Boolean,
        default: false,
    }
});

const item = ref(props.item);
const { el_ctl } = inject('GLOBAL_CREATE_AND_EDIT');

const vDraggable = (el, binding) => {

    let mainEl = document.getElementById(item.value.id);
    let rect = mainEl.getBoundingClientRect();

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
        e.preventDefault();

        isDown = true;
        elStartX = mainEl.offsetLeft;
        elStartY = mainEl.offsetTop;
        mouseStartX = e.pageX;
        mouseStartY = e.pageY;

        el.addEventListener('mousemove', onMouseMove);
        el.addEventListener('mouseup', onMouseUp);
    });

    const updatePosition = () => {
        mainEl.style.position = 'absolute';
        mainEl.style.top = `${elCurrentY}px`;
        mainEl.style.left = `${elCurrentX}px`;

    }

    const onMouseMove = (e) => {
        if (!isDown) return;
        e.preventDefault();
        elCurrentX = e.pageX - mouseStartX + elStartX;
        elCurrentY = e.pageY - mouseStartY + elStartY;
        window.requestAnimationFrame(updatePosition);
    }
    const onMouseUp = (e) => {
        e.preventDefault();
        isDown = false;
        _.set(item.value, `style`, mainEl.style.cssText);

        el.removeEventListener('mousemove', onMouseMove);
        el.removeEventListener('mouseup', onMouseUp);
    }
};

const vResize = (el, binding) => {

    // 设置拖动元素的初始位置和尺寸
    el.draggable = "true";
    let mouseStartX = 0;
    let mouseStartY = 0;
    let elStartX = 0;
    let elStartY = 0;
    let elCurrentX = 0;
    let elCurrentY = 0;
    let isDown = false;

    let itemEl = document.getElementById(`${item.value.id}`);
    let rect = itemEl.getBoundingClientRect();

    el.ondragstart = () => false;
    el.addEventListener('mousedown', (e) => {
        e.preventDefault();
        isDown = true;
        elStartX = rect.width;
        elStartY = rect.height;
        mouseStartX = e.pageX;
        mouseStartY = e.pageY;

        document.addEventListener('mousemove', onMouseMove);
        document.addEventListener('mouseup', onMouseUp);
    });

    const updateRect = () => {

        itemEl.style.position = 'absolute';
        itemEl.style.width = `${elCurrentX.toFixed(0)}px`;
        itemEl.style.height = `${elCurrentY.toFixed(0)}px`;
    }

    const onMouseMove = (e) => {
        e.preventDefault();
        if (!isDown) {
            return;
        }
        elCurrentX = e.pageX - mouseStartX + elStartX;
        elCurrentY = e.pageY - mouseStartY + elStartY;
        window.requestAnimationFrame(updateRect);
    };

    const onMouseUp = (e) => {
        e.preventDefault();
        isDown = false;

        _.set(item.value, `style`, itemEl.style.cssText);
        _.set(item.value, `width`, elCurrentX.toFixed(0));
        _.set(item.value, `height`, elCurrentY.toFixed(0));

        document.removeEventListener('mousemove', onMouseMove);
        document.removeEventListener('mouseup', onMouseUp);
    }


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

const form = ref({
    file: null,
})

const itemUpload = async (e) => {

    form.file = e.target.files[0];

    router.post(route('ads-image.upload'), form, {
        forceFormData: true,
        replace: true,
        onSuccess: (page) => {
            item.value.url = page.props.flash?.upload?.url;
        }
    });
}

</script>

<template>
    <div @click="el_ctl.active(item)" :id="item.id" class="absolute select-none"
        :style="[item.style, `width: ${item.width}px; height: ${item.height}px;`]" :class="[
            { 'ring ring-indigo-100 ring-offset-1': el_ctl.isActive(item) },
            item.class, 'z-30'
        ]">
        <!-- configuration -->
        <Teleport defer to="#configContainer">
            <div v-if="el_ctl.isActive(item)" class="flex flex-col text-sm gap-y-4">
                <!-- size -->
                <div class="w-full flex flex-col">
                    <label for="">宽 - 高 (px)</label>
                    <div class="flex flex-row w-fit gap-x-2 align-middle items-center">
                        <input id="item-width" v-model="item.width" class="w-20 "
                            :placeholder="item.width ?? `宽`"></input>
                        <span> - </span>
                        <input id="item-height" v-model="item.height" class="w-20 "
                            :placeholder="item.height ?? `高`"></input>
                    </div>
                </div>

                <!-- item -->
                <div class="w-full flex flex-col">
                    <label for="item-url">URL</label>
                    <div class="flex flex-row w-fit gap-x-2 align-middle items-center">
                        <input id="item-url" v-model="item.url" class="w-full text-left truncate"
                            placeholder="请贴入背景URL" />
                        <!-- custom file upload -->
                        <form class="flex items-center justify-center bg-white h-9 aspect-square">
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
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-300" id="file_input_help">JPG</p>
                </div>

                <!-- class -->
                <div class="w-full flex flex-col">
                    <label for="item-class">Class</label>
                    <textarea id="item-class" v-model="item.class"></textarea>
                </div>
                <!-- style -->
                <div class="w-full flex flex-col">
                    <label for="item-style">Style</label>
                    <textarea id="item-style" v-model="item.style"></textarea>
                </div>
            </div>
        </Teleport>

        <div class="w-full h-full relative bg-contain bg-no-repeat" :style="`background-image:url(${item.url})`">
            <div v-if="el_ctl.isActive(item)" @click.stop.prevent="el_ctl.del(item.id)"
                class="absolute top-0 right-0 translate-x-1/2 -translate-y-1/2 bg-black/80 ring ring-white rounded-full aspect-square w-9 grid items-center justify-center">
                <span class='text-base'>❌</span>
            </div>
            <div v-resize v-if="el_ctl.isActive(item)"
                class="absolute bottom-0 right-0 translate-x-1/2 translate-y-1/2 bg-white ring ring-white rounded-full aspect-square w-[2vmin] grid items-center justify-center shadow">
            </div>
            <div v-draggable alt="图片" class="w-full h-full" />
        </div>
    </div>
</template>