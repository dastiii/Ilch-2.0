<?php
$comments = $this->get('comments');
$article = $this->get('article');
$commentMapper = new \Modules\Comment\Mappers\Comment();
$content = str_replace('[PREVIEWSTOP]', '', $article->getContent());
$image = $article->getArticleImage();
$imageSource = $article->getArticleImageSource();
$preview = $this->getRequest()->getParam('preview');

function rec($id, $uid, $req, $obj)
{
    $CommentMappers = new \Modules\Comment\Mappers\Comment();
    $userMapper = new \Modules\User\Mappers\User();
    $fk_comments = $CommentMappers->getCommentsByFKId($id);
    $user_rep = $userMapper->getUserById($uid);
    $translator = new \Ilch\Translator();
    $translator->load(APPLICATION_PATH.'/modules/article/translations/');

    foreach ($fk_comments as $fk_comment) {
        $commentDate = new \Ilch\Date($fk_comment->getDateCreated());
        $user = $userMapper->getUserById($fk_comment->getUserId());
        $config = \Ilch\Registry::get('config');

        if ($req > $config->get('comment_interleaving')) {
            $req = $config->get('comment_interleaving');
        }

        $col = 10 - $req;
        echo '<article class="row" id="'.$fk_comment->getId().'">';
        echo '<div class="col-md-2 col-sm-2 col-md-offset-'.$req.' col-sm-offset-'.$req.' hidden-xs"><figure class="thumbnail">';
        echo '<a href="'.$obj->getUrl(array('module' => 'user', 'controller' => 'profil', 'action' => 'index', 'user' => $user->getId())).'"><img class="img-responsive" src="'.$obj->getBaseUrl($user->getAvatar()).'" alt="'.$user->getName().'"></a>';
        echo '</figure></div><div class="col-md-'.$col.' col-sm-'.$col.'"><div class="panel panel-default arrow left"><div class="panel-bodylist"><div class="panel-heading right">'.$user_rep->getName().' <i class="fa fa-reply"></i> '.$translator->trans('reply').'</div><header class="text-left">';
        echo '<div class="comment-user"><i class="fa fa-user"></i> <a href="'.$obj->getUrl(array('module' => 'user', 'controller' => 'profil', 'action' => 'index', 'user' => $fk_comment->getUserId())).'">'.$user->getName().'</a></div>';
        echo '<time class="comment-date"><i class="fa fa-clock-o"></i> '.$commentDate->format("d.m.Y - H:i", true).'</time>';
        echo '</header><div class="comment-post"><p>'.nl2br($fk_comment->getText()).'</p></div>';
        echo '<p class="text-right"><a href="'.$obj->getUrl(array('module' => 'comment', 'controller' => 'index', 'action' => 'index', 'id' => $fk_comment->getId())).'" class="btn btn-default btn-sm"><i class="fa fa-reply"></i> '.$translator->trans('reply').'</a></p>';
        echo '</div></div></div>';
        echo '</article>';

        $fkk_comments = $CommentMappers->getCommentsByFKId($fk_comment->getId());

        if (count($fkk_comments) > 0) {
            $req++;
        }
        $i=1;

        foreach ($fkk_comments as $fkk_comment) {
            if ($i == 1) {
                rec($fk_comment->getId(), $fk_comment->getUserId(), $req, $obj);
                $i++;
            }
        }

        if (count($fkk_comments) > 0) {
            $req--;
        }
    }
}
?>

<legend><?=$article->getTitle() ?></legend>
<?php if (!empty($image)): ?>
    <figure>
        <img class="article_image" src="<?=$this->getBaseUrl($image) ?>" />
        <?php if (!empty($imageSource)): ?>
            <figcaption class="article_image_source"><?=$this->getTrans('articleImageSource') ?>: <?=$imageSource ?></figcaption>
            <br />
        <?php endif; ?>
    <figure>
<?php endif; ?>

<?=$content ?>
<hr />
<?php if ($article->getAuthorId() != ''): ?>
    <?php $userMapper = new \Modules\User\Mappers\User(); ?>
    <?php $user = $userMapper->getUserById($article->getAuthorId()); ?>
    <?php if ($user != ''): ?>
        <?=$this->getTrans('author') ?>: <a href="<?=$this->getUrl('user/profil/index/user/'.$user->getId()) ?>"><?=$this->escape($user->getName()) ?></a>
    <?php endif; ?>
<?php endif; ?>

<link href="<?= $this->getModuleUrl('../comment/static/css/comment.css') ?>" rel="stylesheet">
<?php if(empty($preview)): ?>
    <?php $userMapper = new \Modules\User\Mappers\User(); ?>
    <?php $nowDate = new \Ilch\Date(); ?>
    <div class="row">
        <div class="col-md-12">
            <h3 class="page-header" id="comment"><?=$this->getTrans('comments') ?> (<?=count($comments) ?>)</h3>
            <?php if($this->getUser()): ?>
                <form action="" class="form-horizontal" method="POST">
                    <?=$this->getTokenField() ?>
                    <section class="comment-list">
                        <article class="row">
                            <div class="col-md-2 col-sm-2 hidden-xs">
                                <figure class="thumbnail">
                                    <a href="<?=$this->getUrl('user/profil/index/user/'.$this->getUser()->getId()) ?>"><img class="img-responsive" src="<?=$this->getUrl().'/'.$this->getUser()->getAvatar() ?>" alt="<?=$this->getUser()->getName() ?>"></a>
                                </figure>
                            </div>
                            <div class="col-md-10 col-sm-10">
                                <div class="panel panel-default arrow left">
                                    <div class="panel-body">
                                        <header class="text-left">
                                            <div class="comment-user"><i class="fa fa-user"></i> <a href="<?=$this->getUrl(array('module' => 'user', 'controller' => 'profil', 'action' => 'index', 'user' => $this->getUser()->getId())) ?>"><?=$this->getUser()->getName() ?></a></div>
                                            <time class="comment-date"><i class="fa fa-clock-o"></i> <?=$nowDate->format("d.m.Y - H:i", true) ?></time>
                                        </header>
                                        <div class="comment-post">
                                            <p>
                                                <textarea class="form-control"
                                                          accesskey=""
                                                          name="article_comment_text"
                                                          required></textarea>
                                            </p>
                                        </div>
                                        <p class="text-right submit">
                                            <input type="submit"
                                                   name="saveEntry"
                                                   class="btn" 
                                                   value="<?=$this->getTrans('submit') ?>" />
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </article>
                    </section>
                </form>
            <?php endif; ?>
            <?php foreach ($comments as $comment): ?>
                <?php $user = $userMapper->getUserById($comment->getUserId()); ?>
                <?php $commentDate = new \Ilch\Date($comment->getDateCreated()); ?>
                <section class="comment-list">
                    <article class="row" id="<?=$comment->getId() ?>">
                        <div class="col-md-2 col-sm-2 hidden-xs">
                            <figure class="thumbnail">
                                <a href="<?=$this->getUrl('user/profil/index/user/'.$user->getId()) ?>"><img class="img-responsive" src="<?=$this->getUrl().'/'.$user->getAvatar() ?>" alt="<?=$this->escape($user->getName()) ?>"></a>
                            </figure>
                        </div>
                        <div class="col-md-10 col-sm-10">
                            <div class="panel panel-default arrow left">
                                <div class="panel-bodylist">
                                    <header class="text-left">
                                        <div class="comment-user"><i class="fa fa-user"></i> 
                                            <a href="<?=$this->getUrl(array('module' => 'user', 'controller' => 'profil', 'action' => 'index', 'user' => $user->getId())) ?>"><?=$this->escape($user->getName()) ?></a>
                                        </div>
                                        <time class="comment-date"><i class="fa fa-clock-o"></i> <?=$commentDate->format("d.m.Y - H:i", true) ?></time>
                                    </header>
                                    <div class="comment-post"><p><?=nl2br($this->escape($comment->getText())) ?></p></div>
                                    <p class="text-right"><a href="<?=$this->getUrl(array('module' => 'comment', 'action' => 'index', 'id' => $comment->getId(), 'id_a' => $article->getId())) ?>" class="btn btn-default btn-sm"><i class="fa fa-reply"></i> <?=$this->getTrans('reply') ?></a></p>
                                </div>
                            </div>
                        </div>
                    </article>
                    <?php rec($comment->getId(), $comment->getUserId(), 1, $this) ?>
                </section>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>
