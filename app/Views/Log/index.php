<?= $this->extend('Layout/template'); ?>

<?= $this->section('content'); ?>

<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="header-title">
                    <h4 class="card-title">System Logs</h4>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th width="5%">ID</th>
                                <th width="15%">User</th>
                                <th width="10%">Action</th>
                                <th width="10%">Module</th>
                                <th width="8%">Record ID</th>
                                <th width="35%">Message</th>
                                <th width="17%">Timestamp</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($logs)): ?>
                                <?php foreach ($logs as $log): ?>
                                    <tr>
                                        <td><?= esc($log['id']) ?></td>
                                        <td>
                                            <?php if ($log['user_id']): ?>
                                                <strong><?= esc($log['nama_lengkap'] ?? $log['username'] ?? 'Unknown') ?></strong>
                                                <br>
                                                <small class="text-muted"><?= esc($log['username'] ?? '-') ?></small>
                                            <?php else: ?>
                                                <span class="text-muted">System</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php
                                            $badgeClass = 'badge-secondary';
                                            switch ($log['action']) {
                                                case 'CREATE':
                                                    $badgeClass = 'badge-success';
                                                    break;
                                                case 'UPDATE':
                                                    $badgeClass = 'badge-info';
                                                    break;
                                                case 'DELETE':
                                                    $badgeClass = 'badge-danger';
                                                    break;
                                                case 'LOGIN':
                                                    $badgeClass = 'badge-primary';
                                                    break;
                                                case 'LOGOUT':
                                                    $badgeClass = 'badge-warning';
                                                    break;
                                                case 'ERROR':
                                                    $badgeClass = 'badge-danger';
                                                    break;
                                            }
                                            ?>
                                            <span class="badge <?= $badgeClass ?>"><?= esc($log['action']) ?></span>
                                        </td>
                                        <td>
                                            <span class="badge badge-light"><?= esc($log['module']) ?></span>
                                        </td>
                                        <td class="text-center">
                                            <?= $log['record_id'] ? esc($log['record_id']) : '<span class="text-muted">-</span>' ?>
                                        </td>
                                        <td>
                                            <small><?= esc($log['log_message'] ?? '-') ?></small>
                                        </td>
                                        <td>
                                            <small>
                                                <?php
                                                $timestamp = strtotime($log['created_at']);
                                                echo date('d M Y', $timestamp);
                                                ?>
                                                <br>
                                                <span class="text-muted"><?= date('H:i:s', $timestamp) ?></span>
                                            </small>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center">
                                        <div class="alert alert-info mb-0">
                                            <i class="ri-information-line"></i> No logs found
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>