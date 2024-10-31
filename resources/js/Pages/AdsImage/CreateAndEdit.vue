<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayoutPlus.vue';
import { computed, ref, nextTick, onBeforeMount, onBeforeUnmount, watch, provide } from 'vue';
import moment from 'moment'
import _ from 'lodash-es';
import { Link, router, useForm } from '@inertiajs/vue3';
import Element from "@/Components/Element.vue";
import AdsItems from "@/Components/AdsItem/Index.vue";
import { v4 as uuidv4 } from "uuid";
import { toBlob, toJpeg } from 'html-to-image';
import draggable from 'vuedraggable'

const props = defineProps({
    adsItems: {
        type: Object,
        default: {},
    },
    item: {
        type: Object,
        default: {},
    },
});

const adsItems = computed(() => props.adsItems);

const el_model = ref(props.item?.config ?? {
    base: {
        default: {
            text: {
                class: `text-white text-3xl font-bold bg-black/70 px-8 py-6 rounded-lg -translate-x-1/2 -translate-y-1/2 left-1/2 top-1/2`,
                style: ``,
            },
            picture: {
                class: ``,
                style: ``,
            }
        },
        auxiliary: true,
        width: 720,
        height: 1280,
    },
    data: []
});

const base = ref();
const data = ref();
watch(
    base,
    (n, o) => {
        _.set(el_model.value, 'base', n);
    },
    { deep: true },
)

watch(
    data,
    (n, o) => {
        _.set(el_model.value, 'data', n);
    },
    { deep: true },
)

const el_actived = ref();

let cacheMark = false;
let cacheInterval = null;

const el_ctl = {
    init: () => {
        cacheInterval = setInterval(() => {
            cacheMark = false;
        }, 60 * 1000);

        let cacheTime = localStorage.getItem(`templateUpdated_${props.item?.id ?? 'tmp'}`);
        let cacheM = moment(cacheTime, 'YYYY-MM-DD HH:mm:ss');
        let modelM = moment(_.get(props.item, 'updated_at', '1970-1-1 00:00:00'), 'YYYY-MM-DD HH:mm:ss');

        if (props.item == {} || cacheM.isAfter(modelM)) {
            console.log('read from cache');

            el_model.value = JSON.parse(localStorage.getItem(`templateSave_${props.item?.id ?? 'tmp'}`)) ?? {};
        }

        base.value = el_model.value.base;
        data.value = el_model.value.data;
    },
    isActive: (item) => {
        return item.id === el_actived.value?.id;
    },
    active: async (item) => {
        await nextTick();
        el_actived.value = item;
    },
    addText: () => {
        let el = {
            id: uuidv4(),
            type: "text",
            placeholder: '请输入内容',
            class: `w-max text-white text-6xl tracking-wider font-bold bg-black/70 px-8 py-6 rounded-lg -translate-x-1/2 -translate-y-1/2 left-1/2 top-1/2`,
            style: null,
        };

        el_model.value.data.push(el);
        el_ctl.active(el);
        el_ctl.cache(true);

    },
    addPicture: (config = null) => {

        let el = {
            id: uuidv4(),
            type: "picture",
            placeholder: '图片',
            width: 200,
            height: 100,
            style: `width:200px;height:100px;left:50%;top:50%;`,
            url: 'https://placehold.co/200x100',
            class: `bg-contain`,
        };

        if (_.has(config, 'url')) {
            el.url = config.url;
        }


        data.value.push(el);
        el_ctl.active(el);
        el_ctl.cache(true);
    },

    addMain: async (config = null) => {

        el_ctl.del('main');
        await nextTick();

        let el = {
            id: "main",
            type: "main",
            placeholder: '图片',
            class: `bg-cover left-0 top-0 w-full h-full z-0`,
            style: ``,
            url: 'https://placehold.co/720x1280',
        };

        if (_.has(config, 'url')) {
            el.url = config.url;
        }

        data.value.unshift(el);
        el_ctl.active(el);
        el_ctl.cache(true);
    },

    updateBase: (key, value) => {
        _.set(base.value, key, value);
    },

    updateData: (id, val, key = null) => {
        let index = _.findIndex(data.value, { id: id });
        // 新建属性push进去
        if (index === -1) {
            let t = {};
            if (key) {
                t[key] = val;
            } else {
                t = val;
            }
            data.value.push(t);
        } else if (key) {
            data.value[index][key] = val;
        } else {
            data.value[index] = val;
        }

        el_ctl.cache(true);
    },

    find: (id) => {
        const index = _.findIndex(data.value, { id: id });
        return _.nth(data.value, index, null);
    },
    delByIndex: async (index) => {
        let res = data.value.splice(index, 1);
        await el_ctl.cache(true);
    },
    del: async (id) => {
        let res = _.remove(data.value, (i) => i.id === id);
        await el_ctl.cache(true);
        return res;
    },

    clear: () => {
        el_model.value = data.value.filter(n => n);
    },
    cache: async (force = false) => {
        if (cacheMark && !force) {
            return;
        }
        await nextTick();
        localStorage.setItem(`templateUpdated_${props.item?.id ?? 'tmp'}`, moment().format('YYYY-MM-DD HH:mm:ss'));
        localStorage.setItem(`templateSave_${props.item?.id ?? 'tmp'}`, JSON.stringify(el_model.value));
        cacheMark = true;
        console.log('cached');
    },
    pureCache: async () => {
        localStorage.clear();
        data.value = [];
    }
};

provide("GLOBAL_CREATE_AND_EDIT", { el_model, el_ctl });

function dataURItoBlob(dataURI) {
    // convert base64/URLEncoded data component to raw binary data held in a string
    var byteString;
    if (dataURI.split(',')[0].indexOf('base64') >= 0)
        byteString = atob(dataURI.split(',')[1]);
    else
        byteString = unescape(dataURI.split(',')[1]);

    // separate out the mime component
    var mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0];

    // write the bytes of the string to a typed array
    var ia = new Uint8Array(byteString.length);
    for (var i = 0; i < byteString.length; i++) {
        ia[i] = byteString.charCodeAt(i);
    }

    return new Blob([ia], { type: mimeString });
}

const saveJpeg = async (query, forceCreate = false) => {

    const node = document.querySelector(query);

    toJpeg(node, {
        backgroundColor: '#ffffff',
        width: el_model.value.base.width,
        height: el_model.value.base.height,
        quality: 0.8,
    }).then(function (dataUrl) {

        const blob = dataURItoBlob(dataUrl);

        if (forceCreate) {
            var form = useForm({
                config: el_model.value,
                file: blob,
            });

            form.post(route('ads-image.store'));
        } else if (props.item.id) {
            var form = useForm({
                config: el_model.value,
                file: blob,
                '_method': 'PUT'
            });

            form.post(route('ads-image.update', props.item.id));
        } else {
            var form = useForm({
                config: el_model.value,
                file: blob,
            });

            form.post(route('ads-image.store'));
        }

    });
}

const resetPointer = (e) => {

    let baseDoms = document.getElementsByClassName('base-silence');
    for (var dom of baseDoms) {
        dom.classList.add('silence');
    }

    let doms = document.getElementsByClassName('silence');
    for (var dom of doms) {
        dom.classList.remove('pointer-events-none');
    }
}

onBeforeMount(() => {
    el_ctl.init();
    resetPointer();
});

onBeforeUnmount(() => {
    clearInterval(cacheInterval);
    cacheMark = false;
    el_ctl.cache();
    resetPointer();
})

</script>

<template>
    <AuthenticatedLayout>

        <template #toolbar>
            <div class="shrink-0 w-full flex flex-row flex-wrap gap-x-2 py-4">
                <Link :href="route('ads-image.index')" title="返回主页列表">
                <!-- home icon -->
                <svg class="w-8 group-disabled:stroke-gray-200 stroke-gray-500" viewBox="0 0 48 48" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M6 18L24 4L42 18V40C42 41.0609 41.5786 42.0783 40.8284 42.8284C40.0783 43.5786 39.0609 44 38 44H10C8.93913 44 7.92172 43.5786 7.17157 42.8284C6.42143 42.0783 6 41.0609 6 40V18Z"
                        stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M18 44V24H30V44" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                </Link>

                <a :disabled="item.id" title="保存" @click="saveJpeg(`#SavePicture`)" as="button" type="button">
                    <!-- save icon -->
                    <svg class="w-8 group-disabled:stroke-gray-200 stroke-gray-500" viewBox="0 0 48 48" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M14 6V16H30" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                        <path
                            d="M10 42C8.93913 42 7.92172 41.5786 7.17157 40.8284C6.42143 40.0783 6 39.0609 6 38V10C6 8.93913 6.42143 7.92172 7.17157 7.17157C7.92172 6.42143 8.93913 6 10 6H32L42 16V38C42 39.0609 41.5786 40.0783 40.8284 40.8284C40.0783 41.5786 39.0609 42 38 42H10Z"
                            stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M34 42V26H14V42" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </a>

                <a :disabled="!item.id" @click="saveJpeg(`#SavePicture`, true)" as="button" type="button" class="group"
                    title="另存为">
                    <!-- save as icon -->
                    <svg class="w-8 group-disabled:stroke-gray-200 stroke-gray-500" viewBox="0 0 48 48" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M12 8C12 6.93913 12.4214 5.92172 13.1716 5.17157C13.9217 4.42143 14.9391 4 16 4H36L44 12V32.4C43.9005 33.3901 43.4355 34.3076 42.6959 34.9732C41.9563 35.6389 40.9951 36.005 40 36H16C14.9391 36 13.9217 35.5786 13.1716 34.8284C12.4214 34.0783 12 33.0609 12 32V8Z"
                            stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M20 4V12H32M36 36V22H20V36" stroke-width="4" stroke-linecap="round"
                            stroke-linejoin="round" />
                        <path d="M36 44H8C6.93913 44 5.92172 43.5786 5.17157 42.8284C4.42143 42.0783 4 41.0609 4 40V12"
                            stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>

                </a>

                <a v-if="item.id" :href="route('ads-image.down', item.id)" target="_blank" as="button" type="button"
                    title="下载到本地">
                    <!-- down icon -->
                    <svg class="w-8 group-disabled:stroke-gray-200 stroke-gray-500" viewBox="0 0 48 48" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M24 32V16M24 32L32 24M24 32L16 24" stroke-width="4" stroke-linecap="round"
                            stroke-linejoin="round" />
                        <path
                            d="M8 40V12C8 10.9391 8.42143 9.92172 9.17157 9.17157C9.92172 8.42143 10.9391 8 12 8H36C37.0609 8 38.0783 8.42143 38.8284 9.17157C39.5786 9.92172 40 10.9391 40 12V40M44 40H4"
                            stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </a>

            </div>
        </template>

        <div class="flex flex-row w-full h-full no-scrollbar">
            <!-- left side bar -->
            <div class="w-64 h-lvh px-4 pt-12 pb-4 border-b border-gray-300 text-gray-800 flex flex-col">
                <!-- config editor -->
                <div id="configContainer" class="grow w-full flex flex-col text-sm gap-y-4 pt-4">
                    <div v-if="(!el_actived)" class="flex flex-col gap-y-4">
                        <!-- size -->
                        <div class="w-full flex flex-col">
                            <label for="">宽 - 高 (px)</label>
                            <div class="flex flex-row w-fit gap-x-2 align-middle items-center">
                                <input id="base-width" v-model="base.width" class="w-20" :placeholder="720"></input>
                                <span> - </span>
                                <input id="base-height" v-model="base.height" class="w-20" :placeholder="1280"></input>
                            </div>
                        </div>

                        <hr>
                        <!-- 辅助线 -->
                        <div>
                            <label for="default-auxiliary">辅助线：</label>
                            <input id="default-auxiliary" v-model="base.auxiliary" type="checkbox" />
                        </div>

                        <hr>
                        <!-- function -->
                        <div>
                            <button @click="el_ctl.pureCache"
                                class="w-full py-2 bg-blue-300 rounded text-gray-50">清空画布</button>
                        </div>
                    </div>
                </div>

                <!-- data operation -->
                <div
                    class="shrink-0 flex flex-col gap-y-0.5 text-xs border-t py-2 border-gray-300 bg-gray-700 rounded shadow-inner px-1 text-gray-200">
                    <draggable v-model="data" item-key="id" draggable=".item">
                        <!-- element -->
                        <template #item="{ element, index }">
                            <div @click.prevent="el_ctl.active(element)"
                                class="select-none w-full truncate flex flex-row gap-x-2 group relative py-0.5 px-1 rounded hover:bg-gray-500"
                                :class="{ 'bg-gray-500 ': el_ctl.isActive(element), 'item cursor-grab': index > 0, 'cursor-pointer': index == 0 }">
                                <!-- icon -->
                                <div v-if="element.type === 'text'">
                                    <svg class="w-4 stroke-gray-200" viewBox="0 0 48 48" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M40 16H20C17.7909 16 16 17.7909 16 20V40C16 42.2091 17.7909 44 20 44H40C42.2091 44 44 42.2091 44 40V20C44 17.7909 42.2091 16 40 16Z"
                                            stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                                        <path
                                            d="M8 32C5.8 32 4 30.2 4 28V8C4 5.8 5.8 4 8 4H28C30.2 4 32 5.8 32 8M24 26V24H36V26M30 24V36M28 36H32"
                                            stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </div>
                                <div v-if="element.type === 'picture'">
                                    <svg class="w-4 stroke-gray-200" viewBox="0 0 48 48" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M8 32C6.93913 32 5.92172 31.5786 5.17157 30.8284C4.42143 30.0783 4 29.0609 4 28V8C4 6.93913 4.42143 5.92172 5.17157 5.17157C5.92172 4.42143 6.93913 4 8 4H28C29.0609 4 30.0783 4.42143 30.8284 5.17157C31.5786 5.92172 32 6.93913 32 8"
                                            stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                                        <path
                                            d="M40 16H20C17.7909 16 16 17.7909 16 20V40C16 42.2091 17.7909 44 20 44H40C42.2091 44 44 42.2091 44 40V20C44 17.7909 42.2091 16 40 16Z"
                                            stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                                        <path
                                            d="M28 32C30.2091 32 32 30.2091 32 28C32 25.7909 30.2091 24 28 24C25.7909 24 24 25.7909 24 28C24 30.2091 25.7909 32 28 32Z"
                                            stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                                        <path
                                            d="M26.7998 44L36.1998 36.2C37.7998 34.6 40.1998 34.6 41.7998 36.2L43.9998 38.4"
                                            stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </div>
                                <div v-if="element.type === 'main'">
                                    <svg class="w-4 stroke-gray-200" viewBox="0 0 48 48" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M20.6 6H10C8.93913 6 7.92172 6.42143 7.17157 7.17157C6.42143 7.92172 6 8.93913 6 10V38C6 39.0609 6.42143 40.0783 7.17157 40.8284C7.92172 41.5786 8.93913 42 10 42H38C39.0609 42 40.0783 41.5786 40.8284 40.8284C41.5786 40.0783 42 39.0609 42 38V20.4"
                                            stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M14 24L31 7C35 3 40 3 44 7L33 18H20" stroke-width="4"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </div>
                                <!-- content -->
                                <div class="grow overflow-hidden flex"
                                    :class="{ 'flex-row-reverse': element.type === 'main' || element.type === 'image', 'flex-row': element.type == 'text' }">
                                    <span class="w-fit text-right">
                                        {{ element.type == 'main'
                                            ? element.url
                                            : element.type == 'text'
                                                ? element.innerHTML
                                                : element.type == 'picture'
                                                    ? element.url
                                                    : 'Null'
                                        }}
                                    </span>
                                </div>
                                <!-- delete -->
                                <div @click="el_ctl.delByIndex(index)"
                                    class="absolute right-0 top-1/2 -translate-y-1/2 invisible group-hover:visible px-2 py-1 rounded text-white bg-red-400 border border-gray-300 text-xs">
                                    DEL
                                </div>
                            </div>
                        </template>
                    </draggable>
                    <div v-if="data.length === 0" class="text-center">
                        {{ `< 无 \>` }}
                    </div>
                </div>
            </div>

            <!-- Main area -->
            <div class="grow p-4 relative h-lvh bg-indigo-100">
                <!-- 添加入口 -->
                <div
                    class="fixed top-40 z-40 text-center w-fit flex flex-col justify-center px-2 py-3 gap-y-1 select-none items-center bg-gray-50 rounded shadow-lg">
                    <span class="border-b border-gray-300 text-xs text-gray-400 pb-2 shadow shadow-white">添加</span>
                    <a title="添加文本" @click="el_ctl.addText"
                        class="w-8 pt-2 text-xs aspect-square grid items-center text-center hover:bg-blue-200 hover:text-gray-800 text-gray-600 rounded">
                        <span><svg class="w-8" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M40 16H20C17.7909 16 16 17.7909 16 20V40C16 42.2091 17.7909 44 20 44H40C42.2091 44 44 42.2091 44 40V20C44 17.7909 42.2091 16 40 16Z"
                                    stroke="#757575" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                                <path
                                    d="M8 32C5.8 32 4 30.2 4 28V8C4 5.8 5.8 4 8 4H28C30.2 4 32 5.8 32 8M24 26V24H36V26M30 24V36M28 36H32"
                                    stroke="#757575" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                    </a>
                    <a title="添加图片" @click="el_ctl.addPicture"
                        class="w-8 text-xs aspect-square grid items-center text-center hover:bg-blue-200 hover:text-gray-800 text-gray-600 rounded">
                        <span><svg class="w-8" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M8 32C6.93913 32 5.92172 31.5786 5.17157 30.8284C4.42143 30.0783 4 29.0609 4 28V8C4 6.93913 4.42143 5.92172 5.17157 5.17157C5.92172 4.42143 6.93913 4 8 4H28C29.0609 4 30.0783 4.42143 30.8284 5.17157C31.5786 5.92172 32 6.93913 32 8"
                                    stroke="#757575" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                                <path
                                    d="M40 16H20C17.7909 16 16 17.7909 16 20V40C16 42.2091 17.7909 44 20 44H40C42.2091 44 44 42.2091 44 40V20C44 17.7909 42.2091 16 40 16Z"
                                    stroke="#757575" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                                <path
                                    d="M28 32C30.2091 32 32 30.2091 32 28C32 25.7909 30.2091 24 28 24C25.7909 24 24 25.7909 24 28C24 30.2091 25.7909 32 28 32Z"
                                    stroke="#757575" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M26.7998 44L36.1998 36.2C37.7998 34.6 40.1998 34.6 41.7998 36.2L43.9998 38.4"
                                    stroke="#757575" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                    </a>
                    <a title="添加背景" @click="el_ctl.addMain"
                        class="w-8 text-xs aspect-square grid items-center text-center hover:bg-blue-200 hover:text-gray-800 text-gray-600 rounded">
                        <span><svg class="w-8" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M20.6 6H10C8.93913 6 7.92172 6.42143 7.17157 7.17157C6.42143 7.92172 6 8.93913 6 10V38C6 39.0609 6.42143 40.0783 7.17157 40.8284C7.92172 41.5786 8.93913 42 10 42H38C39.0609 42 40.0783 41.5786 40.8284 40.8284C41.5786 40.0783 42 39.0609 42 38V20.4"
                                    stroke="#757575" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M14 24L31 7C35 3 40 3 44 7L33 18H20" stroke="#757575" stroke-width="4"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                    </a>
                </div>

                <div
                    class="grow  py-5 w-full flex flex-row justify-center max-w-6xl min-w-[720px] h-full overflow-y-scroll no-scrollbar overflow-x-auto">

                    <div class="relative overflow-visible" :style="`width:${base.width}px;height:${base.height}px;`">
                        <!-- 辅助线 -->
                        <div v-if="base.auxiliary"
                            class="absolute !aspect-square pointer-events-none w-full border-dashed border-y-2 border-gray-200 z-40 top-1/2 left-0 -translate-y-1/2 flex flex-row-reverse">
                            <div class="h-full w-[10%] border-l-2 border-gray-200 border-dashed"></div>
                        </div>

                        <!-- TARGET AREA -->
                        <div id="SavePicture" @click.self="el_ctl.active(null)" class="relative overflow-clip"
                            :style="`width:${base.width}px;height:${base.height}px;`">
                            <transition-group name="ele">
                                <Element v-for="(item, index) in data" :key="item.id" :item="item"
                                    :isActived="el_ctl.isActive(item)">
                                </Element>
                            </transition-group>
                        </div>
                    </div>
                </div>

            </div>

            <!-- right side bar -->
            <div
                class="w-fit bg-white shrink-0 grow-0 py-4 h-lvh border-l border-gray-300 text-gray-800 overflow-y-auto no-scrollbar overflow-x-hidden">
                <AdsItems :adsItems="adsItems"></AdsItems>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
<style lang="less">
textarea,
input {
    @apply text-xs;
}

.list-move,
/* 对移动中的元素应用的过渡 */
.list-enter-active,
.list-leave-active {
    transition: all 0.5s ease;
}

.list-enter-from,
.list-leave-to {
    opacity: 0;
    transform: scale(0);
}

/* 确保将离开的元素从布局流中删除
  以便能够正确地计算移动的动画。 */
.list-leave-active {
    position: absolute;
}

.ele-move,
/* 对移动中的元素应用的过渡 */
.ele-enter-active,
.ele-leave-active {
    transition: all 0.3s ease;
}

.ele-enter-from,
.ele-leave-to {
    opacity: 0;
}

/* 确保将离开的元素从布局流中删除
  以便能够正确地计算移动的动画。 */
.ele-leave-active {
    position: absolute;
}
</style>
