<template>
  <div class="flex h-40 items-center justify-center text-4xl">
    <h1>About</h1>
  </div>
  <div class="flex h-40 items-center justify-center text-4xl">
    <button
      type="button"
      @click="main.increment"
      class="rounded border bg-gray-300 px-4 py-2"
    >
      Counter: {{ counter }}
    </button>
  </div>
  <div
    class="mx-auto flex max-w-7xl flex-col items-center justify-center gap-4 text-justify text-3xl mb-6"
  >
    <p>
      Lorem ipsum dolor sit amet consectetur adipisicing elit. Magni impedit
      delectus sint aperiam fugiat quisquam perferendis! Eaque vitae sit,
      laboriosam odit, aspernatur officia dolores delectus dolor esse, quae
      nobis in.
    </p>
    <p>
      Lorem ipsum dolor, sit amet consectetur adipisicing elit. Tenetur
      voluptatem dignissimos eligendi, rem deserunt quas velit pariatur
      molestias, eum dicta ut quisquam. Est corporis eum ea! Iste officiis
      dolorum magni.
    </p>
  </div>
</template>

<script setup>
import { useCounterStore } from "../../../stores/counter";
import { storeToRefs } from "pinia";
const main = useCounterStore();
const { counter } = storeToRefs(main);
</script>
