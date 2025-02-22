document.addEventListener('alpine:init', () => {
    Alpine.data('{{pageSlug}}', () => ({
        ui: {},
        data: {},
        model: {},

        init() {

        }
    }))
}) 