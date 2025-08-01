<template>
    <div v-if="productId">
        <h3>Отзывы:</h3>
        <div v-for="review in productReviews" :key="review.id">
            <strong>{{ review.user.name }}</strong> — ★{{ review.rating }}
            <p v-if="review.review">{{ review.review }}</p>
        </div>
    </div>
</template>

<script>
import {useReviewsStore} from '@/stores/reviews.js'

export default {
    props: ['productId'],

    data() {
        return {
            reviewsStore: useReviewsStore()
        }
    },

    computed: {
        productReviews() {
            return this.reviewsStore.reviewsByProduct(this.productId)
        }
    },


    watch: {
        productId: {
            immediate: true,
            handler(newId) {
                if (newId) {
                    this.reviewsStore.loadReviews(newId)
                }
            }
        }
    }
}
</script>
