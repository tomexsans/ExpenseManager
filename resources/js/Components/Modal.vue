<script setup>
import { computed } from 'vue'
const props = defineProps(['open','title']);



</script>

<template>

    <Teleport to="body">
        <div v-if="props.open" id="modal-container">
            <div ref="modal"  class="modal">
                <p class="px-4 py-2 border-b border-b-slate-800">{{ props.title ?? ""}}</p>
                <div id="modal-body" class="p-4">
                    <slot></slot>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<style scoped>
.modal {
  @apply fixed border border-gray-800  bg-white;
  
  z-index: 999;
  top: 30%;
  left: 40%;
  min-width: 500px;
}

#modal-body{
  min-height: 200px;
}

#modal-container{
    @apply bg-slate-50 bg-opacity-40;
    top:0;
    left:0;
    position:absolute;
    display:block;
    width:100%;
    height: 100%;
}
</style>