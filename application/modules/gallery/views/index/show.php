<?php $commentMapper = new \Modules\Comment\Mappers\Comment(); ?>
<link href="https://unpkg.com/tailwindcss@^1.0/dist/tailwind.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.3.5/dist/alpine.min.js" defer></script>

<div x-data="{ baseUrl: '<?= $this->getUrl() ?>', images: <?= $this->escape($this->get('images')) ?>, currentImage: 0, slideShowActive: false }" class="mb-8 mx-2">
  <div class="flex justify-between mb-6 items-center">
      <h2 class="h2"><?= $this->get('gallery')->getTitle() ?></h2>
      <button @click="slideShowActive = !slideShowActive" class="px-3 py-2 rounded bg-gray-200">
        <div x-show="slideShowActive">
          <i class="fa fa-times fa-xs"></i>
          Slideshow beenden
        </div>
        <div x-show="! slideShowActive">
          <i class="fa fa-play fa-xs"></i>
          Slideshow starten
        </div>
      </button>
  </div>
  <template x-if="slideShowActive">
    <div class="relative"
       x-transition:enter="transition ease-out duration-300"
       x-transition:enter-start="opacity-0 transform scale-90"
       x-transition:enter-end="opacity-100 transform scale-100"
       x-transition:leave="transition ease-in duration-300"
       x-transition:leave-start="opacity-100 transform scale-100"
       x-transition:leave-end="opacity-0 transform scale-90"
    >
      <div class="absolute right-0 top-0 mt-8 mr-4 z-10">
        <a :href="baseUrl + images[currentImage]" target="_blank" title="in neuem Fenster öffnen" class="hover:bg-white hover:bg-opacity-50 p-4 hover:text-black text-white rounded-full">
          <i class="fa fa-external-link fa-fw"></i>
        </a>
      </div>
      <div
        class="h-full w-full flex flex-col justify-around absolute"
      >
        <div
           class="w-full flex items-center"
           :class="{
             'justify-between': currentImage !== images.length - 1 && currentImage !== 0,
             'justify-end': currentImage === 0,
             'justify-start': currentImage === images.length - 1
           }"
        >
          <button @click="currentImage === 0 ? '' : currentImage--" x-show="currentImage > 0" class="hover:bg-white hover:bg-opacity-50 p-4 ml-2 hover:text-black text-white rounded-full">
            <i class="fa fa-angle-left fa-fw fa-lg"></i>
          </button>
          <button @click="currentImage === images.length - 1 ? '' : currentImage++" x-show="currentImage < images.length - 1" class="hover:bg-white hover:bg-opacity-50 p-4 mr-2 hover:text-black text-white rounded-full">
            <i class="fa fa-angle-right fa-fw fa-lg"></i>
          </button>
        </div>
      </div>
      <img :src="baseUrl + images[currentImage]" alt="" class="rounded-lg">
    </div>
  </template>
</div>
<div class="flex flex-wrap w-full items-stretch">
    <?php foreach ($this->get('image') as $image): ?>
        <?php $commentsCount = $commentMapper->getCountComments('gallery/index/showimage/id/'.$image->getId()); ?>
        <div class="w-full sm:w-1/2 lg:w-2/6 pb-6 text-gray-500 hover:text-gray-900">
            <div class="mx-2 flex flex-col rounded-lg">
                <a class="h-64" href="<?=$this->getUrl(['action' => 'showimage', 'id' => $image->getId()]) ?>">
                    <?php $altText = (empty($image->getImageTitle())) ? basename($image->getImageUrl()) : $image->getImageTitle(); ?>
                    <img src="<?=$this->getUrl().'/'.$image->getImageThumb() ?>" class="w-full h-full object-cover rounded-t-lg" alt="<?=$this->escape($altText) ?>" />
                </a>
                <div class="bg-gray-200 px-3 py-2 rounded-b-lg flex justify-around">
                    <span><i class="fa fa-comment-o"></i> <?=$commentsCount ?></span>
                    <span><i class="fa fa-eye"> <?=$image->getVisits() ?></i></span>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?=$this->get('pagination')->getHtml($this, ['action' => 'show', 'id' => $this->getRequest()->getParam('id')]) ?>
