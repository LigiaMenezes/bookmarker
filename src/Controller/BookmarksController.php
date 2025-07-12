<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Event\EventInterface;

/**
 * Bookmarks Controller
 *
 * @property \App\Model\Table\BookmarksTable $Bookmarks
 */
class BookmarksController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        // Garante que o usuário precisa estar logado para qualquer ação
        $this->Authorization->skipAuthorization(); // caso esteja usando Authorization plugin, remova se não usar
    }

    // Método para autorização - libera ações básicas para usuários e restringe edição/visualização a donos
    public function isAuthorized($user)
    {
        $action = $this->request->getParam('action');

        // Permitir index, add e tags para todos os usuários logados
        if (in_array($action, ['index', 'add', 'tags'])) {
            return true;
        }

        // Para outras ações, requer id do bookmark
        $id = $this->request->getParam('pass.0');
        if (!$id) {
            return false;
        }

        $bookmark = $this->Bookmarks->get($id);

        // Somente permite se o bookmark pertencer ao usuário logado
        return $bookmark->user_id === $user['id'];
    }

    public function index()
    {
        $this->paginate = [
            'contain' => ['Tags'], // se quiser mostrar tags junto
            'conditions' => ['Bookmarks.user_id' => $this->Auth->user('id')]
        ];
        $bookmarks = $this->paginate($this->Bookmarks);

        $this->set(compact('bookmarks'));
    }

    public function view($id = null)
    {
        $bookmark = $this->Bookmarks->get($id, [
            'contain' => ['Tags'],
        ]);

        $this->set(compact('bookmark'));
    }

    public function add()
    {
        $bookmark = $this->Bookmarks->newEmptyEntity();
        if ($this->request->is('post')) {
            $bookmark = $this->Bookmarks->patchEntity($bookmark, $this->request->getData());
            $bookmark->user_id = $this->Auth->user('id'); // força o user logado como dono
            if ($this->Bookmarks->save($bookmark)) {
                $this->Flash->success(__('O bookmark foi salvo com sucesso.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Não foi possível salvar o bookmark. Tente novamente.'));
        }
        $tags = $this->Bookmarks->Tags->find('list');
        $this->set(compact('bookmark', 'tags'));
    }

    public function edit($id = null)
    {
        $bookmark = $this->Bookmarks->get($id, [
            'contain' => ['Tags'],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $bookmark = $this->Bookmarks->patchEntity($bookmark, $this->request->getData());
            $bookmark->user_id = $this->Auth->user('id'); // mantém o dono correto
            if ($this->Bookmarks->save($bookmark)) {
                $this->Flash->success(__('O bookmark foi atualizado.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Não foi possível atualizar o bookmark. Tente novamente.'));
        }
        $tags = $this->Bookmarks->Tags->find('list');
        $this->set(compact('bookmark', 'tags'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $bookmark = $this->Bookmarks->get($id);
        if ($this->Bookmarks->delete($bookmark)) {
            $this->Flash->success(__('O bookmark foi excluído.'));
        } else {
            $this->Flash->error(__('Não foi possível excluir o bookmark. Tente novamente.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function tags(...$tags)
    {
        $tags = $this->request->getParam('pass');
        if (empty($tags)) {
            $this->Flash->error('Nenhuma tag especificada.');
            return $this->redirect(['action' => 'index']);
        }

        $bookmarks = $this->Bookmarks->find('tagged', [
            'tags' => $tags,
            'conditions' => ['Bookmarks.user_id' => $this->Auth->user('id')] // limita ao usuário logado
        ]);

        $this->set(compact('bookmarks', 'tags'));
    }
}
