import './bootstrap';

// Auto-save functionality for forms
document.addEventListener('DOMContentLoaded', function() {
    // Add loading states for Livewire components
    document.addEventListener('livewire:init', () => {
        Livewire.on('booking-updated', (event) => {
            // You can add custom JavaScript here for when bookings are updated
            console.log('Booking updated');
        });
    });
});
