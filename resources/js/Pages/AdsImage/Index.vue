<script setup>
import { computed, ref, onMounted, onBeforeMount, watch } from 'vue';
import { toBlob, toJpeg } from 'html-to-image';
import GradientText from "@/Components/GradientText.vue";
import moment from 'moment'
import _ from 'lodash-es';
import { Link, useForm } from '@inertiajs/vue3';
import Element from "@/Components/Element.vue";
import Pagination from "@/Components/Pagination.vue";

const props = defineProps({
    list: {
        type: Object,
        default: {},
    },
    item: {
        type: Object,
        default: {
            main: {
                width: '720px',
                height: '1280px',
                left: 0,
                top: 0,
                image: null,
            },
            elements: [

            ],
        },
    },
});

console.log(props.list.data);

const list = ref(_.get(props.list, 'data'));
const pagi = computed(() => props.list);

function exportAsJSON(data, filename) {
    const jsonData = JSON.stringify(data, null, 2);
    const blob = new Blob([jsonData], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    a.click();
    URL.revokeObjectURL(url);
}

const exportJson = () => {

    const myData = localStorage.getItem(`adsImageMaker-data`);
    let date = moment();
    let dateStr = date.format("YYYYMMDD_HHmmss");
    const filename = `adsImageMaker-${dateStr}.json`;

    exportAsJSON(myData, filename);
}

const importJson = () => {
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = '.json';

    input.addEventListener('change', function (event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                try {
                    const jsonData = JSON.parse(e.target.result);
                    localStorage.setItem(`adsImageMaker-data`, jsonData);
                    alert('导入成功');
                } catch (error) {
                    alert('文件不是有效的 JSON 格式。');
                }
            };
            reader.readAsText(file);
        }
    });

    input.click();
}


const newTab = (e, url) => {
    e.preventDefault();
    window.open(url);
}

onMounted(() => {

})

</script>
<template>
    <div class="flex flex-row w-lvw">
        <!-- left side bar -->
        <div class="w-64 shrink-0 grow-0 h-lvh px-4 border-r border-gray-300 flex flex-col">
            <div class="grow w-full flex flex-col py-4">
                <Link :href="route('ads-image.create')"
                    class="bg-blue-400 text-white w-full py-2 rounded-lg text-center">创建
                </Link>
            </div>
            <div class="shrink-0 grow-0 flex flex-col gap-y-3 border-t py-4 border-gray-400">
                <div class="bg-blue-400 text-white w-full py-2 rounded-lg text-center">导入</div>
                <div class="bg-blue-400 text-white w-full py-2 rounded-lg text-center">导出</div>
            </div>
        </div>
        <!-- main list -->
        <div class="grow p-4 flex flex-col max-w-5xl">
            <div class="grow">
                <div class="flex flex-row flex-wrap gap-5">
                    <Link :href="route('ads-image.show', item.id)" v-for="(item, index) in list"
                        class="w-32 aspect-square bg-contain bg-no-repeat bg-center border border-gray-100 rounded relative group"
                        :style="`background-image:url(${item.url})`">
                    <a target="_blank" :href="route('ads-image.down', item.id)"
                        @click="newTab($event, route('ads-image.down', item.id))"
                        class="absolute right-1 top-1 px-2 py-0.5 text-sm text-white bg-black/80 ring-1 ring-white rounded-sm group-hover:visible invisible">
                        下载
                    </a>
                    </Link>
                </div>
            </div>
            <div class="w-full border-t-2 border-gray-300 border-dashed pt-4 grid items-center">
                <Pagination :pagi="pagi" class="mx-auto" />
            </div>
        </div>
    </div>
</template>