<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.10.0
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 * @var \App\View\AppView $this
 */
use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Datasource\ConnectionManager;
use Cake\Error\Debugger;
use Cake\Http\Exception\NotFoundException;

$this->disableAutoLayout();

$checkConnection = function (string $name) {
    $error = null;
    $connected = false;
    try {
        $connection = ConnectionManager::get($name);
        $connected = $connection->connect();
    } catch (Exception $connectionError) {
        $error = $connectionError->getMessage();
        if (method_exists($connectionError, 'getAttributes')) {
            $attributes = $connectionError->getAttributes();
            if (isset($attributes['message'])) {
                $error .= '<br />' . $attributes['message'];
            }
        }
        if ($name === 'debug_kit') {
            $error = 'Try adding your current <b>top level domain</b> to the
                <a href="https://book.cakephp.org/debugkit/4/en/index.html#configuration" target="_blank">DebugKit.safeTld</a>
            config and reload.';
            if (!in_array('sqlite', \PDO::getAvailableDrivers())) {
                $error .= '<br />You need to install the PHP extension <code>pdo_sqlite</code> so DebugKit can work properly.';
            }
        }
    }

    return compact('connected', 'error');
};

if (!Configure::read('debug')) :
    throw new NotFoundException(
        'Please replace templates/Pages/home.php with your own version or re-enable debug mode.'
    );
endif;

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <?= $this->Html->charset() ?>
    <title>Bookmarker - Página Inicial</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <?= $this->Html->css(['normalize.min', 'milligram.min']) ?>
    <style>
        body {
            padding: 30px;
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        h1, h2 {
            color: #2c3e50;
        }
        .container {
            max-width: 700px;
            margin: auto;
            text-align: center;
        }
        ul {
            list-style: none;
            padding: 0;
            margin-top: 20px;
        }
        ul li {
            margin: 10px 0;
        }
        footer {
            margin-top: 50px;
            font-size: 0.9em;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📚 Bem-vindo ao Bookmarker</h1>
        <p>Gerencie e organize seus links favoritos com facilidade.</p>

        <h2>🔗 Acesso Rápido</h2>
        <ul>
            <li><?= $this->Html->link('Ver todos os bookmarks', ['controller' => 'Bookmarks', 'action' => 'index']) ?></li>
            <li><?= $this->Html->link('Adicionar novo bookmark', ['controller' => 'Bookmarks', 'action' => 'add']) ?></li>
            <li><?= $this->Html->link('Entrar (login)', ['controller' => 'Users', 'action' => 'login']) ?></li>
            <li><?= $this->Html->link('Criar conta', ['controller' => 'Users', 'action' => 'add']) ?></li>
        </ul>

        <footer>
            <p>Projeto desenvolvido por <strong>Ligia Menezes</strong> e <strong>Ronaldo</strong></p>
            <p>Curso: Desenvolvimento Web com CakePHP — Julho de 2025</p>
        </footer>
    </div>
</body>
</html>
