<script setup>
const _ = window._;
import { computed, ref, onMounted, onBeforeMount, watch } from 'vue';
import { toBlob, toJpeg } from 'html-to-image';
import GradientText from "../components/GradientText.vue";
import moment from 'moment';
import { useRouter } from 'vue-router';

const props = defineProps({
    name: String,
})
const name = computed(() => props.name);

const router = useRouter();

const vDraggable = (el, binding) => {

    let rect = el.getBoundingClientRect();

    // 设置拖动元素的初始位置和尺寸
    el.draggable = "true";
    let mouseStartX = 0;
    let mouseStartY = 0;
    let elStartX = 0;
    let elStartY = 0;
    let isDown = false;

    el.ondragstart = () => false;
    el.addEventListener('mousedown', (e) => {

        isDown = true;
        elStartX = el.offsetLeft;
        elStartY = el.offsetTop;
        mouseStartX = e.pageX;
        mouseStartY = e.pageY;
    });

    document.addEventListener('mousemove', (e) => {

        if (!isDown) return;

        var x = e.pageX - mouseStartX + elStartX;
        var y = e.pageY - mouseStartY + elStartY;

        el.style.position = 'absolute';
        el.style.top = `${y}px`;
        el.style.left = `${x}px`;

        _.set(config.value, `${el.id}.left`, el.style.left);
        _.set(config.value, `${el.id}.top`, el.style.top);
    });

    document.addEventListener('mouseup', () => {
        isDown = false;
        change();
    });
};

const data = ref(
    [
        {
            "str": "ISTJ",
            "en": "Inspector",
            "cn": "检察员"
        },
        {
            "str": "ISFJ",
            "en": "Defender",
            "cn": "守护者"
        },
        {
            "str": "INFJ",
            "en": "Advocate",
            "cn": "倡导者"
        },
        {
            "str": "INTJ",
            "en": "Architect",
            "cn": "架构师"
        },
        {
            "str": "ISTP",
            "en": "Virtuoso",
            "cn": "大师"
        },
        {
            "str": "ISFP",
            "en": "Adventurer",
            "cn": "探险家"
        },
        {
            "str": "INFP",
            "en": "Mediator",
            "cn": "调解者"
        },
        {
            "str": "INTP",
            "en": "Logician",
            "cn": "逻辑学家"
        },
        {
            "str": "ESTP",
            "en": "Entrepreneur",
            "cn": "企业家"
        },
        {
            "str": "ESFP",
            "en": "Entertainer",
            "cn": "表演者"
        },
        {
            "str": "ENFP",
            "en": "Campaigner",
            "cn": "运动者"
        },
        {
            "str": "ENTP",
            "en": "Debater",
            "cn": "辩论者"
        },
        {
            "str": "ESTJ",
            "en": "Executive",
            "cn": "执行官"
        },
        {
            "str": "ESFJ",
            "en": "Consul",
            "cn": "咨询者"
        },
        {
            "str": "ENFJ",
            "en": "Protagonist",
            "cn": "主角"
        },
        {
            "str": "ENTJ",
            "en": "Commander",
            "cn": "指挥官"
        }
    ]);

const config = ref({
    imgurl: '',
    mbti_str: true,
    mbti_cn: true,
    mbti_en: true,
    cover: {
        show: true,
        mainText: "MBTI·十六型人格",
        subText: '',
        left: "50%",
        top: "30%",
    },
    logo: {
        show: true,
        left: "50%",
        top: "50%",
    },
    subtitle: {
        show: true,
        text: '2024官方最新版',
        left: "50%",
        top: "50%",
    },
    start: 0,
    end: 16,
    cols: 4,
    drc: 'w-[1280px] h-[720px]',
    position: 'items-end',
    place: 'text-center',
    fontSize: 'text-4xl',
    coverStyle: '!bg-white/80',
    coverFontStyle: '!text-gray-800'

});

const gridCols = computed(() => [
    'grid grid-cols-1',
    'grid grid-cols-2',
    'grid grid-cols-3',
    'grid grid-cols-4',
    'grid grid-cols-5',
    'grid grid-cols-6',
    'grid grid-cols-7',
    'grid grid-cols-8',
    'grid grid-cols-9',
    'grid grid-cols-10',
    'grid grid-cols-11',
    'grid grid-cols-12',
    'grid grid-cols-13',
    'grid grid-cols-14',
    'grid grid-cols-15',
    'grid grid-cols-16',
][parseInt(config.value.cols - 1)]);

function shuffleArray(array) {
    for (let i = array.length - 1; i > 0; i--) {
        let j = Math.floor(Math.random() * (i + 1));
        [array[i], array[j]] = [array[j], array[i]];
    }
    return array;
}

const shuffle = () => {
    data.value = shuffleArray(data.value);
}

const filtered = computed(() => {
    if (config.value.shuffle) {
        data.value = shuffleArray(data.value);
    }

    return data.value.slice(config.value.start, config.value.end);
});

const drcMap = {
    '1280x720': 'w-[1280px] h-[720px]',
    '720x1280': 'w-[720px] h-[1280px]',
}

const widthCmpt = computed(() => _.get({
    'w-[80rem] h-[45rem]': 1280,
    'w-[45rem] h-[80rem]': 720
}, config.value.drc));

const heightCmpt = computed(() => _.get({
    'w-[1280px] h-[720px]': 720,
    'w-[720px] h-[1280px]': 1280
}, config.value.drc));

const coverStyleMap = {
    '!bg-black/80': '黑底',
    '!bg-white/80': '白底',
    '!bg-none': '无底',
}

const coverFontStyleMap = {
    '!text-white': '白字',
    '!text-gray-800': '黑字',
    '!text-white [text-shadow:_0_4px_8px_rgb(0_0_0_/_100%)]': '白字Shadow',
    '!text-gray-800 [text-shadow:_0_4_8px_rgb(255_255_255_/_100%)]': '黑字Shadow',
}

const posiMap = {
    'items-start': '上',
    'items-center': '中',
    'items-end': '下',
}

const placeMap = {
    'text-left': '左',
    'text-center': '中',
    'pr-4 text-right': '右',
}

const sizeMap = {
    'text-7xl': '7xl',
    'text-6xl': '6xl',
    'text-5xl': '5xl',
    'text-4xl': '4xl',
    'text-3xl': '3xl',
    'text-2xl': '2xl',
    'text-xl': 'xl',
    'text-lg': 'lg',
}

const change = () => {
    if (config.value) {
        localData.create('temp', config.value);
    }
}

const inSaving = ref(false);
const list = ref({});
const prefix = ref('');
const filtered_list = ref({});
const showList = ref(true);

const mouseleave = () => {
    showList.value = true;
}

const localData = {
    fetch: () => {
        showList.value = true;
        let allData = JSON.parse(localStorage.getItem(`adsImageMaker-data`) ?? '{}');
        list.value = _.pickBy(allData, (val, key) => !_.startsWith(key, 'temp') && key.includes(prefix.value));
    },
    create: (name, cfg) => {
        let allData = JSON.parse(localStorage.getItem(`adsImageMaker-data`) ?? `{}`);
        _.set(allData, name, cfg);
        localStorage.setItem(`adsImageMaker-data`, JSON.stringify(allData));
    },
    show: (name, auto = false) => {
        let allData = JSON.parse(localStorage.getItem(`adsImageMaker-data`));
        if (_.get(allData, `${name}.imgurl`)) {
            _.merge(config.value, _.get(allData, name));
        } else {
            router.push({
                name: 'home',
            })
        }
        if (auto) {
            showList.value = false;
        }
    },
    del: (name) => {
        let allData = JSON.parse(localStorage.getItem(`adsImageMaker-data`));
        _.unset(allData, name);
        localStorage.setItem(`adsImageMaker-data`, JSON.stringify(allData));
        showList.value = false;
    },
    filter: () => {

    }
}

const saveJpeg = () => {

    inSaving.value = true;

    const node = document.querySelector('#save-image');

    toJpeg(node, {
        backgroundColor: '#ffffff',
        width: widthCmpt.value,
        height: heightCmpt.value,
        quality: 0.8,
    }).then(function (dataUrl) {
        var link = document.createElement('a');
        let date = moment();
        let dateStr = date.format("YYYYMMDD_HHmmss");

        link.download = `mbti_${dateStr}.jpeg`;
        link.href = dataUrl;
        link.click();

        return new Promise((resolve, reject) => {
            resolve(`mbti_${dateStr}`);
        });
    }).then((name) => {
        localData.create(name, config.value);
        inSaving.value = false;
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

watch(
    name,
    (newVal, oldVal) => {
        if (newVal != oldVal) {
            localData.show(props.name, true);
        }
    }
)
onMounted(() => {

    localData.fetch()

    if (props.name) {
        localData.show(props.name, true);
    } else {
        localData.show('temp', true);
    }
})
</script>

<template>
    <div class="flex flex-row w-lvw">
        <!-- left side console -->
        <div class="w-fit shrink-0 max-h-svh overflow-y-auto border-r-4 border-gray-200 border-double select-none">

            <div class="w-full p-5  flex flex-col gap-y-2">
                <!-- 数据 -->
                <div class="relative flex flex-row gap-x-3 group" @mouseover="localData.fetch()"
                    @mouseleave="mouseleave">
                    <!-- 过滤 -->
                    <input class="h-8 text-sm rounded" type='text' placeholder="关键词过滤" v-model="prefix"
                        @change="localData.fetch" @keypress="localData.fetch" />
                    <!-- 导入 -->
                    <div @click="importJson"
                        class="px-2 py-1 h-8 bg-blue-400 rounded text-white w-fit relative overflow-visible hover:z-50 hover:bg-blue-300">
                        导入
                    </div>
                    <div @click="exportJson"
                        class="px-2 py-1 h-8 bg-blue-400 rounded text-white w-fit relative overflow-visible hover:z-50 hover:bg-blue-300">
                        导出
                    </div>
                    <div v-show="showList"
                        class="absolute bg-white text-gray-800 shadow invisible top-8 right-0 h-lvh overflow-y-auto no-scrollbar max-h-80 w-fit group-hover:visible group-hover:z-50">
                        <div class="h-fit flex flex-col-reverse">
                            <div v-for="(item, name, index) in list" :key="name"
                                class="flex flex-row justify-between w-full px-2 py-1 gap-x-3">
                                <RouterLink :to="{ name: 'home', params: { name: name } }" replace>{{ name }}
                                </RouterLink>
                                <button @click="localData.del(name)">❌</button>
                            </div>
                        </div>
                    </div>
                </div>

                <button @click="saveJpeg" class="bg-blue-400 text-white rounded py-2 mb-4">保存图片到本地</button>

                <div class="flex flex-col">
                    <label for="imgurl">imgurl</label>
                    <input @change="change" id="imgurl" placeholder="https://*.<jpg,png,svg,...>"
                        v-model="config.imgurl" />
                </div>
                <div class="flex flex-col">
                    <label for="mainText">Cover.mainText</label>
                    <textarea @change="change" id="mainText" type="text" v-model="config.cover.mainText" />
                </div>

                <div class="flex flex-col">
                    <label for="subText">Cover.subText</label>
                    <textarea @change="change" id="subText" type="text" v-model="config.cover.subText" />
                </div>

                <div class="flex flex-col">
                    <label for="subTitle-text">Subtitle.text</label>
                    <textarea @change="change" id="subTitle-text" type="text" v-model="config.subtitle.text" />
                </div>

                <div class="flex flex-row-reverse justify-end gap-x-2 items-center">
                    <button @click="shuffle" class="px-3 py-1 bg-blue-500 rounded text-white">do it</button>
                    <label for="shuffle">随机数据顺序</label>
                </div>

                <div class="flex flex-row-reverse justify-end gap-x-2 items-center">
                    <label for="mbti_str">显示MBTI字符串</label>
                    <input @change="change" id="mbti_str" type="checkbox" v-model="config.mbti_str" />
                </div>
                <div class="flex flex-row-reverse justify-end gap-x-2 items-center">
                    <label for="mbti_cn">显示中文称号</label>
                    <input @change="change" id="mbti_cn" type="checkbox" v-model="config.mbti_cn" />
                </div>
                <div class="flex flex-row-reverse justify-end gap-x-2 items-center">
                    <label for="mbti_en">显示英文称号</label>
                    <input @change="change" id="mbti_en" type="checkbox" v-model="config.mbti_en" />
                </div>
                <div class="flex flex-row-reverse justify-end gap-x-2 items-center">
                    <label for="show-cover">显示标题</label>
                    <input @change="change" id="show-cover" type="checkbox" v-model="config.cover.show" />
                </div>
                <div class="flex flex-row-reverse justify-end gap-x-2 items-center">
                    <label for="show-subtitle">显示副标题</label>
                    <input @change="change" id="show-subtitle" type="checkbox" v-model="config.subtitle.show" />
                </div>
                <div class="flex flex-row-reverse justify-end gap-x-2 items-center">
                    <label for="show-logo">显示Logo</label>
                    <input @change="change" id="show-logo" type="checkbox" v-model="config.logo.show" />
                </div>
                <div class="flex flex-col">
                    <label for="cols">列数</label>
                    <input @change="change" id="cols" v-model="config.cols" />
                </div>
                <div class="flex flex-col">
                    <label for="start">start</label>
                    <input @change="change" id="start" v-model="config.start" />
                </div>
                <div class="flex flex-col">
                    <label for="end">end</label>
                    <input @change="change" id="end" v-model="config.end" />
                </div>

                <div class="flex flex-col">
                    <label for="drc">方向</label>
                    <select @change="change" id="drc" v-model="config.drc">
                        <option v-for="(val, label, index) in drcMap" :value="val">{{ label }}</option>
                    </select>
                </div>

                <div class="flex flex-col">
                    <label for="position">文字位置</label>
                    <select @change="change" id="position" v-model="config.position">
                        <option v-for="(label, val, index) in posiMap" :value="val">{{ label }}</option>
                    </select>
                </div>
                <div class="flex flex-col">
                    <label for="place">文字对齐</label>
                    <select @change="change" id="place" v-model="config.place">
                        <option v-for="(label, val, index) in placeMap" :value="val">{{ label }}</option>
                    </select>
                </div>
                <div class="flex flex-col">
                    <label for="fontSize">文字大小</label>
                    <select @change="change" id="fontSize" v-model="config.fontSize">
                        <option v-for="(label, val, index) in sizeMap" :value="val">{{ label }}</option>
                    </select>
                </div>

                <div class="flex flex-col">
                    <label for="coverStyle">大字背景</label>
                    <select @change="change" id="coverStyle" v-model="config.coverStyle">
                        <option v-for="(label, val, index) in coverStyleMap" :value="val">{{ label }}</option>
                    </select>
                </div>
                <div class="flex flex-col">
                    <label for="coverFontStyle">大字颜色</label>
                    <select @change="change" id="coverFontStyle" v-model="config.coverFontStyle">
                        <option v-for="(label, val, index) in coverFontStyleMap" :value="val">{{ label }}</option>
                    </select>
                </div>

            </div>
        </div>

        <!-- main content -->
        <div class="grow h-svh overflow-auto p-5">
            <div :class="config.drc" class="relative">
                <!-- 遮罩 -->
                <div class="absolute z-50 pointer-events-none left-1/2 top-1/2 border-dashed border border-gray-500 -translate-x-1/2 -translate-y-1/2 pr-24"
                    style="width:720px;height:720px">
                    <div class="w-full h-full border border-dashed border-gray-500"></div>
                </div>
                <!-- screenshot area -->
                <div id="save-image" class="relative overflow-hidden" :class="config.drc">

                    <!-- logo icon -->
                    <div v-if="config.logo.show" id="logo" v-draggable
                        class="absolute z-30 w-full h-fit flex justify-center -translate-x-1/2 -translate-y-1/2"
                        :style="`left: ${config.logo.left}; top: ${config.logo.top}`">
                        <div class="relative w-fit bg-white/80  rounded-2xl flex flex-row pl-2 pr-5 py-3 items-center"
                            :class="[config.coverStyle, config.position, config.place]">
                            <img src="../assets/images/icon.svg" alt="" referrerpolicy="no-referrer"
                                class="w-16 h-16 aspect-square object-cover drop-shadow-2xl shadow-white">
                            <GradientText class="text-5xl font-black">MBTI</GradientText>

                            <h1 class="text-4xl font-bold text-gray-800 pl-2"
                                :class="[config.position, config.coverFontStyle]">十六型人格测试</h1>
                        </div>
                    </div>

                    <!-- text cover -->
                    <div v-if="config.cover.show" id="cover" v-draggable
                        class="absolute -translate-x-1/2 -translate-y-1/2 font-black text-center w-fit text-nowrap px-10 py-5 rounded-2xl z-30 leading-normal"
                        :class="[config.coverStyle, config.position, config.place]"
                        :style="`left: ${config.cover.left}; top: ${config.cover.top}`">
                        <h1 class="whitespace-pre text-7xl leading-tight tracking-widest"
                            :class="[config.position, config.place, config.coverFontStyle]"
                            v-html="config.cover.mainText">
                        </h1>
                        <p class="whitespace-pre text-5xl mt-4 leading-normal tracking-[0.2em]"
                            :class="[config.position, config.place, config.coverFontStyle]" v-if="config.cover.subText"
                            v-html="config.cover.subText">
                        </p>
                    </div>

                    <!-- subtitle -->
                    <div v-if="config.subtitle.show" id="subtitle" v-draggable
                        class="absolute z-30 -translate-x-1/2 -translate-y-1/2 w-full h-fit flex justify-center"
                        :style="`left: ${config.subtitle.left}; top: ${config.subtitle.top}`">
                        <div class="relative w-fit rounded-2xl flex flex-row pl-2 pr-5 pb-4 pt-5 items-center"
                            :class="[config.coverStyle, config.position, config.place]">
                            <div class="pl-2 leading-relaxed whitespace-pre">
                                <h1 class="text-4xl font-bold"
                                    :class="[config.position, config.place, config.coverFontStyle]"
                                    v-html="config.subtitle.text"></h1>
                            </div>
                        </div>
                    </div>

                    <!-- mbti 16pf string -->
                    <div class="absolute left-0 top-0 w-full h-full mx-auto bg-cover text-white font-black"
                        :class="[gridCols, config.position, config.fontSize]">
                        <div v-for="(ctt, key, index) in filtered" :key="key"
                            class="[text-shadow:_0_4px_4px_rgb(0_0_0_/_100%)] pb-5 pl-5" :class="config.place">
                            <p v-if="config.mbti_str" class="font-mono">{{ ctt.str }}</p>
                            <p v-if="config.mbti_cn">{{ ctt.cn }}</p>
                            <p v-if="config.mbti_en">{{ ctt.en }}</p>
                        </div>
                    </div>

                    <!-- main image -->
                    <img :src="config.imgurl" class="object-fill" :class="config.drc">
                </div>
            </div>
        </div>
    </div>
</template>

<style></style>