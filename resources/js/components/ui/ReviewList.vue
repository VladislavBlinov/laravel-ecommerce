<template>
    <div class="mb-4" v-if="productId">
        <h4 class="font-bold">Отзывы:</h4>
        <div class="flex flex-col gap-2">
            <div class="bg-blue-200/60 rounded-md p-1" v-for="review in productReviews" :key="review.id">
                <strong>{{ review.user.name }}</strong> — ★{{ review.rating }}
                <p v-if="review.review">{{ review.review }}</p>
            </div>
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
