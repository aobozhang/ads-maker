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

console.log(data.value);

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
    <div class="flex flex-col h-full">
        <div>
            <h4 class="px-4">我的素材</h4>
        </div>
        <div class="grow w-full overflow-y-auto no-scrollbar">
            <div class="w-full h-fit grid grid-cols-2 grid-flow-row gap-3 p-4 overflow-hidden no-scrollbar">
                <transition-group name='list'>
                    <div v-for="(item, index) in data" :key="item.id" @mouseleave="confirmReset"
                        @click.self="el_ctl.addPicture(item)"
                        class="relative w-full aspect-square bg-contain bg-no-repeat bg-center border border-dashed border-gray-200 rounded group"
                        :style="`background-image:url(${item.url})`">
                        <div class="absolute right-1 top-1 flex flex-col">
                            <a @click.prevent="el_ctl.addMain(item)"
                                class="cursor-pointer px-2 py-0.5 text-xs text-white bg-black/80 ring-1 ring-white rounded-sm group-hover:visible invisible">
                                主图
                            </a>
                            <a target="_blank" :href="route('ads-item.down', item.id)"
                                @click="newTab($event, route('ads-item.down', item.id))"
                                class="px-2 py-0.5 text-xs text-white bg-black/80 ring-1 ring-white rounded-sm group-hover:visible invisible">
                                下载
                            </a>
                            <button v-if="!confirmed(item.id)" @click.prevent="confirm(item.id)"
                                class="px-2 py-0.5 text-xs text-white bg-red-500/60 ring-1 ring-white rounded-sm group-hover:visible invisible">删除</button>
                            <Link v-else method="delete" :href="route('ads-item.destroy', item.id)" as="button"
                                type="button"
                                class="px-2 py-0.5 text-xs text-white bg-red-500/90 ring-1 ring-white rounded-sm group-hover:visible invisible">
                            确认
                            </Link>
                        </div>
                    </div>
                </transition-group>
            </div>
        </div>
        <div class="w-full border-t-2 border-gray-300 border-dashed pt-4 grid items-center">
            <Pagination :pagi="pagi" class="mx-auto" />
        </div>
    </div>
</template>