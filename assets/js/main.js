function addToCart(productId) {
    var btn = event.target;
    if (btn.tagName !== 'BUTTON') btn = btn.closest('button');
    if (!btn) return;
    var originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
    btn.disabled = true;

    fetch('add_to_cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'product_id=' + productId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message || 'Added to cart!');
            // Update cart count
            fetch('get_cart_count.php').then(r=>r.json()).then(d=>{
                document.getElementById('cart-count').innerText = d.count || 0;
                var floating = document.getElementById('floating-count');
                if (floating) floating.innerText = d.count || 0;
            });
        } else {
            alert(data.message || 'Error adding to cart');
            if (data.redirect && confirm('Please login first. Go to login page?')) {
                window.location.href = data.redirect;
            }
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        alert('Network error. Check console.');
    })
    .finally(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
}