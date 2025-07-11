import {defineStore} from 'pinia'
import api from '@/api'

export const useCategoryStore = defineStore('category', {
    state: () => ({
        categories: []
    }),

    actions: {
        async loadCategories() {
            if (this.categories.length === 0) {
                const res = await api.get('/categories')
                this.categories = res.data.data
            }
        }
    }
})
