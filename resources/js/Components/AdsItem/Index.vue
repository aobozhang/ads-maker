<script setup>
import { computed, ref, inject } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import Pagination from '../Pagination.vue';
import _ from 'lodash-es';

const props = defineProps({
    adsItems: {
        type: Object,
        default: {},
    },
});

const data = computed(() => _.get(props.adsItems, 'data'));
const pagi = computed(() => props.adsItems);

const { el_ctl } = inject('GLOBAL_CREATE_AND_EDIT');


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

</script>

<template>
    <div class="w-64 flex flex-col h-full">

        <div class="grow w-full overflow-y-auto no-scrollbar">
            <div class="h-fit flex flex-row flex-wrap gap-2 py-4 px-2">
                <transition-group name='list'>
                    <div v-for="(item, index) in data" :key="item.id" @mouseleave="confirmReset"
                        class="relative w-28 h-28 bg-contain bg-no-repeat bg-center border border-dashed border-gray-200 rounded group"
                        :style="`background-image:url(${item.url})`">
                        <!-- 操作icon组 -->
                        <div
                            class="absolute right-0 bottom-0 flex flex-row gap-x-px translate-y-full group-hover:visible invisible text-center group-hover:z-50 group-hover:delay-150">
                            <a @click.prevent="el_ctl.addPicture(item)" title="添加为图片"
                                class="cursor-pointer text-xs text-white bg-black/80 ring-1 ring-white rounded-sm">
                                <!-- image-icon -->
                                <svg class="w-full aspect-square" viewBox="0 0 48 48" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M8 32C6.93913 32 5.92172 31.5786 5.17157 30.8284C4.42143 30.0783 4 29.0609 4 28V8C4 6.93913 4.42143 5.92172 5.17157 5.17157C5.92172 4.42143 6.93913 4 8 4H28C29.0609 4 30.0783 4.42143 30.8284 5.17157C31.5786 5.92172 32 6.93913 32 8"
                                        stroke="currentColor" stroke-width="4" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path
                                        d="M40 16H20C17.7909 16 16 17.7909 16 20V40C16 42.2091 17.7909 44 20 44H40C42.2091 44 44 42.2091 44 40V20C44 17.7909 42.2091 16 40 16Z"
                                        stroke="currentColor" stroke-width="4" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path
                                        d="M28 32C30.2091 32 32 30.2091 32 28C32 25.7909 30.2091 24 28 24C25.7909 24 24 25.7909 24 28C24 30.2091 25.7909 32 28 32Z"
                                        stroke="currentColor" stroke-width="4" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path
                                        d="M26.7998 44L36.1998 36.2C37.7998 34.6 40.1998 34.6 41.7998 36.2L43.9998 38.4"
                                        stroke="currentColor" stroke-width="4" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>


                            </a>

                            <a @click.prevent="el_ctl.addMain(item)" title="添加为背景"
                                class="cursor-pointer text-xs text-white bg-black/80 ring-1 ring-white rounded-sm">
                                <!-- bg-icon -->
                                <svg class="w-full aspect-square" viewBox="0 0 48 48" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M20.6 6H10C8.93913 6 7.92172 6.42143 7.17157 7.17157C6.42143 7.92172 6 8.93913 6 10V38C6 39.0609 6.42143 40.0783 7.17157 40.8284C7.92172 41.5786 8.93913 42 10 42H38C39.0609 42 40.0783 41.5786 40.8284 40.8284C41.5786 40.0783 42 39.0609 42 38V20.4"
                                        stroke="currentColor" stroke-width="4" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path d="M14 24L31 7C35 3 40 3 44 7L33 18H20" stroke="currentColor" stroke-width="4"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </a>
                            <a target="_blank" :href="route('ads-item.down', item.id)" title="下载素材"
                                @click="newTab($event, route('ads-item.down', item.id))"
                                class="text-xs text-white bg-black/80 ring-1 ring-white rounded-sm">
                                <!-- download-icon -->
                                <svg class="w-full aspect-square" viewBox="0 0 48 48" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M24 32L14 22L16.8 19.1L22 24.3V8H26V24.3L31.2 19.1L34 22L24 32ZM12 40C10.9 40 9.95867 39.6087 9.176 38.826C8.39333 38.0433 8.00133 37.1013 8 36V30H12V36H36V30H40V36C40 37.1 39.6087 38.042 38.826 38.826C38.0433 39.61 37.1013 40.0013 36 40H12Z"
                                        fill="currentColor" />
                                </svg>
                            </a>

                            <button v-if="!confirmed(item.id)" @click.prevent="confirm(item.id)" title="删除素材"
                                class="text-xs text-white bg-red-500/60 ring-1 ring-white rounded-sm">
                                <!-- delete-icon -->
                                <svg class="w-full aspect-square" viewBox="0 0 48 48" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M14 42C12.9 42 11.9587 41.6087 11.176 40.826C10.3933 40.0433 10.0013 39.1013 10 38V12H8V8H18V6H30V8H40V12H38V38C38 39.1 37.6087 40.042 36.826 40.826C36.0433 41.61 35.1013 42.0013 34 42H14ZM34 12H14V38H34V12ZM18 34H22V16H18V34ZM26 34H30V16H26V34Z"
                                        fill="currentColor" />
                                </svg>

                            </button>
                            <Link v-else method="delete" :href="route('ads-item.destroy', item.id)" as="button"
                                title="确认删除" type="button"
                                class="text-xs text-white bg-red-500/90 ring-1 ring-white rounded-sm">
                            <!-- delete-icon -->
                            <svg class="w-full aspect-square" viewBox="0 0 48 48" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M14 42C12.9 42 11.9587 41.6087 11.176 40.826C10.3933 40.0433 10.0013 39.1013 10 38V12H8V8H18V6H30V8H40V12H38V38C38 39.1 37.6087 40.042 36.826 40.826C36.0433 41.61 35.1013 42.0013 34 42H14ZM34 12H14V38H34V12ZM18 34H22V16H18V34ZM26 34H30V16H26V34Z"
                                    fill="currentColor" />
                            </svg>
                            </Link>
                        </div>
                        <div class="absolute w-[20%] right-px top-px flex flex-row gap-x-px">
                            <Link v-if="item.status & (1 << 0)" method="put" :href="route('ads-item.fav', item.id)"
                                as="button" type="button" title="移除收藏"
                                class=" font-mono text-xs text-white bg-transparent ring-1 ring-white rounded-sm">
                            <svg class="w-full aspect-square" viewBox="0 0 48 48" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M34 9C29.8 9 26.1 11.1 24 14.4C21.9 11.1 18.2 9 14 9C7.4 9 2 14.4 2 21C2 32.9 24 45 24 45C24 45 46 33 46 21C46 14.4 40.6 9 34 9Z"
                                    fill="#F44336" />
                            </svg>
                            </Link>
                            <Link v-else method="put" :href="route('ads-item.fav', item.id)" as="button" type="button"
                                title="添加收藏"
                                class=" font-mono text-xs text-white bg-transparent ring-1 ring-white rounded-sm">

                            <svg class="w-full aspect-square stroke-gray-400" viewBox="0 0 48 48" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M15 8C8.925 8 4 12.925 4 19C4 30 17 40 24 42.326C31 40 44 30 44 19C44 12.925 39.075 8 33 8C29.28 8 25.99 9.847 24 12.674C22.9855 11.2294 21.6379 10.0504 20.0714 9.23683C18.5048 8.42325 16.7653 7.999 15 8Z"
                                    stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>

                            </Link>
                        </div>
                    </div>
                </transition-group>
            </div>
        </div>
        <div v-if="pagi.last_page > 1" class="w-full border-t-2 border-gray-300 border-dashed pt-4 grid items-center">
            <Pagination :pagi="pagi" class="mx-auto" />
        </div>
    </div>
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
</style>