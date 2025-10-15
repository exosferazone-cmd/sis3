<?php require_once TEMPLATES_PATH . '/header.php'; ?>
<div class="container mt-4">
    <h2>Control de Inventario</h2>
    <button id="addProductBtn" class="btn btn-primary mb-3">Agregar Nuevo Producto</button>
    <table class="table table-striped table-responsive">
         <thead>
            <tr>
                <th>Nombre</th>
                <th>SKU</th>
                <th>Precio Normal</th>
                <th>Precio Rebajado</th>
                <th>Stock</th>
                <th>Tipo</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data['products'] as $product): ?>
                <tr class="<?php echo ($product['stock'] <= $product['min_stock_alert']) ? 'low-stock-row' : ''; ?>">
                    <td><?php echo Helpers::escape($product['name']); ?></td>
                    <td><?php echo Helpers::escape($product['sku']); ?></td>
                    <td>$<?php echo Helpers::escape(number_format($product['regular_price'], 2)); ?></td>
                    <td>$<?php echo Helpers::escape(number_format($product['sale_price'], 2)); ?></td>
                    <td><?php echo Helpers::escape($product['stock']); ?></td>
                    <td><?php echo Helpers::escape(ucfirst($product['woo_type'])); ?></td>
                    <td>
                        <button 
                            class="btn btn-sm btn-warning  btn-edit" 
                            data-id="<?php echo $product['id']; ?>" 
                            data-name="<?php echo Helpers::escape($product['name']); ?>" 
                            data-description="<?php echo Helpers::escape($product['description']); ?>" 
                            data-sku="<?php echo Helpers::escape($product['sku']); ?>"
                            data-regular_price="<?php echo Helpers::escape($product['regular_price']); ?>" 
                            data-sale_price="<?php echo Helpers::escape($product['sale_price']); ?>" 
                            data-woo_type="<?php echo Helpers::escape($product['woo_type']); ?>" 
                            data-stock="<?php echo Helpers::escape($product['stock']); ?>" 
                            data-min_stock_alert="<?php echo Helpers::escape($product['min_stock_alert']); ?>"
                            data-attributes_json='<?php echo Helpers::escape($product['attributes_json']); ?>'
                            data-images_url="<?php echo Helpers::escape($product['images_url']); ?>"
                        >Editar</button>
                        <a href="/productos/delete?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de que deseas eliminar este producto?');">Eliminar</a>
                    </td>
                      <td>
                     <a href="/sync/product?id=<?php echo $product['id']; ?>" class="btn btn-success" onclick="return confirm('¿Estás seguro de que deseas sincronizar este producto con WooCommerce? Esto puede actualizar los datos en tu tienda.');">Sincronizar</a>
                     </td>
                </tr>
            <?php endforeach; ?>
       </tbody>
    </table>
</div>
<?php require_once TEMPLATES_PATH . '/footer.php'; ?>

<!-- Modal Bootstrap para edición/agregado de producto -->
<div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Editar Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form id="productForm" action="/productos/add" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?php echo Helpers::escape($data['csrf_token']); ?>">
                    <input type="hidden" id="productId" name="id">
                    <div class="mb-3">
                        <label for="productName" class="form-label">Nombre</label>
                        <input type="text" id="productName" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="productSKU" class="form-label">SKU</label>
                        <input type="text" id="productSKU" name="sku" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="productDescription" class="form-label">Descripción</label>
                        <textarea id="productDescription" name="description" class="form-control"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="productType" class="form-label">Tipo de Producto (WooCommerce)</label>
                        <select id="productType" name="woo_type" class="form-select" required>
                            <option value="simple">Simple</option>
                            <option value="variable">Variable</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="productRegularPrice" class="form-label">Precio Normal</label>
                            <input type="number" id="productRegularPrice" name="regular_price" step="0.01" min="0" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="productSalePrice" class="form-label">Precio Rebajado (Opcional)</label>
                            <input type="number" id="productSalePrice" name="sale_price" step="0.01" min="0" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="productStock" class="form-label">Stock</label>
                            <input type="number" id="productStock" name="stock" min="0" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="productMinStock" class="form-label">Alerta de Stock Mínimo</label>
                            <input type="number" id="productMinStock" name="min_stock_alert" min="0" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="productImagesURL" class="form-label">URLs de Imágenes (Separadas por coma)</label>
                        <textarea id="productImagesURL" name="images_url" class="form-control"></textarea>
                        <small class="form-text text-muted">Ejemplo: url1.jpg,url2.jpg</small>
                    </div>
                    <div class="mb-3">
                        <label for="productAttributesInput" class="form-label">Atributos (Ej: Color|Azul,Blanco;Talle|S,M)</label>
                        <textarea id="productAttributesInput" name="attributes_input" rows="3" class="form-control" placeholder="Color|Azul,Blanco,Negra;Talles|S,M,L"></textarea>
                        <small class="form-text text-muted">Separa los atributos con <b>;</b> y los valores con <b>,</b>.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php require_once TEMPLATES_PATH . '/footer.php'; ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const addBtn = document.getElementById('addProductBtn');
    const productModal = new bootstrap.Modal(document.getElementById('productModal'));
    const form = document.getElementById('productForm');
    const modalTitle = document.getElementById('modalTitle');
    const productId = document.getElementById('productId');
    const productName = document.getElementById('productName');
    const productDescription = document.getElementById('productDescription');
    const productSKU = document.getElementById('productSKU');
    const productRegularPrice = document.getElementById('productRegularPrice');
    const productSalePrice = document.getElementById('productSalePrice');
    const productType = document.getElementById('productType');
    const productStock = document.getElementById('productStock');
    const productMinStock = document.getElementById('productMinStock');
    const productImagesURL = document.getElementById('productImagesURL');
    const productAttributesInput = document.getElementById('productAttributesInput');
    const editBtns = document.querySelectorAll('.btn-edit');
    const detailBtns = document.querySelectorAll('.btn-detail');

    function jsonToAttributeString(jsonString) {
        if (!jsonString) return '';
        try {
            const attributes = JSON.parse(jsonString);
            let result = [];
            attributes.forEach(attr => {
                if (Array.isArray(attr.values)) {
                    const values = attr.values.join(',');
                    result.push(`${attr.name}|${values}`);
                }
            });
            return result.join(';');
        } catch (e) {
            return '';
        }
    }

    addBtn.addEventListener('click', function() {
        modalTitle.textContent = 'Agregar Nuevo Producto';
        form.action = '/productos/add';
        productId.value = '';
        productName.value = '';
        productDescription.value = '';
        productSKU.value = '';
        productRegularPrice.value = '';
        productSalePrice.value = '';
        productType.value = 'simple';
        productStock.value = '';
        productMinStock.value = '';
        productImagesURL.value = '';
        productAttributesInput.value = '';
        productModal.show();
    });

    editBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            modalTitle.textContent = 'Editar Producto';
            form.action = '/productos/edit';
            productId.value = btn.dataset.id;
            productName.value = btn.dataset.name;
            productDescription.value = btn.dataset.description;
            productSKU.value = btn.dataset.sku;
            productRegularPrice.value = btn.dataset.regular_price;
            productSalePrice.value = btn.dataset.sale_price;
            productType.value = btn.dataset.woo_type;
            productStock.value = btn.dataset.stock;
            productMinStock.value = btn.dataset.min_stock_alert;
            productImagesURL.value = btn.dataset.images_url;
            productAttributesInput.value = jsonToAttributeString(btn.dataset.attributes_json);
            productModal.show();
        });
    });

    detailBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            modalTitle.textContent = 'Detalle de Producto';
            form.action = '/productos/edit'; // Permite editar desde el detalle
            productId.value = btn.dataset.id;
            productName.value = btn.dataset.name;
            productDescription.value = btn.dataset.description;
            productSKU.value = btn.dataset.sku;
            productRegularPrice.value = btn.dataset.regular_price;
            productSalePrice.value = btn.dataset.sale_price;
            productType.value = btn.dataset.woo_type;
            productStock.value = btn.dataset.stock;
            productMinStock.value = btn.dataset.min_stock_alert;
            productImagesURL.value = btn.dataset.images_url;
            productAttributesInput.value = jsonToAttributeString(btn.dataset.attributes_json);
            productModal.show();
        });
    });
});
</script>
