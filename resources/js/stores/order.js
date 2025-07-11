import {defineStore} from 'pinia'
import api from '@/api.js'
import {useCartStore} from '@/stores/cart.js'

export const useOrderStore = defineStore('order', {
    state: () => ({
        isSubmitting: false,
        lastOrder: null,
    }),

    actions: {
        async createOrder(address) {
            const cartStore = useCartStore()

            const payload = {
                address,
                items: cartStore.items.map(item => ({
                    product_id: item.product.id,
                    quantity: item.quantity,
                })),
            }

            this.isSubmitting = true
            try {
                const response = await api.post('/orders', payload)
                this.lastOrder = response.data.order
                localStorage.setItem('lastOrderId', JSON.stringify(response.data.order.id));
                console.log('RESPONSE_ORDER', response.data)
                await cartStore.clearCart()
                return response.data
            } finally {
                this.isSubmitting = false
            }
        }
        
    }
})
