<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { X, CheckCircle, AlertCircle } from 'lucide-vue-next';
import { ref, watch } from 'vue';

const page = usePage();
const showMessage = ref(false);
const message = ref('');
const messageType = ref<'success' | 'error'>('success');

watch(() => page.props.flash, (flash) => {
    if (flash?.success) {
        message.value = flash.success;
        messageType.value = 'success';
        showMessage.value = true;
        setTimeout(() => {
            showMessage.value = false;
        }, 5000);
    } else if (flash?.error) {
        message.value = flash.error;
        messageType.value = 'error';
        showMessage.value = true;
        setTimeout(() => {
            showMessage.value = false;
        }, 5000);
    }
}, { immediate: true });

const closeMessage = () => {
    showMessage.value = false;
};
</script>

<template>
    <div
        v-if="showMessage"
        :class="[
            'fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm transition-all duration-300',
            messageType === 'success' 
                ? 'bg-green-50 border border-green-200 text-green-800' 
                : 'bg-red-50 border border-red-200 text-red-800'
        ]"
    >
        <div class="flex items-start gap-3">
            <CheckCircle v-if="messageType === 'success'" class="h-5 w-5 text-green-600 mt-0.5" />
            <AlertCircle v-else class="h-5 w-5 text-red-600 mt-0.5" />
            <div class="flex-1">
                <p class="text-sm font-medium">
                    {{ messageType === 'success' ? 'Success' : 'Error' }}
                </p>
                <p class="text-sm mt-1">{{ message }}</p>
            </div>
            <button
                @click="closeMessage"
                class="text-gray-400 hover:text-gray-600 transition-colors"
            >
                <X class="h-4 w-4" />
            </button>
        </div>
    </div>
</template> 