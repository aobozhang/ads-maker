<script setup>
import { computed, ref, onMounted, onBeforeMount, watch, provide } from 'vue';
import GradientText from "@/Components/GradientText.vue";
import moment from 'moment'
import _ from 'lodash-es';
import { Link, router, useForm, usePage } from '@inertiajs/vue3';
import Element from "@/Components/Element.vue";
import { v4 as uuidv4 } from "uuid";
import { toBlob } from 'html-to-image';

const props = defineProps({
    item: {
        type: Object,
        default: {},
    },
});

const item = ref(props.item);

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
                if (main.value.defaultClass) {
                    el.class = main.value.defaultClass;
                    el.style = main.value.defaultStyle;
                } else {
                    el.class = `w-fit text-white text-3xl font-bold bg-black/70 px-8 py-6 rounded-lg -translate-x-1/2 -translate-y-1/2 left-1/2 top-1/2`;
                    el.style = null;
                }
                break;
            case 'image':
                el.src = "https://placehold.co/50x50";
                el.placeholder = '图片';
                el.class = `w-[50px] h-[50px] aspect-auto -translate-x-1/2 -translate-y-1/2 left-1/2 top-1/2`;
                break;
            case 'main':
                el.id = type;
                el.src = "https://placehold.co/720x1280";
                el.placeholder = '主背景';
                el.class = 'absolute left-0 top-0 bg-cover -index-50';
                el.defaultClass = `text-white text-3xl font-bold bg-black/70 px-8 py-6 rounded-lg -translate-x-1/2 -translate-y-1/2 left-1/2 top-1/2`;
                el.defaultStyle = null;
                el.auxiliary = true;
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

        if (_.get(el_model.value, `${id}.type`) === 'text' && main.value.defaultClass === 'null') {
            main.value.defaultClass = _.get(el_model.value, `${id}.class`);
        }

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

const main = ref(_.get(el_model.value, 'main', null));

watch(
    main,
    (newVal, oldVal) => {
        el_controller.update('main', main.value);
    },
    { deep: true }
)

provide("GLOBAL_CREATE_AND_EDIT", { el_model, el_controller });

const saveJpeg = async (query, forceCreate = false) => {

    const node = document.querySelector(query);

    toBlob(node, {
        backgroundColor: '#ffffff',
        width: main.value.width,
        height: main.value.height,
        quality: 0.8,
    }).then(function (blob) {

        var data = new FormData();
        let _page;

        data.append('file', blob);
        for (const key in item.value) {
            data.append(key, item.value[key]);
        }

        if (forceCreate) {
            router.post(route('ads-image.store'), data, {
                forceFormData: true,
                onSuccess: (page) => {
                    item.value = page.props.item;

                }
            });
        } else if (item.value.id) {
            router.put(route('ads-image.update', item.value.id), data, {
                forceFormData: true,
                onSuccess: (page) => {
                    item.value = page.props.item;
                }
            });
        } else {
            router.post(route('ads-image.store'), data, {
                forceFormData: true,
                onSuccess: (page) => {
                    item.value = page.props.item;

                }
            });
        }

    }).then(() => {
        console.log(item.value);
        const a = document.createElement('a');
        a.href = route('ads-image.down', item.value.id);
        a.target = '_blank';
        a.click();
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


const mainUpload = async (e) => {

    const data = {
        file: e.target.files[0]
    };

    router.post(route('ads-image.upload'), data, {
        forceFormData: true,
        replace: true,
        onSuccess: (page) => {
            main.value.src = page.props.flash?.upload?.url;
        }
    });
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
        <div class="w-64 shrink-0 grow-0 h-lvh px-4 border-r border-gray-300 text-gray-800 flex flex-col">
            <!-- file operation -->
            <div class="shrink-0 grow-0 w-full flex flex-row flex-wrap gap-1 py-4 border-b broder-gray-300">
                <Link :href="route('ads-image.index')"
                    class="bg-gray-800 text-white w-fit px-4 py-2 text-sm rounded-lg text-center">返回
                </Link>
                <template v-if="item.id">
                    <a @click="saveJpeg(`#SavePicture`)" as="button" type="button"
                        class="bg-blue-400 text-white w-fit px-4 py-2 text-sm rounded-lg text-center">更新
                    </a>
                    <a @click="saveJpeg(`#SavePicture`, true)" as="button" type="button"
                        class="bg-blue-400 text-white w-fit px-4 py-2 text-sm rounded-lg text-center">保存副本
                    </a>
                </template>
                <a v-else @click="saveJpeg(`#SavePicture`)" as="button" type="button"
                    class="bg-blue-400 text-white w-full py-2 rounded-lg text-center">保存
                </a>
            </div>

            <!-- config editor -->
            <div id="configContainer" class="grow w-full flex flex-col py-4">

                <div v-if="(!el_actived || main.active) && main" class="flex flex-col gap-y-4">
                    <!-- size -->
                    <div class="w-full">
                        <label for="">宽 - 高 (px)</label>
                        <div class="flex flex-row w-fit gap-x-2 align-middle items-center">
                            <input id="main-width" v-model="main.width" class="w-20" :placeholder="720"></input>
                            <span> - </span>
                            <input id="main-height" v-model="main.height" class="w-20" :placeholder="1280"></input>
                        </div>
                    </div>

                    <!-- main -->
                    <div class="w-full">
                        <label for="main-src">URL</label>
                        <div class="flex flex-row w-fit gap-x-2 align-middle items-center">
                            <input id="main-src" v-model="main.src" class="w-full text-left truncate"
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
                                    <input id="mainFile" type="file" class="h-full w-full opacity-0" name="mainFile"
                                        @input="mainUpload($event)">
                                </div>
                            </form>

                        </div>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-300" id="file_input_help">SVG, PNG, JPG</p>
                    </div>
                    <!-- class -->
                    <div>
                        <label for="default-class">default Class</label>
                        <textarea id="default-class" v-model="main.defaultClass"></textarea>
                    </div>
                    <!-- style -->
                    <div>
                        <label for="default-style">default Style</label>
                        <textarea id="default-style" v-model="main.defaultStyle"></textarea>
                    </div>
                    <!-- 辅助线 -->
                    <div>
                        <label for="default-auxiliary">辅助线：</label>
                        <input id="default-auxiliary" v-model="main.auxiliary" type="checkbox" />
                    </div>
                </div>
            </div>

            <!-- data operation -->
            <div class="shrink-0 grow-0 flex flex-col gap-y-3 border-t py-4 border-gray-300">
                <div @click="el_controller.active(key)" class="w-full truncate flex flex-row gap-x-3"
                    v-for="(layer, key, index) in el_model">
                    <span class="border-r border-gray-200 pr-2">{{ layer.type }}</span>
                    <div class="grow overflow-hidden flex"
                        :class="{ 'flex-row-reverse': layer.type === 'main' || layer.type === 'image', 'flex-row': layer.type == 'text' }">
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

        <!-- right area -->
        <div class="grow p-4 relative h-lvh bg-indigo-100">

            <div
                class="fixed top-40 z-40 text-center w-fit flex flex-col justify-center px-2 py-3 select-none items-center bg-gray-50 rounded shadow-lg">
                <span class=" border-gray-200 text-xs text-gray-600 pb-2 shadow shadow-white">添加</span>
                <div @click="el_controller.add('text')"
                    class="w-8 text-xs border-x border-t border-gray-300 aspect-square grid items-center text-center hover:bg-blue-200 hover:text-gray-800 text-gray-600">
                    <span>文</span>
                </div>
                <div @click="el_controller.add('image')"
                    class="w-8 text-xs border-x border-t last:border-b border-gray-300 aspect-square grid items-center text-center hover:bg-blue-200 hover:text-gray-800 text-gray-600">
                    <span>图</span>
                </div>
            </div>

            <div
                class="grow  py-5 w-full flex flex-row justify-center max-w-6xl min-w-[720px] h-full overflow-y-scroll no-scrollbar overflow-x-auto">

                <div class="relative" :class="main.rootClass"
                    :style="`width:${main.width}px;height:${main.height}px;${main.rootStyle}`">
                    <!-- 辅助线 -->
                    <div v-if="main.auxiliary"
                        class="absolute !aspect-square pointer-events-none w-full border-dashed border-y-2 border-gray-200 z-40 top-1/2 left-0 -translate-y-1/2 flex flex-row-reverse">
                        <div class="h-full w-[10%] border-l-2 border-gray-200 border-dashed"></div>
                    </div>
                    <!-- TARGET AREA -->
                    <div id="SavePicture" @click.self="el_controller.active(null)" class="relative overflow-hidden"
                        :class="main.rootClass"
                        :style="`width:${main.width}px;height:${main.height}px;${main.rootStyle}`">

                        <Element v-for="(item, key, index) in el_model" :item="item" @clk="handleElementClick">
                        </Element>

                    </div>
                </div>
            </div>

        </div>
    </div>
</template>