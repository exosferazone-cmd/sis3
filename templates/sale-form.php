<?php 
require_once TEMPLATES_PATH . '/header.php';
?>
<div class="page-header">
    <h1>Crear Nueva Venta</h1>
</div>

<?php if (isset($_SESSION['error_message'])): ?>
    <div class="alert alert-danger">
        <?php echo Helpers::escape($_SESSION['error_message']); ?>
        <?php unset($_SESSION['error_message']); ?>
    </div>
<?php endif; ?>

<form id="saleForm" action="/ventas/save" method="POST">
    <input type="hidden" name="csrf_token" value="<?php echo Helpers::escape($data['csrf_token']); ?>">
    
    <div class="form-section">
        <h3>Datos de la Venta</h3>
        
         <div class="form-group">
            <label for="customer_id">Cliente</label>
            <select id="customer_id" name="customer_id">
                <option value="">P¨²blico General</option>
                <?php foreach ($data['customers'] as $customer): ?>
                    <option value="<?php echo Helpers::escape($customer['id']); ?>"><?php echo Helpers::escape($customer['name']); ?> (<?php echo Helpers::escape($customer['email']); ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
           <label for="channel_id">Canal de Venta</label>
            <select id="channel_id" name="channel_id" required>
           <option value="">Seleccione un canal</option>
            <?php foreach ($data['channels'] as $channel): ?>
            <option value="<?php echo Helpers::escape($channel['id']); ?>"><?php echo Helpers::escape($channel['name']); ?></option>
            <?php endforeach; ?>
           </select>
        </div>
    </div>
    
    <div class="form-section">
        <h3>Productos</h3>
        <div id="product-list">
        </div>
        <button type="button" id="addProductLineBtn" class="btn btn-secondary">Agregar Producto</button>
    </div>

    <button type="submit" class="btn btn-primary">Guardar Venta</button>
</form>

<?php require_once TEMPLATES_PATH . '/footer.php'; ?>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const productListContainer = document.getElementById('product-list');
        const addProductLineBtn = document.getElementById('addProductLineBtn');
        
        const products = <?php echo json_encode($data['products']); ?>;

        const addProductLine = () => {
            const productLine = document.createElement('div');
            productLine.classList.add('product-line');

            let productOptions = '<option value="">Seleccione un producto</option>';
            products.forEach(p => {
                productOptions += `<option 
                    value="${p.id}" 
                    data-price="${p.price}"
                    data-stock="${p.stock}"
                >${p.name} (Stock: ${p.stock} | $${parseFloat(p.price).toFixed(2)})</option>`;
            });

            productLine.innerHTML = `
                <div class="form-group product-select-group">
                    <label>Producto</label>
                    <select name="product_id[]" class="product-select" required>
                        ${productOptions}
                    </select>
                </div>
                <div class="form-group quantity-group">
                    <label>Cantidad</label>
                    <input type="number" name="quantity[]" class="quantity-input" min="1" value="1" required>
                    <small class="stock-info text-muted">Stock: N/A</small>
                </div>
                <button type="button" class="btn btn-danger remove-product-line">Eliminar</button>
            `;
            productListContainer.appendChild(productLine);
            
            attachProductListeners(productLine);
        };

        const attachProductListeners = (productLine) => {
            const selectInput = productLine.querySelector('.product-select');
            const quantityInput = productLine.querySelector('.quantity-input');
            const stockInfo = productLine.querySelector('.stock-info');

            selectInput.addEventListener('change', () => {
                const selectedOption = selectInput.options[selectInput.selectedIndex];
                const stock = parseInt(selectedOption.dataset.stock);
                const price = parseFloat(selectedOption.dataset.price);

                if (selectedOption.value) {
                    quantityInput.max = stock;
                    stockInfo.textContent = `Stock: ${stock} | Precio: $${price.toFixed(2)}`;
                    quantityInput.disabled = false;
                    
                    if (parseInt(quantityInput.value) > stock) {
                        quantityInput.value = stock;
                    }
                } else {
                    quantityInput.disabled = true;
                    stockInfo.textContent = 'Stock: N/A';
                    quantityInput.value = 1;
                }
            });

            quantityInput.addEventListener('input', () => {
                 const maxStock = parseInt(quantityInput.max);
                 const currentValue = parseInt(quantityInput.value);
                 if (currentValue > maxStock) {
                     quantityInput.value = maxStock;
                     alert(`La cantidad no puede exceder el stock disponible (${maxStock}).`);
                 }
            });
        };

        addProductLine();

        addProductLineBtn.onclick = addProductLine;

        productListContainer.addEventListener('click', (event) => {
            if (event.target.classList.contains('remove-product-line')) {
                event.target.closest('.product-line').remove();
            }
        });
    });
</script>