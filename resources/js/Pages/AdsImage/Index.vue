<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayoutPlus.vue';
import { computed, ref, onMounted, onBeforeMount, watch } from 'vue';
import moment from 'moment'
import _ from 'lodash-es';
import { Link, useForm } from '@inertiajs/vue3';
import Pagination from "@/Components/Pagination.vue";
import { dataURItoBlob } from '@/Components/SubEl/func';

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

const list = computed(() => _.get(props.list, 'data'));
const updateDate = (item) => moment(item.updated_at, 'YYYY-MM-DD HH:mm:ss').format('YYYY-MM-DD');

const catg = computed(() => _.groupBy(list.value, updateDate));

const pagi = computed(() => props.list);

const confirmData = ref({});
const confirm = (id) => {
    _.set(confirmData.value, id, true);
}
const confirmed = (id) => {

    return _.get(confirmData.value, id, false);
}
const confirmReset = () => {
    confirmData.value = {};
}

const newTab = (e, url) => {
    e.preventDefault();
    window.open(url);
}

onMounted(() => {

})

</script>
<template>
    <AuthenticatedLayout>

        <template #toolbar>
            <div class="shrink-0 w-full flex flex-row flex-wrap gap-x-2 py-4 border-b broder-gray-300">
                <Link v-if="false" :href="route('ads-image.index')" title="返回主页列表">
                <!-- home icon -->
                <svg class="w-8 group-disabled:stroke-gray-200 stroke-gray-500" viewBox="0 0 48 48" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M6 18L24 4L42 18V40C42 41.0609 41.5786 42.0783 40.8284 42.8284C40.0783 43.5786 39.0609 44 38 44H10C8.93913 44 7.92172 43.5786 7.17157 42.8284C6.42143 42.0783 6 41.0609 6 40V18Z"
                        stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M18 44V24H30V44" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                </Link>

                <Link :href="route('ads-image.create')" title="新建">
                <svg class="w-8 group-disabled:stroke-gray-200 stroke-gray-500" viewBox="0 0 48 48" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M30 24H18M24 18V30M8 40V12C8 10.9391 8.42143 9.92172 9.17157 9.17157C9.92172 8.42143 10.9391 8 12 8H36C37.0609 8 38.0783 8.42143 38.8284 9.17157C39.5786 9.92172 40 10.9391 40 12V40M44 40H4"
                        stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                </svg>

                </Link>

            </div>
        </template>

        <div class="grow flex flex-row h-lvh justify-center w-full">
            <!-- left side bar -->
            <div class="w-64 shrink-0 grow-0 h-lvh pl-4 pt-16 border-r border-gray-300 flex flex-col gap-y-4 items-end">
                <Link :href="route('ads-image.index')" class="py-4 pl-12 pr-8 text-right text-xl w-fit rounded-l -mr-px"
                    :class="{ 'border-y border-l border-gray-300 bg-white': route().current('ads-image.index') }">
                主页
                </Link>
                <div class="py-4 pl-12 pr-8 text-right text-xl w-fit rounded-l -mr-px text-gray-500"
                    :class="{ 'border-y border-l border-gray-300 bg-white': route().current('ads-item.index') }">
                    素材
                </div>
            </div>
            <!-- main list -->
            <div class="grow p-8 h-full flex flex-col  bg-white">
                <div class="grow flex flex-col gap-y-5">
                    <div class="w-full border-b-2 border-gray-200 border-dashed pt-4 pb-8">
                        <Link :href="route('ads-image.create')" title="新建"
                            class="w-40 aspect-square rounded relative group grid items-center justify-center bg-gray-100 hover:bg-gray-50 drop-shadow">
                        <svg class="w-16 group-disabled:stroke-gray-200 stroke-gray-300" viewBox="0 0 48 48" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M30 24H18M24 18V30M8 40V12C8 10.9391 8.42143 9.92172 9.17157 9.17157C9.92172 8.42143 10.9391 8 12 8H36C37.0609 8 38.0783 8.42143 38.8284 9.17157C39.5786 9.92172 40 10.9391 40 12V40M44 40H4"
                                stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>

                        </Link>
                    </div>
                    <div v-for="(items, key, index) in catg" :key="key">
                        <div class="w-full border-b-2 border-gray-200 border-dashed pt-4 pb-8">
                            <h4 class="font-black text-2xl">{{ key }}</h4>
                        </div>
                        <div class="flex flex-row flex-wrap gap-5 py-4">
                            <transition-group name='list'>
                                <Link @mouseleave="confirmReset" :href="route('ads-image.show', item.id)"
                                    v-for="(item, index) in items" :key="item.id"
                                    class="w-40 aspect-square bg-contain bg-no-repeat bg-center border border-gray-100 rounded relative group"
                                    :style="`background-image:url(${item.url})`">
                                <div class="absolute right-1 top-1 flex flex-row">
                                    <a target="_blank" :href="route('ads-image.down', item.id)"
                                        @click="newTab($event, route('ads-image.down', item.id))"
                                        class="px-2 py-0.5 text-sm text-white bg-black/80 ring-1 ring-white rounded-sm group-hover:visible invisible">
                                        下载
                                    </a>
                                    <button v-if="!confirmed(item.id)" @click.prevent="confirm(item.id)"
                                        class="px-2 py-0.5 text-sm text-white bg-red-500/60 ring-1 ring-white rounded-sm group-hover:visible invisible">删除</button>
                                    <Link v-else method="delete" :href="route('ads-image.destroy', item.id)"
                                        :only="['list']" as="button" type="button"
                                        class="px-2 py-0.5 text-sm text-white bg-red-500/90 ring-1 ring-white rounded-sm group-hover:visible invisible">
                                    确认
                                    </Link>
                                </div>
                                </Link>
                            </transition-group>
                        </div>
                    </div>

                </div>
                <div v-if="pagi.last_page > 1"
                    class="w-full border-t-2 border-gray-300 border-dashed pt-4 grid items-center">
                    <Pagination :pagi="pagi" class="mx-auto" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
<style>
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
</style>