<?php
/**
 * Sistema de Autenticação Centralizado
 * Gerencia sessões e redirecionamentos baseados no tipo de usuário
 */

// Iniciar sessão se não estiver iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Definir constantes para tipos de usuário
define('USER_TYPE_ADMIN', 'admin');
define('USER_TYPE_USER', 'usuario');

// Definir constantes para status de login
define('LOGIN_STATUS_LOGGED_IN', true);
define('LOGIN_STATUS_LOGGED_OUT', false);

/**
 * Verifica se o usuário está logado
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['login']) && $_SESSION['login'] === LOGIN_STATUS_LOGGED_IN;
}

/**
 * Verifica se o usuário é administrador
 * @return bool
 */
function isAdmin() {
    return isLoggedIn() && isset($_SESSION['admin']) && $_SESSION['admin'] === true;
}

/**
 * Verifica se o usuário é usuário comum
 * @return bool
 */
function isUser() {
    return isLoggedIn() && isset($_SESSION['user']) && $_SESSION['user'] === true;
}

/**
 * Obtém o tipo de usuário atual
 * @return string|null
 */
function getUserType() {
    if (isAdmin()) {
        return USER_TYPE_ADMIN;
    } elseif (isUser()) {
        return USER_TYPE_USER;
    }
    return null;
}

/**
 * Obtém dados do usuário logado
 * @return array|null
 */
function getCurrentUser() {
    if (isLoggedIn()) {
        return [
            'id' => $_SESSION['id'] ?? null,
            'nome' => $_SESSION['nome'] ?? null,
            'email' => $_SESSION['email'] ?? null,
            'tipo' => $_SESSION['tipo'] ?? null
        ];
    }
    return null;
}

/**
 * Define sessão de login para administrador
 * @param array $userData Dados do usuário
 */
function setAdminSession($userData) {
    $_SESSION['login'] = LOGIN_STATUS_LOGGED_IN;
    $_SESSION['admin'] = true;
    $_SESSION['user'] = false;
    $_SESSION['id'] = $userData['id'];
    $_SESSION['nome'] = $userData['nome'];
    $_SESSION['email'] = $userData['email'];
    $_SESSION['tipo'] = $userData['tipo'];
    $_SESSION['usuariologado'] = $userData;
}

/**
 * Define sessão de login para usuário comum
 * @param array $userData Dados do usuário
 */
function setUserSession($userData) {
    $_SESSION['login'] = LOGIN_STATUS_LOGGED_IN;
    $_SESSION['admin'] = false;
    $_SESSION['user'] = true;
    $_SESSION['id'] = $userData['id'];
    $_SESSION['nome'] = $userData['nome'];
    $_SESSION['email'] = $userData['email'];
    $_SESSION['tipo'] = $userData['tipo'];
    $_SESSION['usuariologado'] = $userData;
}

/**
 * Limpa a sessão do usuário
 */
function clearSession() {
    $_SESSION['login'] = LOGIN_STATUS_LOGGED_OUT;
    $_SESSION['admin'] = false;
    $_SESSION['user'] = false;
    unset($_SESSION['id'], $_SESSION['nome'], $_SESSION['email'], $_SESSION['tipo'], $_SESSION['usuariologado']);
}

/**
 * Redireciona usuário baseado no tipo e página atual
 * @param string $currentPage Página atual (opcional)
 */
function redirectBasedOnUserType($currentPage = '') {
    if (!isLoggedIn()) {
        // Usuário não logado - redirecionar para login
        header('Location: ../view/index.php');
        exit;
    }
    
    $userType = getUserType();
    $currentPage = strtolower($currentPage);
    
    // Se estiver na página de login e já estiver logado, redirecionar
    if (strpos($currentPage, 'index.php') !== false) {
        if (isAdmin()) {
            header('Location: ../view/painel/Admin/dashboard_Admin.php');
        } elseif (isUser()) {
            header('Location: ../view/painel/Usuario/dashboard_usuario.php');
        }
        exit;
    }
    
    // Verificar acesso a páginas de admin
    if (strpos($currentPage, 'admin') !== false || strpos($currentPage, 'painel/admin') !== false) {
        if (!isAdmin()) {
            // Usuário comum tentando acessar área de admin
            header('Location: ../view/painel/Usuario/dashboard_usuario.php');
            exit;
        }
    }
    
    // Verificar acesso a páginas de usuário
    if (strpos($currentPage, 'usuario') !== false || strpos($currentPage, 'painel/usuario') !== false) {
        if (!isUser()) {
            // Admin tentando acessar área de usuário (opcional - pode permitir)
            // header('Location: ../view/painel/Admin/dashboard_Admin.php');
            // exit;
        }
    }
}

/**
 * Requer login - função para usar no início de páginas protegidas
 * @param string $requiredType Tipo de usuário necessário ('admin', 'user', ou null para qualquer)
 * @param string $currentPage Página atual
 */
function requireLogin($requiredType = null, $currentPage = '') {
    if (!isLoggedIn()) {
        header('Location: ../../index.php');
        exit;
    }
    
    if ($requiredType === 'admin' && !isAdmin()) {
        header('Location: ../Usuario/dashboard_usuario.php');
        exit;
    }
    
    if ($requiredType === 'user' && !isUser()) {
        header('Location: ../Admin/dashboard_Admin.php');
        exit;
    }
    
    // Redirecionar baseado no tipo de usuário se necessário
    redirectBasedOnUserType($currentPage);
}

/**
 * Verifica se o usuário tem permissão para acessar uma página
 * @param string $page Página a ser verificada
 * @return bool
 */
function hasPermission($page) {
    if (!isLoggedIn()) {
        return false;
    }
    
    $page = strtolower($page);
    
    // Páginas que requerem admin
    $adminPages = ['admin', 'itens_cadastro', 'solicitacoes', 'relatorios'];
    
    foreach ($adminPages as $adminPage) {
        if (strpos($page, $adminPage) !== false) {
            return isAdmin();
        }
    }
    
    // Páginas que qualquer usuário logado pode acessar
    return true;
}

/**
 * Logout do usuário
 */
function logout() {
    clearSession();
    session_destroy();
    header('Location: ../view/index.php');
    exit;
}

/**
 * Exibe mensagens de erro/sucesso da sessão
 */
function displaySessionMessages() {
    if (isset($_SESSION['erro'])) {
        echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">';
        echo htmlspecialchars($_SESSION['erro']);
        echo '</div>';
        unset($_SESSION['erro']);
    }
    
    if (isset($_SESSION['mensagem_sucesso'])) {
        echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">';
        echo htmlspecialchars($_SESSION['mensagem_sucesso']);
        echo '</div>';
        unset($_SESSION['mensagem_sucesso']);
    }
    
    if (isset($_SESSION['erro_login'])) {
        echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">';
        echo htmlspecialchars($_SESSION['erro_login']);
        echo '</div>';
        unset($_SESSION['erro_login']);
    }
}
?>
