<?php
interface IArticleDAO
{
    public function save($article);
    public function getArticles($sectionId);
}

