<?php 
namespace App\Controller;

class TagsController extends AppController
{
    public function index()
    {
        $this->Authorization->skipAuthorization();

        $tags = $this->paginate($this->Tags);
        $this->set(compact('tags'));
    }
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->Authorization->skipAuthorization();
    }

    public function view($id = null)
    {
        $this->Authorization->skipAuthorization();

        $tag = $this->Tags->get($id, [
            'contain' => ['Bookmarks'],
        ]);

        $this->set(compact('tag'));
    }

    public function add()
    {
        $this->Authorization->skipAuthorization();

        $tag = $this->Tags->newEmptyEntity();
        if ($this->request->is('post')) {
            $tag = $this->Tags->patchEntity($tag, $this->request->getData());
            if ($this->Tags->save($tag)) {
                $this->Flash->success(__('The tag has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The tag could not be saved. Please, try again.'));
        }
        $bookmarks = $this->Tags->Bookmarks->find('list', ['limit' => 200])->all();
        $this->set(compact('tag', 'bookmarks'));
    }

    public function edit($id = null)
    {
        $this->Authorization->skipAuthorization();

        $tag = $this->Tags->get($id, [
            'contain' => ['Bookmarks'],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $tag = $this->Tags->patchEntity($tag, $this->request->getData());
            if ($this->Tags->save($tag)) {
                $this->Flash->success(__('The tag has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The tag could not be saved. Please, try again.'));
        }
        $bookmarks = $this->Tags->Bookmarks->find('list', ['limit' => 200])->all();
        $this->set(compact('tag', 'bookmarks'));
    }

    public function delete($id = null)
    {
        $this->Authorization->skipAuthorization();

        $this->request->allowMethod(['post', 'delete']);
        $tag = $this->Tags->get($id);
        if ($this->Tags->delete($tag)) {
            $this->Flash->success(__('The tag has been deleted.'));
        } else {
            $this->Flash->error(__('The tag could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
?>
