<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import PostForm from '@/Components/User/PostForm.vue';
import PostList from '@/Components/User/PostList.vue';
</script>

<template>
    <Head title="Home" />

    <AuthenticatedLayout>
        <!-- <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Home
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                    <div class="p-6 text-gray-900">
                        You're logged in!
                    </div>
                </div>
            </div>
        </div> -->

    <div class="mx-auto my-5 m max-w-7xl sm:px-6 lg:px-8">
        <PostForm />
      </div>

      <div class="py-5">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
          <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg border-b border-gray-200">
            <div class="p-6 text-gray-900 dark:text-dark-100">
             Suggested Post
            </div>
          </div>
        </div>
      </div>

      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <PostList />
      </div>
    </AuthenticatedLayout>
</template>
