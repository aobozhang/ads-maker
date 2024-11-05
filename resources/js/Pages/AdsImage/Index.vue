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
    favlist: {
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

const showFav = ref(false);
const list = computed(() => _.get(showFav.value ? props.favlist : props.list, 'data'));

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
                <div @click="showFav = false" :href="route('ads-image.index')"
                    class="py-4 pl-12 pr-8 text-right text-xl w-fit rounded-l -mr-px"
                    :class="{ 'border-y border-l border-gray-300 bg-white': !showFav }">
                    主页
                </div>
                <div @click="showFav = true"
                    class="py-4 pl-12 pr-8 text-right text-xl w-fit rounded-l -mr-px text-gray-500"
                    :class="{ 'border-y border-l border-gray-300 bg-white': showFav }">
                    收藏
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
                            <h4 class="font-black text-2xl"><span v-if="showFav">收藏日期：</span>{{ key }}</h4>
                        </div>
                        <div class="flex flex-row flex-wrap gap-5 py-4">
                            <transition-group name='list'>
                                <Link @mouseleave="confirmReset" :href="route('ads-image.show', item.id)"
                                    v-for="(item, index) in items" :key="item.id" title="编辑"
                                    class="w-40 aspect-square bg-contain bg-no-repeat bg-center border border-gray-100 rounded relative group"
                                    :style="`background-image:url(${item.url})`">
                                <!-- 默认fav status -->
                                <div class="absolute top-1 right-1 w-10">
                                    <Link v-if="item.status & (1 << 0)" method="put" preserve-state
                                        :href="route('ads-image.fav', item.id)" as="button" type="button" title="移除收藏"
                                        class=" font-mono text-xs text-white bg-transparent">
                                    <svg class="w-full aspect-square" viewBox="0 0 48 48" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M34 9C29.8 9 26.1 11.1 24 14.4C21.9 11.1 18.2 9 14 9C7.4 9 2 14.4 2 21C2 32.9 24 45 24 45C24 45 46 33 46 21C46 14.4 40.6 9 34 9Z"
                                            fill="#F44336" />
                                    </svg>
                                    </Link>
                                    <Link v-else method="put" preserve-state :href="route('ads-image.fav', item.id)"
                                        as="button" type="button" title="添加收藏"
                                        class=" font-mono text-xs text-white bg-transparent">

                                    <svg class="w-full aspect-square stroke-gray-400" viewBox="0 0 48 48" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M15 8C8.925 8 4 12.925 4 19C4 30 17 40 24 42.326C31 40 44 30 44 19C44 12.925 39.075 8 33 8C29.28 8 25.99 9.847 24 12.674C22.9855 11.2294 21.6379 10.0504 20.0714 9.23683C18.5048 8.42325 16.7653 7.999 15 8Z"
                                            stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    </Link>
                                </div>
                                <transition name="list">
                                    <div
                                        class="absolute right-1 top-1 w-72 bg-contain bg-no-repeat bg-center flex flex-row group-hover:visible group-hover:delay-300 invisible shadow-lg bg-white">
                                        <div class="relative w-full">
                                            <img :src="item.url" alt="" class="object-cover">
                                            <!-- 操作icon组 -->
                                            <div class="absolute top-0 right-0 w-32 flex flex-row gap-x-0.5">

                                                <a target="_blank" :href="route('ads-image.down', item.id)" title="下载素材"
                                                    @click="newTab($event, route('ads-image.down', item.id))"
                                                    class="text-xs text-white bg-black/80">
                                                    <!-- download-icon -->
                                                    <svg class="w-full aspect-square" viewBox="0 0 48 48" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M24 32L14 22L16.8 19.1L22 24.3V8H26V24.3L31.2 19.1L34 22L24 32ZM12 40C10.9 40 9.95867 39.6087 9.176 38.826C8.39333 38.0433 8.00133 37.1013 8 36V30H12V36H36V30H40V36C40 37.1 39.6087 38.042 38.826 38.826C38.0433 39.61 37.1013 40.0013 36 40H12Z"
                                                            fill="currentColor" />
                                                    </svg>
                                                </a>

                                                <button v-if="!confirmed(item.id)" @click.prevent="confirm(item.id)"
                                                    title="删除素材" class="text-xs text-white bg-red-500/60">
                                                    <!-- delete-icon -->
                                                    <svg class="w-full aspect-square" viewBox="0 0 48 48" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M14 42C12.9 42 11.9587 41.6087 11.176 40.826C10.3933 40.0433 10.0013 39.1013 10 38V12H8V8H18V6H30V8H40V12H38V38C38 39.1 37.6087 40.042 36.826 40.826C36.0433 41.61 35.1013 42.0013 34 42H14ZM34 12H14V38H34V12ZM18 34H22V16H18V34ZM26 34H30V16H26V34Z"
                                                            fill="currentColor" />
                                                    </svg>

                                                </button>
                                                <Link v-else method="delete" preserve-state
                                                    :href="route('ads-image.destroy', item.id)" as="button" title="确认删除"
                                                    type="button" class="text-xs text-white bg-red-500/90">
                                                <!-- delete-icon -->
                                                <svg class="w-full aspect-square" viewBox="0 0 48 48" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M14 42C12.9 42 11.9587 41.6087 11.176 40.826C10.3933 40.0433 10.0013 39.1013 10 38V12H8V8H18V6H30V8H40V12H38V38C38 39.1 37.6087 40.042 36.826 40.826C36.0433 41.61 35.1013 42.0013 34 42H14ZM34 12H14V38H34V12ZM18 34H22V16H18V34ZM26 34H30V16H26V34Z"
                                                        fill="currentColor" />
                                                </svg>
                                                </Link>

                                                <Link v-if="item.status & (1 << 0)" method="put" preserve-state
                                                    :href="route('ads-image.fav', item.id)" as="button" type="button"
                                                    title="移除收藏" class=" font-mono text-xs text-white bg-transparent">
                                                <svg class="w-full aspect-square" viewBox="0 0 48 48" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M34 9C29.8 9 26.1 11.1 24 14.4C21.9 11.1 18.2 9 14 9C7.4 9 2 14.4 2 21C2 32.9 24 45 24 45C24 45 46 33 46 21C46 14.4 40.6 9 34 9Z"
                                                        fill="#F44336" />
                                                </svg>
                                                </Link>
                                                <Link v-else method="put" preserve-state
                                                    :href="route('ads-image.fav', item.id)" as="button" type="button"
                                                    title="添加收藏" class=" font-mono text-xs text-white bg-transparent">

                                                <svg class="w-full aspect-square stroke-gray-400" viewBox="0 0 48 48"
                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M15 8C8.925 8 4 12.925 4 19C4 30 17 40 24 42.326C31 40 44 30 44 19C44 12.925 39.075 8 33 8C29.28 8 25.99 9.847 24 12.674C22.9855 11.2294 21.6379 10.0504 20.0714 9.23683C18.5048 8.42325 16.7653 7.999 15 8Z"
                                                        stroke-width="4" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                </svg>
                                                </Link>
                                            </div>

                                        </div>
                                    </div>
                                </transition>
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