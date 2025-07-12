<h1>Cadastro de Usuário</h1>

<?= $this->Form->create($user) ?>
<?= $this->Form->control('email') ?>
<?= $this->Form->control('password') ?>
<?= $this->Form->button('Cadastrar') ?>
<?= $this->Form->end() ?>
