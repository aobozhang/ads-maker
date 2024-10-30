<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayoutPlus.vue';
import { computed, ref, nextTick, onBeforeMount, onBeforeUnmount, watch, provide } from 'vue';
import moment from 'moment'
import _ from 'lodash-es';
import { Link, useForm } from '@inertiajs/vue3';
import Element from "@/Components/Element.vue";
import AdsItems from "@/Components/AdsItem/Index.vue";
import { v4 as uuidv4 } from "uuid";
import { toBlob, toJpeg } from 'html-to-image';

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
        } else if (el_model.value.id) {
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

onBeforeMount(() => {
    el_ctl.init();
});

onBeforeUnmount(() => {
    clearInterval(cacheInterval);
    cacheMark = false;
    el_ctl.cache();
})

</script>
<template>
    <AuthenticatedLayout>
        <div class="flex flex-row w-full h-full no-scrollbar">
            <!-- left side bar -->
            <div class="w-64 shrink-0 grow-0 h-lvh px-4 border-r border-gray-300 text-gray-800 flex flex-col">
                <!-- file operation -->
                <div class="shrink-0 grow-0 w-full flex flex-col flex-wrap gap-y-2 py-4 border-b broder-gray-300">
                    <Link :href="route('ads-image.index')"
                        class="bg-gray-800 text-white w-full px-4 py-2 text-sm rounded text-center">查看列表全部
                    </Link>

                    <div v-if="item.id" class="flex flex-row text-xs gap-x-2">
                        <a @click="saveJpeg(`#SavePicture`)" as="button" type="button"
                            class="bg-blue-400 text-white w-fit px-4 py-2 rounded text-center">保存
                        </a>
                        <a @click="saveJpeg(`#SavePicture`, true)" as="button" type="button"
                            class="bg-blue-400 text-white w-fit px-4 py-2 rounded text-center">另存
                        </a>
                        <a :href="route('ads-image.down', item.id)" target="_blank" as="button" type="button"
                            class="bg-blue-400 text-white w-fit px-4 py-2 rounded text-center">下载
                        </a>
                    </div>
                    <a v-else @click="saveJpeg(`#SavePicture`)" as="button" type="button"
                        class="bg-blue-400 text-white w-full py-2 rounded text-center">保存
                    </a>
                </div>

                <!-- config editor -->
                <div id="configContainer" class="grow w-full flex flex-col text-sm gap-y-4 py-4">
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

                    </div>
                </div>

                <!-- data operation -->
                <div class="shrink-0 grow-0 flex flex-col gap-y-2 text-xs border-t py-4 border-gray-300">
                    <div v-for="(layer, index) in data" @click="el_ctl.active(layer)" :key="layer.id"
                        class="w-full truncate flex flex-row gap-x-3 group relative">
                        <span class="border-r border-gray-200 pr-2">{{ layer.type ?? 'Null' }}</span>
                        <div class="grow overflow-hidden flex"
                            :class="{ 'flex-row-reverse': layer.type === 'main' || layer.type === 'image', 'flex-row': layer.type == 'text' }">
                            <span class="w-fit text-right">
                                {{ layer.type == 'main'
                                    ? layer.url
                                    : layer.type == 'text'
                                        ? layer.innerHTML
                                        : layer.type == 'picture'
                                            ? layer.url
                                            : 'Null'
                                }}
                            </span>
                        </div>
                        <!-- 激活标识 -->
                        <div v-if="el_ctl.isActive(layer)"
                            class="absolute left-0 -translate-x-1/2 top-1/2 -translate-y-1/2 w-4 bg-blue-500 rounded-full aspect-square">
                        </div>
                        <!-- 删除键 -->
                        <div @click="el_ctl.delByIndex(index)"
                            class="absolute right-0 top-1/2 -translate-y-1/2 invisible group-hover:visible px-1 py-0.5 bg-gray-200 border border-gray-300 text-xs">
                            DEL
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main area -->
            <div class="grow p-4 relative h-lvh bg-indigo-100">

                <div
                    class="fixed top-40 z-40 text-center w-fit flex flex-col justify-center px-2 py-3 select-none items-center bg-gray-50 rounded shadow-lg">
                    <span class=" border-gray-200 text-xs text-gray-600 pb-2 shadow shadow-white">添加</span>
                    <div @click="el_ctl.addText"
                        class="w-8 text-xs border-x border-t border-gray-300 aspect-square grid items-center text-center hover:bg-blue-200 hover:text-gray-800 text-gray-600">
                        <span>文</span>
                    </div>
                    <div @click="el_ctl.addPicture"
                        class="w-8 text-xs border-x border-t last:border-b border-gray-300 aspect-square grid items-center text-center hover:bg-blue-200 hover:text-gray-800 text-gray-600">
                        <span>图</span>
                    </div>
                    <div @click="el_ctl.addMain"
                        class="w-8 text-xs border-x border-t last:border-b border-gray-300 aspect-square grid items-center text-center hover:bg-blue-200 hover:text-gray-800 text-gray-600">
                        <span>背</span>
                    </div>
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
                class="w-64 shrink-0 grow-0 py-4 h-lvh border-l border-gray-300 text-gray-800 overflow-y-auto no-scrollbar overflow-x-hidden">
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