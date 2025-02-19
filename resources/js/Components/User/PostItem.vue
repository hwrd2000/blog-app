<script setup>
import { ref } from "vue";
import axios from "axios";

const props = defineProps({
  post: {
    type: Object,
    required: true,
  },
});

const emit = defineEmits(['postDeleted']);

const newComment = ref("");

const isEditing = ref(false);
const editedTitle = ref("");
const editedContent = ref("");
const editedImage = ref(null);

const getImageUrl = (imagePath) => {
  return "/storage/" + imagePath;
};

const submitComment = async () => {
  if (newComment.value.trim() === "") {
    alert("Comment cannot be empty.");
    return;
  }

  try {
    const response = await axios.post("/comment", {
      post_id: props.post.id,
      content: newComment.value,
      reply_id: null,
    });
    console.log("Comment created:", response.data);
    const newCommentObj = response.data.data;
    newCommentObj.replies = [];
    props.post.comments.push(newCommentObj);
    newComment.value = "";
  } catch (error) {
    console.error("Error creating comment:", error);
  }
};

const toggleReply = (comment) => {
  if (comment.showReply === undefined) {
    comment.showReply = true;
    comment.newReply = "";
  } else {
    comment.showReply = !comment.showReply;
  }
};

const submitReply = async (comment) => {
  if (!comment.newReply || comment.newReply.trim() === "") {
    alert("Reply cannot be empty.");
    return;
  }
  try {
    const response = await axios.post("/comment", {
      post_id: props.post.id,
      content: comment.newReply,
      reply_id: comment.id,
    });
    console.log("Reply created:", response.data);
    if (!comment.replies) {
      comment.replies = [];
    }
    comment.replies.push(response.data.data);
    comment.newReply = "";
    comment.showReply = false;
  } catch (error) {
    console.error("Error creating reply:", error);
  }
};

const enableEdit = () => {
  isEditing.value = true;
  editedTitle.value = props.post.title;
  editedContent.value = props.post.content;
};

const cancelEdit = () => {
  isEditing.value = false;
};

const handleFileChange = (event) => {
  editedImage.value = event.target.files[0];
};

const updatePost = async () => {
    try {
        const formData = new FormData();
        formData.append("_method", "PUT");
        formData.append("title", editedTitle.value);
        formData.append("content", editedContent.value);

        if (editedImage.value) {
            formData.append("image", editedImage.value);
        }

        const response = await axios.post(`/post/${props.post.id}`, formData, {
            headers: { "Content-Type": "multipart/form-data" }
        });

        props.post.title = response.data.data.title;
        props.post.content = response.data.data.content;
        props.post.image = response.data.data.image;

        isEditing.value = false;
    } catch (error) {
        console.error("Error updating post:", error);
    }
};

const deletePost = async () => {
  if (confirm("Are you sure you want to delete this post?")) {
    try {
      await axios.delete(`/post/${props.post.id}`);
      emit('postDeleted', props.post.id);
    } catch (error) {
      if (error.response && error.response.status === 403) {
        alert("You are not authorized to delete this post.");
      } else {
        console.error("Error deleting post:", error);
        alert("An error occurred while deleting the post.");
      }
    }
  }
};

const deleteComment = async (comment, parentComment = null) => {
  if (confirm("Are you sure you want to delete this comment?")) {
    try {
      await axios.delete(`/comment/${comment.id}`);
      if (parentComment) {
        parentComment.replies = parentComment.replies.filter(
          (reply) => reply.id !== comment.id
        );
      } else {
        props.post.comments = props.post.comments.filter(
          (c) => c.id !== comment.id
        );
      }
    } catch (error) {
      if (error.response && error.response.status === 403) {
        alert("You are not authorized to delete this comment.");
      } else {
        console.error("Error deleting comment:", error);
        alert("An error occurred while deleting the comment.");
      }
    }
  }
};
</script>

<template>
    <div class="mb-6 bg-white text-black border border-gray-300 shadow rounded p-4">
        <div class="flex justify-between items-start">
          <div>
            <p class="text-sm text-gray-600">
              Posted by: {{ post.user ? post.user.name : "OP" }}
            </p>
          </div>
          <div class="flex space-x-2">
            <button
              v-if="!isEditing"
              @click="enableEdit"
              class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm"
            >
              Update
            </button>
            <button
              @click="deletePost"
              class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm"
            >
              Delete
            </button>
          </div>
        </div>
    
        <div v-if="!isEditing">
          <h3 class="font-bold text-xl text-black">
            {{ post.title }}
          </h3>
          <p class="mt-2 text-gray-700">{{ post.content }}</p>
        </div>
        <div v-else class="mt-2">
          <input
            v-model="editedTitle"
            type="text"
            class="w-full p-2 border border-gray-400 rounded bg-white text-black"
            placeholder="Update title"
          />
          <textarea
            v-model="editedContent"
            class="w-full p-2 border border-gray-400 rounded mt-2 bg-white text-black"
            placeholder="Update content"
          ></textarea>
          <input type="file" @change="handleFileChange" accept="image/*" class="mt-2 w-full text-sm text-gray-900 border border-gray-400 rounded p-2" />
          <div class="flex space-x-2 mt-2">
            <button
              @click="updatePost"
              class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded text-sm"
            >
              Save
            </button>
            <button
              @click="cancelEdit"
              class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded text-sm"
            >
              Cancel
            </button>
          </div>
        </div>
    
        <div v-if="post.image" class="mt-2">
          <img
            :src="getImageUrl(post.image)"
            alt="Post image"
            class="w-15 h-15 object-cover rounded"
          />
        </div>
    
    <div class="mt-4">
      <h4 class="font-semibold text-black">
        <u>Comments</u>
      </h4>
      <ul>
        <li
          v-for="comment in post.comments"
          :key="comment.id"
          class="mt-2 text-gray-700"
        >
          <div>
            <span class="font-bold">
              {{ comment.user ? comment.user.name : "Unknown" }}:
            </span>
            <span> {{ comment.content }} </span>
            <button 
              @click="deleteComment(comment)"
              class="text-red-500 text-sm ml-2"
            >
              Delete
            </button>
          </div>
          
          <ul v-if="comment.replies && comment.replies.length" class="ml-4 mt-2 border-l border-gray-300 pl-2">
            <li
              v-for="reply in comment.replies"
              :key="reply.id"
              class="mt-1 text-gray-600"
            >
              <span class="font-bold">
                {{ reply.user ? reply.user.name : "Unknown" }}:
              </span>
              <span> {{ reply.content }} </span>
              <button 
                @click="deleteComment(reply, comment)"
                class="text-red-500 text-sm ml-2"
              >
              Delete
            </button>
            </li>
          </ul>

          <div class="mt-2">
            <button @click="toggleReply(comment)" class="text-blue-500 text-sm">
              Reply
            </button>
          </div>
          <div v-if="comment.showReply" class="mt-2 ml-4">
            <textarea
              v-model="comment.newReply"
              placeholder="Write a reply..."
              class="w-full p-2 border border-gray-400 rounded resize-none bg-white text-black"
              rows="1"
            ></textarea>
            <div class="flex justify-end mt-1">
              <button
                @click="submitReply(comment)"
                class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded text-sm"
              >
                Submit Reply
              </button>
            </div>
          </div>
        </li>
      </ul>
    </div>

    <div class="mt-4">
      <textarea
        v-model="newComment"
        placeholder="Write a comment..."
        class="w-full p-2 border border-gray-400 rounded resize-none bg-white text-black"
        rows="2"
      ></textarea>
      <div class="flex justify-end mt-2">
        <button
          @click="submitComment"
          class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded"
        >
          Submit Comment
        </button>
      </div>
    </div>
  </div>
</template>
