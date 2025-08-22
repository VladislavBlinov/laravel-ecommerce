<template>
    <div v-if="productId">
        <div v-if="authStore.user">
            <h3 class="font-medium" v-if="!isEditing">Оставить отзыв</h3>
            <h3 v-else>Редактировать отзыв</h3>
            <label>Оценка:
                <select class="border-1 border-gray-400" v-model="rating">
                    <option disabled value="">Выберите</option>
                    <option v-for="n in 5" :key="n" :value="n">{{ n }}</option>
                </select>
            </label>
            <br/>
            <textarea class="border-1 p-1" v-model="comment" placeholder="Комментарий (необязательно)"></textarea>
            <br/>
            <button class="bg-blue-600 text-white p-1 rounded-md hover:bg-blue-500 cursor-pointer" @click="submit">{{
                    isEditing ? 'Обновить' : 'Отправить'
                }}
            </button>
        </div>
    </div>
</template>

<script>
import {useReviewsStore} from '@/stores/reviews.js'
import {useAuthStore} from '@/stores/auth.js'

export default {
    props: ['productId'],

    data() {
        return {
            rating: '',
            comment: '',
            reviewsStore: useReviewsStore(),
            authStore: useAuthStore(),
            isEditing: false,
            userReview: null,
        }
    },

    computed: {
        hasReviewed() {
            return !!this.userReview
        }
    },

    methods: {
        async submit() {
            if (!this.rating) return

            try {
                if (this.isEditing && this.userReview) {
                    await this.reviewsStore.updateReview(this.userReview.id, this.rating, this.comment)
                } else {
                    await this.reviewsStore.submitReview(this.productId, this.rating, this.comment)
                }

                this.loadUserReview()
            } catch (error) {
                console.error('Ошибка при отправке отзыва:', error)
            }
        },

        loadUserReview() {
            this.userReview = this.reviewsStore.getUserReview(this.productId)

            if (this.userReview) {
                this.rating = this.userReview.rating
                this.comment = this.userReview.review
                this.isEditing = true
            } else {
                this.rating = ''
                this.comment = ''
                this.isEditing = false
            }
        }
    },

    watch: {
        productId: {
            immediate: true,
            handler(newId) {
                if (newId) {
                    this.reviewsStore.loadReviews(newId).then(() => {
                        this.loadUserReview()
                    })
                }
            }
        }
    }
}
</script>
