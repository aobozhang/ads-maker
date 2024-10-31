<script setup>
import { ref, computed, inject } from 'vue';
import _ from 'lodash-es';
import { watch } from 'vue';
import { Link, router, useForm, usePage } from '@inertiajs/vue3';
import { toBlob, toJpeg } from 'html-to-image';
import { dataURItoBlob } from './func.js'

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

    router.post(route('ads-item.store'), form, {
        forceFormData: true,
        replace: true,
        onSuccess: (page) => {
            item.value.url = page.props.flash?.upload?.url;
        }
    });
}

const bgImage = new Image();

const onUrlChange = () => {
    bgImage.src = item.value.url;
    bgImage.onload = saveJpeg;
}


const saveJpeg = async () => {

    const node = bgImage;

    toJpeg(node, {
        // backgroundColor: '#ffffff',
        width: bgImage.width,
        height: bgImage.height,
        quality: 0.8,
    }).then(function (dataUrl) {

        const blob = dataURItoBlob(dataUrl);
        form.file = blob;
        form.url = item.value.url;

        router.post(route('ads-item.store'), form, {
            forceFormData: true,
            replace: true,
            onSuccess: (page) => {
                item.value.url = page.props.flash?.upload?.url;
            }
        });
    });
}

</script>

<template>
    <div :class="item.class" class="absolute w-full h-full pointer-events-none"
        :style="`background-image:url(${item.url})`">

        <!-- configuration -->
        <Teleport defer to="#configContainer">
            <div v-if="isActived" class="flex flex-col text-sm gap-y-4 py-4">
                <!-- main -->
                <div class="w-full flex flex-col">
                    <label for="item-url">URL</label>
                    <div class="flex flex-row w-fit gap-x-2 align-middle items-center">
                        <input id="item-url" v-model="item.url" @input="onUrlChange" class="w-full text-left truncate"
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
            </div>
        </Teleport>
    </div>
</template>