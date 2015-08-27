<?php 
$userMapper = new Modules\User\Mappers\User();
?>

<legend><?=$this->getTrans('menuUserList') ?></legend>
<div class="row">
    <?php foreach ($this->get('userList') as $userlist): ?>        
        <div class="col-lg-4">
            <div class="user">
                <img class="thumbnail" src="<?=$this->getStaticUrl().'../'.$this->escape($userlist->getAvatar()) ?>" alt="">
                <h3><a href="<?=$this->getUrl(array('module' => 'user', 'controller' => 'profil', 'action' => 'index', 'user' => $userlist->getId())) ?>" title="<?=$this->escape($userlist->getName()) ?>s <?=$this->getTrans('profile') ?>"><?=$this->escape($userlist->getName()) ?></a></h3>
                <div class="userInfo">
                    <i class="fa fa-sign-in" title="<?=$this->getTrans('regist') ?>"></i> <?=$this->escape($userlist->getDateCreated()) ?>
                </div>
                <div class="userLinks">
                    <?=$this->getTrans('contact'); ?>:
                    <br>
                    <a href="<?=$this->getUrl(array('module' => 'user', 'controller' => 'profil', 'action' => 'index', 'user' => $userlist->getId())) ?>" class="fa fa-user" title="<?=$this->escape($userlist->getName()) ?>s <?=$this->getTrans('profile') ?>"></a>
                    <?php if($this->getUser() and $this->getUser()->getId() != $this->escape($userlist->getID())): ?>
                        <a href="<?=$this->getUrl(array('module' => 'user', 'controller' => 'panel', 'action' => 'dialognew', 'id' => $userlist->getId())) ?>" class="fa fa-comment" title="<?=$this->getTrans('privateMessage') ?>"></a>
                     <?php endif; ?>
                    <a href="#" class="fa fa-envelope" title="E-Mail"></a>
                    <?php if($this->escape($userlist->getHomepage()) != ''): ?>
                        <a href="<?=$userMapper->getHomepage($this->escape($userlist->getHomepage())) ?>" class="fa fa-globe" title="<?=$this->getTrans('website') ?>"></a>
                    <?php endif; ?>
                    <a href="#" class="fa fa-skype" title="Skype"></a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
