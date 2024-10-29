<script setup>
import { computed, ref, onMounted, onBeforeMount, watch, provide } from 'vue';
import GradientText from "@/Components/GradientText.vue";
import moment from 'moment'
import _ from 'lodash-es';
import { Link, useForm } from '@inertiajs/vue3';
import Element from "@/Components/Element.vue";
import { v4 as uuidv4 } from "uuid";

const props = defineProps({
    item: {
        type: Object,
        default: {},
    },
});

const item = ref(props.item);


const form = useForm({
    config: null,
    file: null,
});

const el_actived = ref(null);
const el_model = ref(localStorage.getItem('templateSave') ? JSON.parse(localStorage.getItem('templateSave')) : {});
const el_controller = {
    active: (id = null) => {

        const old_actived = el_actived.value;

        // 有对象，就射对象
        if (id !== null) {
            if (id === old_actived) {
                return;
            }
            _.set(el_model.value, `${id}.active`, true);
        }

        // 有前对象，就否前对象
        if (old_actived !== null) {
            _.set(el_model.value, `${old_actived}.active`, false);
        }

        // 宣告当前对象
        el_actived.value = id;
    },
    add: (type) => {
        let el = {};
        el.id = uuidv4();
        el.type = type;
        switch (type) {
            case 'text':
                el.placeholder = '请输入内容';
                el.class = `text-white text-3xl font-bold bg-black/70 px-8 py-6 rounded-lg -translate-x-1/2 -translate-y-1/2 left-1/2 top-1/2`;
                break;
            case 'image':
                el.src = null;
                el.placeholder = '图片';
                break;
            case 'main':
                el.id = type;
                el.src = null;
                el.placeholder = '主背景';
                el.class = 'absolute left-0 top-0 bg-cover -index-50'
                el.width = 720;
                el.height = 1280;
                main.value = el;
                break;
            default:
                break;
        }
        el_model.value[el.id] = el;
        el_controller.active(el.id);
        return el.id;
    },
    update: (id, val) => {
        _.set(el_model.value, `${id}`, val);
        localStorage.setItem('templateSave', JSON.stringify(el_model.value));
    },
    del: (id) => {
        console.log(id);
        console.log(el_model.value);
        delete el_model.value[id];
        el_actived.value = null;
        localStorage.setItem('templateSave', JSON.stringify(el_model.value));
    },
};

console.log(el_model.value);

const main = ref(_.get(el_model.value, 'main', null));

watch(
    main,
    (newVal, oldVal) => {
        el_controller.update('main', main.value);
    },
    { deep: true }
)

provide("GLOBAL_CREATE_AND_EDIT", { el_model, el_controller });


const saveJpeg = async (query) => {

    const node = document.querySelector(query);

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

onBeforeMount(() => {
    if (main.value == null) {
        el_controller.add('main');
    }
})

</script>
<template>
    <div class="flex flex-row w-full h-full no-scrollbar">
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
                <div v-if="(!el_actived || main.active) && main" class="flex flex-col gap-y-4">
                    <div>
                        <label for="">宽 - 高</label>
                        <div class="flex flex-row w-fit gap-x-2 align-middle items-center">
                            <input id="main-width" v-model="main.width" class="w-20" :placeholder="720"></input>
                            <span> - </span>
                            <input id="main-height" v-model="main.height" class="w-20" :placeholder="1280"></input>
                        </div>
                    </div>
                    <div>
                        <label for="main-src">URL</label>
                        <div class="flex flex-row w-fit gap-x-2 align-middle items-center">
                            <input id="main-src" v-model="main.src" class="w-full text-left truncate"
                                placeholder="请贴入背景URL"></input>
                        </div>
                    </div>
                </div>
            </div>
            <!-- data operation -->
            <div class="shrink-0 grow-0 flex flex-col gap-y-3 border-t py-4 border-gray-300">
                <div @click="el_controller.active(key)" class="w-full truncate flex flex-row gap-x-3"
                    v-for="(layer, key, index) in el_model">
                    <span class="">{{ layer.type }}</span>
                    <div class="grow overflow-hidden flex flex-row-reverse">
                        <span class="w-fit text-right">
                            {{ layer.type == 'main'
                                ? layer.src
                                : layer.type == 'text'
                                    ? layer.innerHTML
                                    : layer.type == 'image'
                                        ? layer.src
                                        : ''
                            }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <!-- main list -->
        <div class="grow p-4 flex flex-col">

            <div class="w-full flex flex-row py-2 select-none items-center">
                <span class="pr-4">添加</span>
                <div @click="el_controller.add('text')"
                    class="w-10 border border-gray-300 aspect-square grid items-center text-center">
                    <span>文</span>
                </div>
                <div @click="el_controller.add('image')"
                    class="w-10 border border-gray-300 aspect-square grid items-center text-center">
                    <span>图</span>
                </div>
            </div>

            <div class="grow bg-red-100 w-full overflow-hidden">

                <div @click.self="el_controller.active(null)" class="relative"
                    :style="`width:${main.width}px;height:${main.height}px;`">

                    <Element v-for="(item, key, index) in el_model" :item="item" @clk="handleElementClick"></Element>

                </div>
            </div>

        </div>
    </div>
</template>