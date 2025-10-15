<?php require_once TEMPLATES_PATH . '/header.php'; ?>
<div class="page-header">
    <h1>Gestión de Caja</h1>
    <p>Balance actual: <span class="balance-amount">$<?php echo Helpers::escape(number_format($data['balance'], 2)); ?></span></p>
</div>

<div class="financial-summary">
    <div class="summary-card income">
        <h4>Ingresos Totales</h4>
        <p>$<?php echo Helpers::escape(number_format($data['totalIncome'], 2)); ?></p>
    </div>
    <div class="summary-card expenses">
        <h4>Egresos Totales</h4>
        <p>$<?php echo Helpers::escape(number_format($data['totalExpenses'], 2)); ?></p>
    </div>
</div>

<div class="form-section">
    <h3>Registrar Transacción</h3>
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success">
            <?php echo Helpers::escape($_SESSION['success_message']); ?>
            <?php unset($_SESSION['success_message']); ?>
        </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger">
            <?php echo Helpers::escape($_SESSION['error_message']); ?>
            <?php unset($_SESSION['error_message']); ?>
        </div>
    <?php endif; ?>
    <form action="/caja/add-transaction" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo Helpers::escape($data['csrf_token']); ?>">
        <div class="form-group">
            <label for="type">Tipo</label>
            <select id="type" name="type" required>
                <option value="ingreso">Ingreso</option>
                <option value="egreso">Egreso</option>
            </select>
        </div>
        <div class="form-group">
            <label for="category">Categoría</label>
            <input type="text" id="category" name="category" placeholder="Ej: Venta, Suministros, Salarios" required>
        </div>
        <div class="form-group">
            <label for="description">Descripción</label>
            <textarea id="description" name="description" required></textarea>
        </div>
        <div class="form-group">
            <label for="amount">Monto</label>
            <input type="number" id="amount" name="amount" step="0.01" min="0" required>
        </div>
        <button type="submit" class="btn btn-primary">Registrar</button>
    </form>
</div>

<div class="container mt-4">
    <h3>Historial de Transacciones</h3>
    <table class="table table-striped table-responsive">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Tipo</th>
                <th>Categoría</th>
                <th>Descripción</th>
                <th>Monto</th>
                <th>Usuario</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data['transactions'] as $transaction): ?>
                <tr>
                    <td><?php echo Helpers::escape(date('d/m/Y H:i', strtotime($transaction['transaction_date']))); ?></td>
                    <td class="transaction-type-<?php echo Helpers::escape($transaction['type']); ?>">
                        <?php echo Helpers::escape(ucfirst($transaction['type'])); ?>
                    </td>
                    <td><?php echo Helpers::escape($transaction['category']); ?></td>
                    <td><?php echo Helpers::escape($transaction['description']); ?></td>
                    <td>$<?php echo Helpers::escape(number_format($transaction['amount'], 2)); ?></td>
                    <td><?php echo Helpers::escape($transaction['user_name']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once TEMPLATES_PATH . '/footer.php'; ?>
<style>
    /* Estilos adicionales para la vista de caja */
    .financial-summary {
        display: flex;
        justify-content: space-around;
        gap: 1rem;
        margin-bottom: 2rem;
    }
    .summary-card {
        background: #fff;
        border-left: 5px solid;
        padding: 1rem;
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        flex: 1;
    }
    .summary-card.income { border-color: var(--success-color); }
    .summary-card.expenses { border-color: var(--danger-color); }
    .balance-amount {
        font-size: 1.5rem;
        font-weight: bold;
        color: var(--primary-color);
    }
    .transaction-type-ingreso { color: var(--success-color); font-weight: bold; }
    .transaction-type-egreso { color: var(--danger-color); font-weight: bold; }
    .mt-2 { margin-top: 2rem; }
    @media (max-width: 768px) {
        .financial-summary {
            flex-direction: column;
        }
    }
</style>
