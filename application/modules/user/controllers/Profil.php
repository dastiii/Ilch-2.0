<?php
/**
 * @copyright Ilch 2.0
 * @package ilch
 */

namespace Modules\User\Controllers;

use Modules\User\Mappers\User as UserMapper;
use Modules\User\Mappers\Gallery as GalleryMapper;

class Profil extends \Ilch\Controller\Frontend
{
    public function indexAction()
    {
        $userMapper = new UserMapper();
        $galleryMapper = new GalleryMapper();

        $profil = $userMapper->getUserById($this->getRequest()->getParam('user'));

        if ($profil) {
            $this->getLayout()->getHmenu()
                    ->add($this->getTranslator()->trans('menuUserList'), ['controller' => 'index'])
                    ->add($profil->getName(), ['action' => 'index', 'user' => $this->getRequest()->getParam('user')]);

            $this->getView()->set('userMapper', $userMapper);
            $this->getView()->set('profil', $profil);
            $this->getView()->set('galleryAllowed', $this->getConfig()->get('usergallery_allowed'));
            $this->getView()->set('gallery', $galleryMapper->getCountGalleryByUser($this->getRequest()->getParam('user')));
        } else {
            $this->redirect(['module' => 'error', 'controller' => 'index', 'action' => 'index', 'error' => 'User', 'errorText' => 'notFound']);
        }
    }
}
