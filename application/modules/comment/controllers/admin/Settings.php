<?php
/**
 * @copyright Ilch 2.0
 * @package ilch
 */

namespace Modules\Comment\Controllers\Admin;

use Ilch\Validation;

class Settings extends \Ilch\Controller\Admin
{
    public function init()
    {
        $items = [
            [
                'name' => 'manage',
                'active' => false,
                'icon' => 'fa fa-th-list',
                'url' => $this->getLayout()->getUrl(['controller' => 'index', 'action' => 'index'])
            ],
            [
                'name' => 'settings',
                'active' => true,
                'icon' => 'fa fa-cogs',
                'url' => $this->getLayout()->getUrl(['controller' => 'settings', 'action' => 'index'])
            ]
        ];

        $this->getLayout()->addMenu
        (
            'menuComments',
            $items
        );
    }
    
    public function indexAction() 
    {
        $this->getLayout()->getAdminHmenu()
                ->add($this->getTranslator()->trans('menuComments'), ['controller' => 'index', 'action' => 'index'])
                ->add($this->getTranslator()->trans('settings'), ['action' => 'index']);

        $post = [
            'reply' => '',
            'nesting' => '',
            'check_avatar' => '',
            'check_date' => ''
        ];

        if ($this->getRequest()->isPost()) {
            $post = [
                'reply' => $this->getRequest()->getPost('reply'),
                'nesting' => $this->getRequest()->getPost('nesting'),
                'check_avatar' => $this->getRequest()->getPost('check_avatar'),
                'check_date' => $this->getRequest()->getPost('check_date')
            ];

            Validation::setCustomFieldAliases([
                'reply' => 'acceptReply',
                'check_avatar' => 'showAvatar',
                'check_date' => 'showDateTime'
            ]);

            $validation = Validation::create($post, [
                'reply' => 'required|numeric|integer|min:0|max:1',
                'nesting' => 'required|numeric|integer|min:0',
                'check_avatar' => 'required|numeric|integer|min:0|max:1',
                'check_date' => 'required|numeric|integer|min:0|max:1'
            ]);

            if ($validation->isValid()) {
                $this->getConfig()->set('comment_reply', $post['reply']);
                $this->getConfig()->set('comment_nesting', $post['nesting']);
                $this->getConfig()->set('comment_avatar', $post['check_avatar']);
                $this->getConfig()->set('comment_date', $post['check_date']);
                $this->addMessage('saveSuccess');
            }

            $this->getView()->set('errors', $validation->getErrorBag()->getErrorMessages());
            $errorFields = $validation->getFieldsWithError();
        }

        $this->getView()->set('errorFields', (isset($errorFields) ? $errorFields : []));
        $this->getView()->set('comment_reply', $this->getConfig()->get('comment_reply'));
        $this->getView()->set('comment_nesting', $this->getConfig()->get('comment_nesting'));
        $this->getView()->set('comment_avatar', $this->getConfig()->get('comment_avatar'));
        $this->getView()->set('comment_date', $this->getConfig()->get('comment_date'));
    }
}
