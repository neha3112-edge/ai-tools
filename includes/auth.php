<?php
// ============================================================
// includes/auth.php
// ============================================================

function is_logged_in(): bool
{
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

function require_login(): void
{
    if (!is_logged_in()) {
        header('Location: ' . ADMIN_URL . '/login.php');
        exit;
    }
}

function resolve_user_permissions(): void
{
    if (!is_logged_in()) {
        return;
    }

    global $pdo;
    if (!isset($pdo)) {
        require_once __DIR__ . '/db.php';
    }

    try {
        $stmt = $pdo->prepare("SELECT is_superadmin, role_id, team_id FROM admins WHERE id = ? LIMIT 1");
        $stmt->execute([$_SESSION['admin_id']]);
        $user = $stmt->fetch();
        if ($user) {
            $_SESSION['is_superadmin'] = (int)$user['is_superadmin'];
            $_SESSION['admin_team_id'] = $user['team_id'] ? (int)$user['team_id'] : null;
            $_SESSION['admin_role_id'] = $user['role_id'] ? (int)$user['role_id'] : null;

            if ($user['is_superadmin']) {
                $_SESSION['admin_role'] = 'Superadmin';
            } else {
                $_SESSION['admin_role'] = 'No Role';
            }

            $modules = [];
            $actions = [];

            if ($user['role_id']) {
                $roleStmt = $pdo->prepare("SELECT name, module_access, team_permissions FROM roles WHERE id = ? LIMIT 1");
                $roleStmt->execute([$user['role_id']]);
                $role = $roleStmt->fetch();
                if ($role) {
                    $_SESSION['admin_role'] = $role['name'];
                    $modules = json_decode($role['module_access'], true) ?: [];
                    $team_perms = json_decode($role['team_permissions'], true) ?: [];
                    $user_team_id = $user['team_id'];
                    if ($user_team_id && isset($team_perms[$user_team_id])) {
                        $actions = $team_perms[$user_team_id] ?: [];
                    }
                }
            }

            $_SESSION['admin_module_access'] = $modules;
            $_SESSION['admin_team_actions'] = $actions;
        } else {
            $_SESSION['is_superadmin'] = 0;
            $_SESSION['admin_module_access'] = [];
            $_SESSION['admin_team_actions'] = [];
        }
    } catch (Exception $e) {
        $_SESSION['is_superadmin'] = 0;
        $_SESSION['admin_module_access'] = [];
        $_SESSION['admin_team_actions'] = [];
    }
}

function get_current_user_permissions(): array
{
    if (!is_logged_in()) {
        return [];
    }
    if (!isset($_SESSION['admin_team_actions'])) {
        resolve_user_permissions();
    }
    return $_SESSION['admin_team_actions'] ?? [];
}

function has_module_access(string $module): bool
{
    if (!is_logged_in()) {
        return false;
    }
    if (!isset($_SESSION['admin_module_access'])) {
        resolve_user_permissions();
    }
    if (!empty($_SESSION['is_superadmin'])) {
        return true;
    }
    $modules = $_SESSION['admin_module_access'] ?? [];
    return in_array(strtolower($module), array_map('strtolower', $modules), true);
}

function require_module_access(string $module): void
{
    require_login();
    if (!has_module_access($module)) {
        header('Location: ' . ADMIN_URL . '/rbac/access-denied.php');
        exit;
    }
}

function has_team_action(string $action): bool
{
    if (!is_logged_in()) {
        return false;
    }
    if (!isset($_SESSION['admin_team_actions'])) {
        resolve_user_permissions();
    }
    if (!empty($_SESSION['is_superadmin'])) {
        return true;
    }
    $actions = $_SESSION['admin_team_actions'] ?? [];
    $actions = array_map('strtolower', $actions);
    $action = strtolower($action);

    if (in_array($action, $actions, true)) {
        return true;
    }

    if (($action === 'create' || $action === 'update') && in_array('write', $actions, true)) {
        return true;
    }

    return false;
}

function require_team_action(string $action): void
{
    require_login();
    if (!has_team_action($action)) {
        header('Location: ' . ADMIN_URL . '/rbac/access-denied.php');
        exit;
    }
}

function has_permission(string $permission): bool
{
    if (!is_logged_in()) {
        return false;
    }
    if (strpos($permission, '.') !== false) {
        list($module, $action) = explode('.', $permission, 2);
        
        // Dashboard does not require team actions
        if (strtolower($module) === 'dashboard') {
            return has_module_access('dashboard');
        }

        if ($action === 'view') $action = 'Read';
        elseif ($action === 'add') $action = 'Create';
        elseif ($action === 'edit') $action = 'Update';
        elseif ($action === 'delete') $action = 'Delete';
        elseif ($action === 'export') $action = 'Read';
        
        return has_module_access($module) && has_team_action($action);
    }
    return false;
}

function require_permission(string $permission): void
{
    require_login();
    if (!has_permission($permission)) {
        header('Location: ' . ADMIN_URL . '/rbac/access-denied.php');
        exit;
    }
}

function require_superadmin(): void
{
    require_login();
    if (empty($_SESSION['is_superadmin'])) {
        header('Location: ' . ADMIN_URL . '/rbac/access-denied.php');
        exit;
    }
}
