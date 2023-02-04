<script setup>
import { onMounted, ref, reactive } from '@vue/runtime-core';
import Main from '../Layouts/main.vue';
import Navigation from '../Components/NavigationLinks.vue';
import VideoActionButtons from '../Components/VideoActionButtons.vue';
import { editingTools, navTitles } from '../Functions/globalTools.vue';

/**************************************    DATA   ***************************************/
const app = document.getElementById('app');
const timeline = ref(null);
const timelineIsActive = ref(false);
const videoInput = ref(null);

// Canvas
const canvasContainer = ref(null);
const canvasImages = ref([]);

let videos = ref('');

/**************************************    LIFECYCLE   ***************************************/
onMounted(async () => {
    videos.value = await fetch('https://jsonplaceholder.typicode.com/photos')
                         .then(response => response.json());
    
    // Add custom style
    app.classList.add('bg-neutral-800');
    app.style.color = '#ffffff';
});

/**************************************    METHOD   ***************************************/
// Show video in timeline method
const showVideo = (src) => {
    timeline.value.src = src;
    timeline.value.style.border = 'none';
};
const openFileDialog = () => {
    videoInput.value.click();
}
const uploadVideo = () => {
    let file = videoInput.value.files[0];
    const reader = new FileReader();
  
    reader.addEventListener("load", function () {
        // convert image file to base64 string
        timeline.value.src = reader.result;
    }, false);

    if (file) {
        reader.readAsDataURL(file);
    }

    timeline.value.src = URL.createObjectURL(file);
    timeline.value.addEventListener('loadeddata', () => {
        setInterval(captureImage, 1000);
      });
}

const togglePlayVideo = (event) => {
    if(event.target.className.indexOf('fa-play') != -1) {
        event.target.classList.remove('fa-play');
        event.target.classList.add('fa-stop');
        timeline.value.play();
    } else {
        event.target.classList.remove('fa-stop');
        event.target.classList.add('fa-play');
        timeline.value.pause();
    }
};

const backWard = () => {
    timeline.value.currentTime -= 5;
};

const forWard = () => {
    timeline.value.currentTime += 5;
};

const replayVideo = () => {
    timeline.value.currentTime = 0;
}

const captureImage = () => {
    timelineIsActive.value = true;

    const canvas = document.createElement('canvas');
    canvas.width = 150;
    canvas.height = 100;
    canvas.getContext('2d').drawImage(timeline.value, 0, 0, timeline.value.videoWidth, timeline.value.videoHeight, 0, 0, 150, 100);

    canvasImages.value.push(canvas);
    const index = canvasImages.value.length - 1;
    canvas.onclick = () => {
        seekToTime(index);
    };
    canvasContainer.value.appendChild(canvas);
    canvasContainer.value.scrollLeft = canvasContainer.value.scrollWidth;
};

const seekToTime = (index) => {
    timeline.value.currentTime = index * timeline.value.duration / canvasImages.value.length;
    timeline.pause();

    const wasPlaying = !timeline.value.paused;
    if (wasPlaying) {
        timeline.value.pause();
    }
    timeline.value.currentTime = currentTime;
    if (wasPlaying) {
        timeline.value.play();
    }
};

const updateIndicator = () => {
      scrollIndicator.value.style.left =
        (timeline.value.currentTime / timeline.value.duration) *
        canvasContainer.value.scrollWidth +
        'px'
    }

</script>

<template>
    <Navigation :video-title="navTitles.video" :timeline-title="navTitles.timeline" :settings="navTitles.settings"/>
    <Main>
        <template #filesOrganisation>
            <ul class="list-none w-full h-screen overflow-y-scroll py-10 px-2">
                <li class="grid grid-cols-2 gap-[0.40rem]">
                    <img v-for="video in videos.slice(0, 20)" :key="video.id" :src="video.url" @click="showVideo(video.url)"
                         class="cursor-pointer hover:scale-125 rounded h-28 w-full" >
                </li>
                <button @click="openFileDialog" class="absolute bottom-2 left-[3.5rem] bg-indigo-500 shadow-lg shadow-cyan-500/50 font-bold py-2 px-4 rounded-l" id="uploadVideo">
                    Ajouter une vidéo
                </button>
                <input class="hidden" type="file" ref="videoInput" @change="uploadVideo"/>
            </ul>
        </template>
        <template #display>
            <div class="flex flex-col">
                <video class="mt-10 h-[25rem] w-full shadow-lg shadow-blue-500/50" ref="timeline"></video>
                <VideoActionButtons @backward-event="backWard" @play-video="togglePlayVideo" @forward-event="forWard" @replay-video="replayVideo" />
                <div class="mt-16">
                    <h4 class="title font-mono" v-if="timelineIsActive">Timeline</h4>
                    <div class="cursor-grabbing flex flew-row max-h-32 snap-x overflow-x-scroll hover:resize whitespace-nowrap rounded" ref="canvasContainer">
                    </div>
                </div>
            </div>
        </template>
        <template #edit>
            <div class="list-none w-full h-[50%] overflow-y-scroll py-10 px-2 pr-[1.5rem] grid grid-cols-2 gap-1 max-h-[50vh] mr-3">
                <div v-for="edition in editingTools" :key="edition.id">
                   <div class="bg-slate-700 p-1 cursor-pointer w-full text-center rounded">
                       <i :class="edition.icon"></i>
                       <p class="text-center">
                            {{ edition.text }}
                       </p>
                   </div>
                </div>
            </div>
        </template>
    </Main>
</template>

<style scoped>
canvas {
    height: 100px;
    width: 150px;
}

.canvas-container {
  overflow-x: scroll;
  white-space: nowrap;
  display: flex;
}

.scroll-indicator {
  position: absolute;
  top: 0;
  bottom: 0;
  width: 1px;
  background-color: white;
}
</style>