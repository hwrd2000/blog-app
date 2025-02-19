<script setup>
import { ref } from "vue";
import axios from "axios";
import { usePage } from "@inertiajs/vue3";

// const emit = defineEmits(['postCreated']);

const user = usePage().props.auth.user;

const isModalOpen = ref(false);
const title = ref("");
const content = ref("");
const selectedImage = ref(null);

const openModal = () => {
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
};

const handleFileChange = (event) => {
    selectedImage.value = event.target.files[0];
};

const submitPost = async () => {
    if (!title.value.trim() || !content.value.trim()) {
        alert("Title and content are required.");
        return;
    }

    const formData = new FormData();
    formData.append("title", title.value);
    formData.append("content", content.value);
    if (selectedImage.value) {
        formData.append("image", selectedImage.value);
    }

    try {
        const response = await axios.post("/post", formData, {
            headers: { "Content-Type": "multipart/form-data" },
        });
        console.log("Post created:", response.data);
        // emit('postCreated', response.data.data);
        title.value = "";
        content.value = "";
        selectedImage.value = null;
        closeModal();
    } catch (error) {
        console.error("Error creating post:", error);
    }
};
</script>

<template>
    <div
        class="max-w-10xl mx-auto bg-white text-black border border-gray-300 shadow rounded p-4"
    >
        <div>
            <input
                type="text"
                @focus="openModal"
                :placeholder="`What's on your mind, ${user?.name || ''}?`"
                class="w-full p-3 border border-gray-400 rounded bg-white text-black cursor-pointer"
                readonly
            />
        </div>

        <Transition name="fade">
            <div
                v-if="isModalOpen"
                class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50"
            >
                <div
                    class="bg-white p-6 rounded-lg shadow-lg w-96"
                    @click.stop
                >
                    <h2 class="text-lg font-semibold mb-3">Create a Post</h2>
                    
                    <input
                        type="text"
                        v-model="title"
                        placeholder="Title"
                        class="w-full p-2 border border-gray-400 rounded mb-3"
                    />

                    <textarea
                        v-model="content"
                        placeholder="What's on your mind?"
                        class="w-full p-2 border border-gray-400 rounded resize-none mb-3"
                        rows="3"
                    ></textarea>

                    <input
                        type="file"
                        @change="handleFileChange"
                        accept="image/*"
                        class="w-full text-sm border border-gray-400 p-2 rounded"
                    />

                    <div class="flex justify-end gap-2 mt-3">
                        <button
                            @click="closeModal"
                            class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded"
                        >
                            Cancel
                        </button>
                        <button
                            @click="submitPost"
                            class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded"
                        >
                            Post
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </div>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>