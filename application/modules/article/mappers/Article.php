<?php
/**
 * @copyright Ilch 2.0
 * @package ilch
 */

namespace Modules\Article\Mappers;

use Modules\Article\Models\Article as ArticleModel;

class Article extends \Ilch\Mapper
{
    /**
     * Get articles.
     *
     * @param string $locale
     * @param \Ilch\Pagination|null $pagination
     * @return ArticleModel[]|array
     */
    public function getArticles($locale = '', $pagination = null)
    {
        $select = $this->db()->select()
                ->fields(['p.id', 'p.cat_id', 'p.date_created'])
                ->from(['p' => 'articles'])
                ->join(['pc' => 'articles_content'], 'p.id = pc.article_id', 'LEFT', ['pc.article_id', 'pc.author_id', 'pc.visits', 'pc.content', 'pc.description', 'pc.keywords', 'pc.keywords', 'pc.locale', 'pc.title', 'pc.teaser', 'pc.perma', 'pc.img', 'pc.img_source'])
                ->where(['pc.locale' => $this->db()->escape($locale)])
                ->group(['p.id', 'p.cat_id', 'p.date_created', 'pc.article_id', 'pc.author_id', 'pc.visits', 'pc.content', 'pc.description', 'pc.keywords', 'pc.keywords', 'pc.locale', 'pc.title', 'pc.teaser', 'pc.perma', 'pc.img', 'pc.img_source'])
                ->order(['date_created' => 'DESC']);

        if ($pagination !== null) {
            $select->limit($pagination->getLimit())
                ->useFoundRows();
            $result = $select->execute();
            $pagination->setRows($result->getFoundRows());
        } else {
            $result = $select->execute();
        }

        $articleArray = $result->fetchRows();

        if (empty($articleArray)) {
            return null;
        }

        $articles = [];
        foreach ($articleArray as $articleRow) {
            $articleModel = new ArticleModel();
            $articleModel->setId($articleRow['id']);
            $articleModel->setCatId($articleRow['cat_id']);
            $articleModel->setVisits($articleRow['visits']);
            $articleModel->setAuthorId($articleRow['author_id']);
            $articleModel->setDescription($articleRow['description']);
            $articleModel->setKeywords($articleRow['keywords']);
            $articleModel->setTitle($articleRow['title']);
            $articleModel->setTeaser($articleRow['teaser']);
            $articleModel->setPerma($articleRow['perma']);
            $articleModel->setContent($articleRow['content']);
            $articleModel->setDateCreated($articleRow['date_created']);
            $articleModel->setImage($articleRow['img']);
            $articleModel->setImageSource($articleRow['img_source']);
            $articles[] = $articleModel;
        }

        return $articles;
    }

    /**
     * Get articles by cat id.
     *
     * @param integer $catId
     * @param string $locale
     * @return ArticleModel[]|array
     */
    public function getArticlesByCats($catId, $locale = '', $pagination = null)
    {
        $select = $this->db()->select()
                ->fields(['p.id', 'p.cat_id', 'p.date_created'])
                ->from(['p' => 'articles'])
                ->join(['pc' => 'articles_content'], 'p.id = pc.article_id', 'LEFT', ['pc.visits', 'pc.author_id', 'pc.description', 'pc.keywords', 'pc.title', 'pc.teaser', 'pc.perma', 'pc.content', 'pc.img', 'pc.img_source'])
                ->where(['p.cat_id' => $catId, 'pc.locale' => $this->db()->escape($locale)])
                ->order(['id' => 'DESC']);

        if ($pagination !== null) {
            $select->limit($pagination->getLimit())
                ->useFoundRows();
            $result = $select->execute();
            $pagination->setRows($result->getFoundRows());
        } else {
            $result = $select->execute();
        }
        $articleArray = $result->fetchRows();

        if (empty($articleArray)) {
            return null;
        }

        $articles = [];
        foreach ($articleArray as $articleRow) {
            $articleModel = new ArticleModel();
            $articleModel->setId($articleRow['id']);
            $articleModel->setCatId($articleRow['cat_id']);
            $articleModel->setVisits($articleRow['visits']);
            $articleModel->setAuthorId($articleRow['author_id']);
            $articleModel->setDescription($articleRow['description']);
            $articleModel->setKeywords($articleRow['keywords']);
            $articleModel->setTitle($articleRow['title']);
            $articleModel->setTeaser($articleRow['teaser']);
            $articleModel->setPerma($articleRow['perma']);
            $articleModel->setContent($articleRow['content']);
            $articleModel->setDateCreated($articleRow['date_created']);
            $articleModel->setImage($articleRow['img']);
            $articleModel->setImageSource($articleRow['img_source']);
            $articles[] = $articleModel;
        }

        return $articles;
    }

    /**
     * Get articles of the month of the given date
     *
     * @param \DateTime $date
     * @return ArticleModel[]|array
     */
    public function getArticlesByDate(\DateTime $date)
    {
        $db = $this->db();

        $dateTo = clone $date;
        $dateTo->modify('first day of next month');

        $dateFrom = $date->format($db::FORMAT_DATETIME);
        $dateTo = $dateTo->format($db::FORMAT_DATETIME);

        $select = $this->db()->select()
            ->from(['p' => 'articles'])
            ->join(['pc' => 'articles_content'], 'p.id = pc.article_id', 'LEFT', [])
            ->where(['p.date_created >=' => $dateFrom, 'p.date_created <' => $dateTo])
            ->group(['p.id' => 'DESC', 'p.cat_id', 'p.date_created', 'pc.article_id', 'pc.author_id', 'pc.visits', 'pc.content', 'pc.description', 'pc.keywords', 'pc.locale', 'pc.title', 'pc.teaser', 'pc.perma', 'pc.img', 'pc.img_source']);

        $result = $select->execute();
        $articleArray = $result->fetchRows();

        if (empty($articleArray)) {
            return null;
        }

        $articles = [];
        foreach ($articleArray as $articleRow) {
            $articleModel = new ArticleModel();
            $articleModel->setId($articleRow['id']);
            $articleModel->setCatId($articleRow['cat_id']);
            $articleModel->setVisits($articleRow['visits']);
            $articleModel->setAuthorId($articleRow['author_id']);
            $articleModel->setDescription($articleRow['description']);
            $articleModel->setKeywords($articleRow['keywords']);
            $articleModel->setTitle($articleRow['title']);
            $articleModel->setTeaser($articleRow['teaser']);
            $articleModel->setPerma($articleRow['perma']);
            $articleModel->setContent($articleRow['content']);
            $articleModel->setDateCreated($articleRow['date_created']);
            $articleModel->setImage($articleRow['img']);
            $articleModel->setImageSource($articleRow['img_source']);
            $articles[] = $articleModel;
        }

        return $articles;
    }

    /**
     * Get articles count by cat id
     *
     * @param int $catId
     * @return $count
     */
    public function getCountArticlesByCatId($catId)
    {
        $sql = 'SELECT COUNT(*)
                FROM `[prefix]_articles`
                WHERE `cat_id` LIKE "%'.$catId.'%"';

        $count = $this->db()->queryCell($sql);

        return $count;
    }

    /**
     * Get articles count by month and year
     *
     * @param integer $date
     * @return int
     */
    public function getCountArticlesByMonthYear($date = null)
    {
        $sql = 'SELECT COUNT(*)
                FROM `[prefix]_articles`';

        if ($date != null) {
            $sql .= ' WHERE YEAR(date_created) = YEAR("'.$date.'") AND MONTH(date_created) = MONTH("'.$date.'")';
        }

        $article = $this->db()->queryCell($sql);

        return $article;
    }

    /**
     * Get a list for the archive-box.
     *
     * @param int|null $limit
     * @return []|ArticleModel[]
     * @todo: Remove the group (aggregate) function MAX() workaround, which avoids duplicated entries in the archive-box if possible.
     */
    public function getArticleDateList($limit = null)
    {
        $sql = 'SELECT MAX(`date_created`) AS `date_created`
                FROM `[prefix]_articles`
                GROUP BY YEAR(date_created), MONTH(date_created)
                ORDER BY `date_created` DESC';

        if ($limit !== null) {
            $sql .= ' LIMIT '.(int)$limit;
        }

        $articleArray = $this->db()->queryArray($sql);

        if (empty($articleArray)) {
            return [];
        }

        $articles = [];
        foreach ($articleArray as $articleRow) {
            $articleModel = new ArticleModel();
            $articleModel->setDateCreated($articleRow['date_created']);
            $articles[] = $articleModel;
        }

        return $articles;
    }

    /**
     * Get article lists for overview.
     *
     * @param string $locale
     * @param integer $limit
     * @return ArticleModel[]|null
     */
    public function getArticleList($locale = '', $limit = null)
    {
        $select = $this->db()->select()
                ->fields(['p.id', 'p.cat_id', 'p.date_created'])
                ->from(['p' => 'articles'])
                ->join(['pc' => 'articles_content'], 'p.id = pc.article_id', 'LEFT', ['pc.visits', 'pc.author_id', 'pc.description', 'pc.keywords', 'pc.title', 'pc.teaser', 'pc.perma', 'pc.content', 'pc.img', 'pc.img_source'])
                ->join(['m' => 'media'], 'pc.img = m.url', 'LEFT', ['m.url_thumb', 'm.url'])
                ->where(['pc.locale' => $this->db()->escape($locale)])
                ->group(['p.id', 'p.cat_id', 'p.date_created', 'pc.visits', 'pc.author_id', 'pc.description', 'pc.keywords', 'pc.title', 'pc.teaser', 'pc.perma', 'pc.content', 'pc.img', 'pc.img_source', 'm.url_thumb', 'm.url'])
                ->order(['date_created' => 'DESC']);

        if ($limit !== null) {
            $select->limit($limit);
        }
        $result = $select->execute();
        $articleArray = $result->fetchRows();

        if (empty($articleArray)) {
            return null;
        }

        $articles = [];
        foreach ($articleArray as $articleRow) {
            $articleModel = new ArticleModel();
            $articleModel->setId($articleRow['id']);
            $articleModel->setCatId($articleRow['cat_id']);
            $articleModel->setAuthorId($articleRow['author_id']);
            $articleModel->setVisits($articleRow['visits']);
            $articleModel->setKeywords($articleRow['keywords']);
            $articleModel->setTitle($articleRow['title']);
            $articleModel->setTeaser($articleRow['teaser']);
            $articleModel->setPerma($articleRow['perma']);
            $articleModel->seImage($articleRow['img']);
            $articleModel->setImageThumb($articleRow['url_thumb']);
            $articleModel->setImageSource($articleRow['img_source']);
            $articles[] = $articleModel;
        }

        return $articles;
    }

    /**
     * Returns article model found by the key.
     *
     * @param string $id
     * @param string $locale
     * @return ArticleModel|null
     */
    public function getArticleByIdLocale($id, $locale = '')
    {
        $select = $this->db()->select()
                ->fields(['p.id', 'p.cat_id', 'p.date_created'])
                ->from(['p' => 'articles'])
                ->join(['pc' => 'articles_content'], 'p.id = pc.article_id', 'LEFT', ['pc.visits', 'pc.author_id', 'pc.description', 'pc.keywords', 'pc.title', 'pc.teaser', 'pc.perma', 'pc.content', 'pc.locale', 'pc.img', 'pc.img_source'])
                ->where(['p.id' => $id, 'pc.locale' => $this->db()->escape($locale)]);

        $result = $select->execute();
        $articleRow = $result->fetchAssoc();

        if (empty($articleRow)) {
            return null;
        }

        $articleModel = new ArticleModel();
        $articleModel->setId($articleRow['id']);
        $articleModel->setCatId($articleRow['cat_id']);
        $articleModel->setAuthorId($articleRow['author_id']);
        $articleModel->setVisits($articleRow['visits']);
        $articleModel->setDescription($articleRow['description']);
        $articleModel->setKeywords($articleRow['keywords']);
        $articleModel->setTitle($articleRow['title']);
        $articleModel->setTeaser($articleRow['teaser']);
        $articleModel->setContent($articleRow['content']);
        $articleModel->setLocale($articleRow['locale']);
        $articleModel->setPerma($articleRow['perma']);
        $articleModel->setDateCreated($articleRow['date_created']);
        $articleModel->setImage($articleRow['img']);
        $articleModel->setImageSource($articleRow['img_source']);

        return $articleModel;
    }
    /**
     * Get articles.
     *
     * @param int $limit
     * @return ArticleModel[]|array
     */
    public function getKeywordsList($limit = null)
    {
        $sql = 'SELECT `keywords`
                FROM `[prefix]_articles_content`';

        if ($limit !== null) {
            $sql .= ' LIMIT '.(int)$limit;
        }

        $keywordsArray = $this->db()->queryArray($sql);

        if (empty($keywordsArray)) {
            return [];
        }

        $keywordsList = [];
        foreach ($keywordsArray as $keywords) {
            $articleModel = new ArticleModel();
            $articleModel->setKeywords($keywords['keywords']);
            $keywordsList[] = $articleModel;
        }

        return $keywordsList;
    }

    /**
     * Returns all article permas.
     *
     * @return array|null
     */
    public function getArticlePermas()
    {
        $sql = 'SELECT article_id, locale, perma FROM `[prefix]_articles_content`';
        $permas = $this->db()->queryArray($sql);

        if (empty($permas)) {
            return null;
        }

        $permaArray = [];
        foreach ($permas as $perma) {
            $permaArray[$perma['perma']] = $perma;
        }

        return $permaArray;
    }

    /**
     * Updates visits.
     *
     * @param ArticleModel $article
     */
    public function saveVisits(ArticleModel $article)
    {
        if ($article->getVisits()) {
            $this->db()->update('articles_content')
                    ->values(['visits' => $article->getVisits()])
                    ->where(['article_id' => $article->getId()])
                    ->execute();
        }
    }

    /**
     * Inserts or updates a article.
     *
     * @param ArticleModel $article
     */
    public function save(ArticleModel $article)
    {
        if ($article->getId()) {
            if ($this->getArticleByIdLocale($article->getId(), $article->getLocale())) {
                $this->db()->update('articles')
                    ->values(['cat_id' => $article->getCatId()])
                    ->where(['id' => $article->getId()])
                    ->execute();

                $this->db()->update('articles_content')
                    ->values
                    (
                        [
                            'title' => $article->getTitle(),
                            'teaser' => $article->getTeaser(),
                            'description' => $article->getDescription(),
                            'keywords' => $article->getKeywords(),
                            'content' => $article->getContent(),
                            'perma' => $article->getPerma(),
                            'img' => $article->getImage(),
                            'img_source' => $article->getImageSource()
                        ]
                    )
                    ->where
                    (
                        [
                            'article_id' => $article->getId(), 
                            'locale' => $article->getLocale()
                        ]
                    )
                    ->execute();
            } else {
                $this->db()->insert('articles_content')
                    ->values
                    (
                        [
                            'article_id' => $article->getId(),
                            'author_id' => $article->getAuthorId(),
                            'description' => $article->getDescription(),
                            'keywords' => $article->getKeywords(),
                            'title' => $article->getTitle(),
                            'teaser' => $article->getTeaser(),
                            'content' => $article->getContent(),
                            'perma' => $article->getPerma(),
                            'locale' => $article->getLocale(),
                            'img' => $article->getImage(),
                            'img_source' => $article->getImageSource()
                        ]
                    )
                    ->execute();
            }
        } else {
            $date = new \Ilch\Date();
            $articleId = $this->db()->insert('articles')
                ->values
                (
                    [
                        'cat_id' => $article->getCatId(),
                        'date_created' => $date->toDb()
                    ]
                )
                ->execute();

            $this->db()->insert('articles_content')
                ->values
                (
                    [
                        'article_id' => $articleId,
                        'author_id' => $article->getAuthorId(),
                        'description' => $article->getDescription(),
                        'keywords' => $article->getKeywords(),
                        'title' => $article->getTitle(),
                        'teaser' => $article->getTeaser(),
                        'content' => $article->getContent(),
                        'perma' => $article->getPerma(),
                        'locale' => $article->getLocale(),
                        'img' => $article->getImage(),
                        'img_source' => $article->getImageSource()
                    ]
                )
                ->execute();
        }
    }

    /**
     * Delete an article (with all language contents)
     * 
     * @param int $id
     * @return int
     */
    public function delete($id)
    {
        $deleted = $this->db()->delete('articles')
            ->where(['id' => $id])
            ->execute();
        
        $this->db()->delete('articles_content')
            ->where(['article_id' => $id])
            ->execute();
        
        return $deleted;
    }
}
