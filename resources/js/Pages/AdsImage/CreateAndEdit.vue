<script setup>
import { computed, ref, onMounted, onBeforeMount, watch } from 'vue';
import GradientText from "@/Components/GradientText.vue";
import moment from 'moment'
import _ from 'lodash-es';
import { Link, useForm } from '@inertiajs/vue3';
import Element from "@/Components/Element.vue";

const props = defineProps({
    item: {
        type: Object,
        default: {
            type: 'div',
            prop: {
                style: {
                    position: 'relative',
                    width: '360px',
                    height: '640px',
                    'background-size': 'cover',
                    'background-image': `url(https://cdn.midjourney.com/fb32dda8-4780-48f1-b6cb-85935d285526/0_0.png)`,
                },
            },
            elements: [
                {
                    text: '测试',
                    prop: {
                        style: {
                            left: '50%',
                            top: '50%',
                            color: '#fff',
                            fontSize: '20px',
                        },
                        innerHTML: 'test',
                    },
                }
            ],
        },
    },
});

const item = ref(props.item);


const form = useForm({
    config: null,
    file: null,
})

const saveJpeg = () => {

    inSaving.value = true;

    const node = document.querySelector('#save-image');

    toBlob(node, {
        backgroundColor: '#ffffff',
        width: widthCmpt.value,
        height: heightCmpt.value,
        quality: 0.8,
    }).then(function (blob) {
        form.file = blob;
        form.config = config.value;
        form.post('/ads-image');
    });
}

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

const handleElementClick = (e) => {
    console.log(e);
}

onMounted(() => {

})

</script>
<template>
    <div class="flex flex-row w-lvw">
        <!-- left side bar -->
        <div class="w-64 shrink-0 grow-0 h-lvh px-4 border-r border-gray-300 flex flex-col">
            <!-- file operation -->
            <div class="shrink-0 grow-0 w-full flex flex-col py-4 border-b broder-gray-300">
                <template v-if="item.id">
                    <Link :href="route('ads-image.update')" method="put" :data="item" as="button" type="button"
                        class="bg-blue-400 text-white w-full py-2 rounded-lg text-center">更新
                    </Link>
                    <Link :href="route('ads-image.store')" method="post" :data="item" as="button" type="button"
                        class="bg-blue-400 text-white w-full py-2 rounded-lg text-center">保存副本
                    </Link>
                </template>
                <Link v-else :href="route('ads-image.store')" method="post" :data="item" as="button" type="button"
                    class="bg-blue-400 text-white w-full py-2 rounded-lg text-center">保存
                </Link>
            </div>
            <!-- config editor -->
            <div id="configContainer" class="grow w-full flex flex-col py-4">

            </div>
            <!-- data operation -->
            <div class="shrink-0 grow-0 flex flex-col gap-y-3 border-t py-4 border-gray-300">
                <div class="bg-blue-400 text-white w-full py-2 rounded-lg text-center">导入</div>
                <div class="bg-blue-400 text-white w-full py-2 rounded-lg text-center">导出</div>
            </div>
        </div>
        <!-- main list -->
        <div class="grow p-4">
            <div class="bg-red-100 w-full h-full relative">
                <Element :item="item" @clk="handleElementClick"></Element>
            </div>
        </div>
    </div>
</template>