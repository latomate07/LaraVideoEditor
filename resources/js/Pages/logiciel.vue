<script setup>
import { onMounted, ref, computed } from '@vue/runtime-core'
import Main from '../Layouts/main.vue'
import Navigation from '../Components/NavigationLinks.vue'
import VideoActionButtons from '../Components/VideoActionButtons.vue'
import { editingTools, navTitles } from '../Functions/globalTools.vue'
import { useForm, router } from '@inertiajs/vue3'
import SignIn from '../Modals/SignIn.vue'
import waitingVideoAnimation from '../../../public/storage/animations/C8B4Oz7AA0.json'

/**************************************    DATA   ***************************************/
const app = document.getElementById('app');
const props = defineProps({
    videos: Object,
    errors: Object
});

// Video
const timeline = ref(null);
const timelineIsActive = ref(false);
const videoInput = ref(null);
const timelineUrl = computed(() => {
    return timeline.value ? timeline.value.src : ''
});

// Canvas
const canvasContainer = ref(null);
const canvasImages = ref([]);

// Auth Forms
const showSignInForm = ref(false);

/**************************************    LIFECYCLE   ***************************************/
onMounted(async () => {
    // Add custom style
    app.classList.add('bg-neutral-800', 'relative');
    app.style.color = '#ffffff';
});

/**************************************    METHOD   ***************************************/
// Show video in timeline method
const showVideo = (src) => {
    const prefix = '/upload/';
    timeline.value.src = prefix + src;
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
        timelineIsActive.value = true;

        reader.readAsDataURL(file);
        timeline.value.src = URL.createObjectURL(file);
        timeline.value.addEventListener('loadeddata', () => {
            setInterval(captureImage, 1000);
        });

        router.post('/upload-video', {
            preserveScroll: true,
            _method: 'POST',
            video: file
        })
    }
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
    if(timelineIsActive.value == true) {
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
    }
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

const deleteVideo = (video) => {
    if(confirm('Êtes-vous sûr de vouloir supprimer cette vidéo ?')) {
        router.post('/delete-video', {
            _method: 'DELETE',
            video: video
        });

        timelineIsActive.value = false;
        timeline.value.src = "";
    }
};
</script>

<template>
    <Navigation :video-title="navTitles.video" :timeline-title="navTitles.timeline" :settings="navTitles.settings"/>
    <Main>
        <template #filesOrganisation>
            <div class="h-screen overflow-y-scroll">
                <Transition>
                    <div class="py-10 px-2" v-if="props.videos.length == 0">
                        <lottie-player class="bg-transparent w-full h-full mx-auto" :src="waitingVideoAnimation" mode="bounce" speed="1" loop  autoplay></lottie-player>
                        <p class="text-center w-full">Vos vidéos s'afficheront ici...</p>
                    </div>
                </Transition>
                <ul class="list-none w-full h-auto py-10 px-2 leading-3 grid grid-cols-2 gap-[0.40rem]">
                    <li class="relative" v-for="video in props.videos" :key="video.id">
                        <video :src="'/upload/'+video.url" @click="showVideo(video.url)"
                            class="hover:opacity-25 transition-opacity cursor-pointer rounded h-28 w-full">
                        </video>
                        <i class="absolute top-6 right-2 cursor-pointer hover:text-red-400 fa fa-trash" @click="deleteVideo(video)"></i>
                    </li>
                </ul>
                <div class="absolute flex bottom-20 left-[4rem] items-center justify-between">
                    <i @click="showSignInForm = !showSignInForm" class="fa fa-user rounded-full absolute right-[11.2rem] bg-indigo-500 shadow-lg shadow-cyan-500/50 font-bold py-2 px-4 cursor-pointer"></i>
                    <button @click="openFileDialog" class="bg-indigo-500 shadow-lg shadow-cyan-500/50 font-bold py-2 px-4 rounded" id="uploadVideo">
                        Ajouter une vidéo
                    </button>
                </div>
                <input class="hidden" type="file" ref="videoInput" @change="uploadVideo"/>
            </div>
        </template>
        <template #display>
            <div class="flex flex-col">
                <video class="mt-10 h-[25rem] w-full shadow-lg shadow-blue-500/50" ref="timeline"></video>
                <VideoActionButtons @backward-event="backWard" @play-video="togglePlayVideo" @forward-event="forWard" @replay-video="replayVideo" />
                <Transition>
                    <div class="mt-16" v-if="timelineIsActive">
                        <h4 class="title font-mono">Timeline</h4>
                        <div class="cursor-grabbing flex flew-row max-h-32 snap-x overflow-x-scroll hover:resize whitespace-nowrap rounded" ref="canvasContainer"></div>
                    </div>
                </Transition>
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

    <!-- Modals -->
    <div v-if="showSignInForm" id="sign-in" 
    class="fixed top-0 left-0 right-0 z-50 w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-modal md:h-full">
        <SignIn />
        <p @click="showSignInForm = !showSignInForm" class="text-center font-mono cursor-pointer text-xl absolute top-10 right-10 text-black p-5 py-3 border-2 rounded-full">X</p>
    </div>
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