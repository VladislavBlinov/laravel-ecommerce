<template>
    <div>
        <h2 class="text-xl mb-4">Профиль пользователя</h2>

        <div v-if="authStore.isLoading">
            Загрузка...
        </div>

        <div v-else class="space-y-3">
            <div>
                <label class="font-semibold">Имя:</label>
                <div v-if="!isEditing">
                    {{ authStore.user.name }}
                </div>
                <input v-else v-model="form.name" class="border border-gray-300 px-3 py-1 rounded w-full"/>
            </div>

            <div>
                <label class="font-semibold">Email:</label>
                <div v-if="!isEditing">
                    {{ authStore.user.email }}
                </div>
                <input v-else v-model="form.email" class="border border-gray-300 px-3 py-1 rounded w-full"/>
            </div>

            <div>
                <label class="font-semibold">Адрес:</label>
                <div v-if="!isEditing">
                    {{ authStore.user.address }}
                </div>
                <input v-else v-model="form.address" class="border border-gray-300 px-3 py-1 rounded w-full"/>
            </div>

            <div class="pt-4">
                <button v-if="!isEditing" @click="startEditing"
                        class="bg-blue-500 hover:bg-blue-600 text-white py-1 px-4 rounded">
                    Редактировать
                </button>
                <div v-else class="space-x-2">
                    <button @click="save" class="bg-green-500 hover:bg-green-600 text-white py-1 px-4 rounded">
                        Сохранить
                    </button>
                    <button @click="cancel" class="bg-gray-300 hover:bg-gray-400 text-black py-1 px-4 rounded">
                        Отмена
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {useAuthStore} from "@/stores/auth.js";

export default {
    data() {
        return {
            isEditing: false,
            form: {
                name: '',
                email: '',
                address: ''
            }
        };
    },
    created() {
        this.authStore = useAuthStore();
        this.setFormFromUser();
    },
    methods: {
        setFormFromUser() {
            const user = this.authStore.user;
            this.form.name = user.name;
            this.form.email = user.email;
            this.form.address = user.address;
        },
        startEditing() {
            this.setFormFromUser();
            this.isEditing = true;
        },
        cancel() {
            this.isEditing = false;
        },
        async save() {
            try {
                await this.authStore.updateProfile(this.form);
                this.isEditing = false;
            } catch (e) {
                console.error('Ошибка при сохранении:', e);
            }
        }
    }
};
</script>
