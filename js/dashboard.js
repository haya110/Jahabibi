// Function to show the modal and fetch details
function showOrderDetails(orderId) {
    const modal = document.getElementById('orderDetailsModal');
    const itemsList = document.getElementById('modalOrderItems');

    // Clear previous details
    itemsList.innerHTML = '<li>Loading...</li>';
    document.getElementById('modalOrderId').textContent = '#' + orderId;
    modal.style.display = 'block';

    // Fetch details using AJAX
    fetch('../actions/fetch_order_details.php?order_id=' + orderId)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                itemsList.innerHTML = '<li>Error loading details: ' + data.error + '</li>';
                return;
            }

            // Update modal header info
            document.getElementById('modalOrderStatus').innerHTML = `<span class="status-badge ${get_status_class(data.status)}">${data.status.toUpperCase()}</span>`;
            document.getElementById('modalOrderDate').textContent = new Date(data.created_at).toLocaleString('en-US', { dateStyle: 'medium', timeStyle: 'short' });
            document.getElementById('modalOrderTotal').textContent = '₱' + parseFloat(data.total_amount).toFixed(2);
            
            itemsList.innerHTML = '';
            
            // Populate item list
            data.items.forEach(item => {
                const listItem = document.createElement('li');
                listItem.innerHTML = `
                    <span>${item.quantity} x ${item.product_name}</span>
                    <span>₱${parseFloat(item.subtotal).toFixed(2)}</span>
                `;
                itemsList.appendChild(listItem);
            });

        })
        .catch(error => {
            itemsList.innerHTML = '<li>Failed to fetch order details.</li>';
            console.error('Fetch error:', error);
        });
}

// Helper function (must match PHP logic)
function get_status_class(status) {
    switch (status) {
        case 'completed': return 'status-completed';
        case 'cancelled': return 'status-cancelled';
        default: return 'status-pending';
    }
}