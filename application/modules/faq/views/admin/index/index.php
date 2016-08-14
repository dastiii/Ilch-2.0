<?php $categoryMapper = $this->get('categoryMapper'); ?>

<legend><?=$this->getTrans('manage') ?></legend>
<?php if ($this->get('faqs') != ''): ?>
    <form class="form-horizontal" method="POST" action="">
        <?=$this->getTokenField() ?>
        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <colgroup>
                    <col class="icon_width">
                    <col class="icon_width">
                    <col class="icon_width">
                    <col class="col-lg-2">
                    <col>
                </colgroup>
                <thead>
                    <tr>
                        <th><?=$this->getCheckAllCheckbox('check_faqs') ?></th>
                        <th></th>
                        <th></th>
                        <th><?=$this->getTrans('cat') ?></th>
                        <th><?=$this->getTrans('question') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($this->get('faqs') as $faq): ?>
                        <?php $faqsCats = $categoryMapper->getCategoryById($faq->getCatId()); ?>
                        <tr>
                            <td><input type="checkbox" name="check_faqs[]" value="<?=$faq->getId() ?>" /></td>
                            <td><?=$this->getEditIcon(['action' => 'treat', 'id' => $faq->getId()]) ?></td>
                            <td><?=$this->getDeleteIcon(['action' => 'delfaq', 'id' => $faq->getId()]) ?></td>
                            <td><?=$this->escape($faqsCats->getTitle()) ?></td>
                            <td><?=$this->escape($faq->getQuestion()) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?=$this->getListBar(['delete' => 'delete']) ?>
    </form>
<?php else: ?>
    <?=$this->getTrans('noFaqs') ?>
<?php endif; ?>
