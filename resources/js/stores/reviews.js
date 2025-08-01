import {defineStore} from 'pinia'
import api from '@/api.js'
import {useAuthStore} from '@/stores/auth.js'

export const useReviewsStore = defineStore('reviews', {
    state: () => ({
        reviews: []
    }),

    actions: {
        async loadReviews(productId) {
            const response = await api.get(`reviews/${productId}`)
            this.reviews = [
                ...this.reviews.filter(r => r.product_id !== productId),
                ...response.data.data
            ]
            console.log('ОТЗЫВЫ ', this.reviews)
        },

        async submitReview(productId, rating, comment) {
            const response = await api.post(`reviews/${productId}`, {rating, comment})
            this.reviews.push(response.data.data)
            return response.data.data
        },

        updateReview(reviewId, rating, comment) {
            return api.put(`reviews/${reviewId}`, {rating, comment}).then(response => {
                const index = this.reviews.findIndex(r => r.id === reviewId)
                if (index !== -1) {
                    this.reviews[index] = response.data.data
                }
            })
        },

        reviewsByProduct(productId) {
            return this.reviews.filter(r => r.product_id === productId)
        },

        userReviewedProduct(productId) {
            const auth = useAuthStore()
            return this.reviews.some(r => r.product_id === productId && r.user_id === auth.user?.id)
        },

        getUserReview(productId) {
            const auth = useAuthStore()
            return this.reviews.find(r => r.product_id === productId && r.user_id === auth.user?.id)
        }
    }
})
