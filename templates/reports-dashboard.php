<?php require_once TEMPLATES_PATH . '/header.php'; ?>
<div class="page-header">
    <h1>Dashboard de Reportes y KPIs</h1>
</div>

<div class="reports-grid">
    <div class="card financial-summary">
        <h2>Resumen Financiero</h2>
        <p>Ingresos Totales: <span class="income">$<?php echo Helpers::escape(number_format($data['totalIncome'], 2)); ?></span></p>
        <p>Egresos Totales: <span class="expenses">$<?php echo Helpers::escape(number_format($data['totalExpenses'], 2)); ?></span></p>
        <p class="big-number">Balance Neto: <span class="balance">$<?php echo Helpers::escape(number_format($data['netBalance'], 2)); ?></span></p>
    </div>

    <div class="card channel-report">
        <h2>Ventas por Canal (Total: $<?php echo Helpers::escape(number_format($data['totalSales'], 2)); ?>)</h2>
        <table>
            <thead>
                <tr>
                    <th>Canal</th>
                    <th>Ventas ($)</th>
                    <th>% del Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data['salesByChannel'] as $channel): ?>
                <tr>
                    <td><?php echo Helpers::escape($channel['channel_name']); ?></td>
                    <td>$<?php echo Helpers::escape(number_format($channel['total_sales'], 2)); ?></td>
                    <td>
                        <?php 
                        $percentage = ($data['totalSales'] > 0) ? ($channel['total_sales'] / $data['totalSales']) * 100 : 0;
                        echo Helpers::escape(number_format($percentage, 2)) . '%';
                        ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <div class="card user-report">
        <h2>Ventas por Vendedor</h2>
        <ul>
            <?php foreach ($data['salesByUser'] as $user): ?>
            <li>
                <strong><?php echo Helpers::escape($user['user_name']); ?>:</strong> 
                $<?php echo Helpers::escape(number_format($user['total_sales'], 2)); ?>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<?php require_once TEMPLATES_PATH . '/footer.php'; ?>

<style>
/* Estilos Específicos para Reportes */
.reports-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
    margin-top: 1rem;
}
.card {
    background: #fff;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.05);
}
.card h2 {
    color: var(--primary-color);
    margin-bottom: 1rem;
    font-size: 1.4rem;
}
.financial-summary .income { color: var(--success-color); font-weight: bold; }
.financial-summary .expenses { color: var(--danger-color); font-weight: bold; }
.financial-summary .big-number {
    font-size: 2rem;
    margin-top: 1rem;
    border-top: 1px solid var(--border-color);
    padding-top: 1rem;
}
.channel-report table {
    width: 100%;
    border-collapse: collapse;
}
.channel-report th, .channel-report td {
    padding: 0.75rem 0.5rem;
    border-bottom: 1px solid var(--border-color);
    text-align: left;
}
.user-report ul {
    list-style: none;
    padding: 0;
}
.user-report li {
    padding: 0.5rem 0;
    border-bottom: 1px dotted var(--border-color);
}
@media (max-width: 768px) {
    .reports-grid {
        grid-template-columns: 1fr;
    }
}
</style>
